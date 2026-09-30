<?php

namespace App\Services;

use App\Models\ZaloOaToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Gửi OTP thật qua Zalo Notification Service (ZNS) của Zalo OA.
 *
 * Luồng hoạt động:
 *  1. Bootstrap 1 lần: admin truy cập /zalo-authorize?key=<setup_key> để đăng
 *     nhập & cấp quyền cho app trên Zalo. Zalo redirect về /zalo-oauth-callback
 *     kèm "code", code được đổi lấy access_token + refresh_token đầu tiên.
 *  2. Mỗi lần gửi OTP: lấy access_token còn hiệu lực từ bảng zalo_oa_tokens,
 *     tự động refresh bằng refresh_token nếu sắp hết hạn (getValidToken()).
 *  3. Gọi API ZNS gửi tin theo template_id đã được Zalo duyệt.
 *
 * Lưu ý đặc tả Zalo v4:
 *  - secret_key đi trong HTTP HEADER (không phải body) cho cả 2 grant OAuth.
 *  - API ZNS dùng header "access_token: <token>" (không phải Bearer).
 *  - Thành công khi response có error === 0.
 *  - refresh_token XOAY VÒNG sau mỗi lần refresh — phải lưu lại cả 2 token.
 *
 * Tài liệu: https://developers.zalo.me/docs/zalo-notification-service
 */
class ZaloOAService
{
    /** Refresh chủ động trước khi access_token thật sự hết hạn (giây). */
    private const REFRESH_MARGIN_SECONDS = 300;

    private const OAUTH_TOKEN_URL = 'https://oauth.zaloapp.com/v4/oa/access_token';
    private const OAUTH_PERMISSION_URL = 'https://oauth.zaloapp.com/v4/oa/permission';
    private const ZNS_SEND_URL = 'https://business.openapi.zalo.me/message/template';

    private string $appId;
    private string $appSecret;
    private string $templateIdOtp;
    private string $redirectUri;
    private int $timeout;
    private bool $debug;

    public function __construct()
    {
        $config = config('services.zalo_oa');

        $this->appId = (string) $config['app_id'];
        $this->appSecret = (string) $config['app_secret'];
        $this->templateIdOtp = (string) $config['template_id_otp'];
        $this->redirectUri = (string) $config['redirect_uri'];
        $this->timeout = (int) ($config['timeout'] ?? 30);
        $this->debug = (bool) ($config['debug'] ?? false);
    }

    public function isConfigured(): bool
    {
        return $this->appId !== '' && $this->appSecret !== '' && $this->templateIdOtp !== '';
    }

    /**
     * Gửi mã OTP tới số điện thoại qua Zalo ZNS.
     */
    public function sendOTP(string $phone, string $otp): bool
    {
        $token = $this->getValidToken();

        $payload = [
            'phone' => $this->normalizePhone($phone),
            'template_id' => $this->templateIdOtp,
            'template_data' => [
                'otp' => $otp,
            ],
            'tracking_id' => uniqid('otp_', true),
        ];

        $response = $this->postJson(self::ZNS_SEND_URL, $payload, [
            'access_token: ' . $token->access_token,
        ]);

        $this->writeLog(self::ZNS_SEND_URL, $payload, $response);

        if (! isset($response['error']) || (int) $response['error'] !== 0) {
            Log::error('Gửi OTP qua Zalo ZNS thất bại', ['response' => $response]);

            return false;
        }

        return true;
    }

    /**
     * URL để admin đăng nhập & cấp quyền app trên Zalo (bootstrap 1 lần).
     */
    public function getAuthorizeUrl(string $state): string
    {
        return self::OAUTH_PERMISSION_URL . '?' . http_build_query([
            'app_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
        ]);
    }

    /**
     * Đổi "code" lấy access_token/refresh_token đầu tiên, lưu vào DB.
     */
    public function exchangeCodeForToken(string $code): ZaloOaToken
    {
        $response = $this->postForm(self::OAUTH_TOKEN_URL, [
            'code' => $code,
            'app_id' => $this->appId,
            'grant_type' => 'authorization_code',
        ], [
            'secret_key: ' . $this->appSecret,
        ]);

        $this->writeLog(self::OAUTH_TOKEN_URL, ['grant_type' => 'authorization_code'], $this->maskToken($response));

        return $this->saveToken($response);
    }

    /**
     * Lấy access_token còn hiệu lực, tự động refresh nếu sắp hết hạn.
     *
     * @throws \RuntimeException khi chưa bootstrap token lần đầu
     */
    private function getValidToken(): ZaloOaToken
    {
        $token = ZaloOaToken::findByAppId($this->appId);

        if ($token === null) {
            throw new \RuntimeException(
                'Chưa có access_token Zalo OA. Truy cập /zalo-authorize?key=<setup_key> để authorize trước.'
            );
        }

        if ($token->expired_at - self::REFRESH_MARGIN_SECONDS <= time()) {
            // Lock chống race: 2 request gửi OTP đồng thời cùng refresh sẽ làm
            // refresh_token xoay vòng đè nhau và mất hiệu lực.
            $token = Cache::lock('zalo_oa_refresh', 15)->block(10, function () {
                $fresh = ZaloOaToken::findByAppId($this->appId);

                if ($fresh->expired_at - self::REFRESH_MARGIN_SECONDS > time()) {
                    return $fresh; // request khác vừa refresh xong
                }

                return $this->refreshAccessToken($fresh);
            });
        }

        return $token;
    }

    private function refreshAccessToken(ZaloOaToken $token): ZaloOaToken
    {
        $response = $this->postForm(self::OAUTH_TOKEN_URL, [
            'refresh_token' => $token->refresh_token,
            'app_id' => $this->appId,
            'grant_type' => 'refresh_token',
        ], [
            'secret_key: ' . $this->appSecret,
        ]);

        $this->writeLog(self::OAUTH_TOKEN_URL, ['grant_type' => 'refresh_token'], $this->maskToken($response));

        return $this->saveToken($response);
    }

    private function saveToken(array $response): ZaloOaToken
    {
        if (empty($response['access_token']) || empty($response['refresh_token'])) {
            throw new \RuntimeException(
                'Phản hồi lấy token từ Zalo không hợp lệ: ' . json_encode($this->maskToken($response))
            );
        }

        $expiresIn = isset($response['expires_in']) ? (int) $response['expires_in'] : 3600;

        return ZaloOaToken::query()->updateOrCreate(
            ['app_id' => $this->appId],
            [
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'],
                'expired_at' => time() + $expiresIn,
            ]
        );
    }

    /**
     * Chuẩn hoá số nội địa (0xxxxxxxxx) sang 84xxxxxxxxx (không dấu +).
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '84' . substr($phone, 1);
        }

        return $phone;
    }

    /** POST application/x-www-form-urlencoded (API OAuth của Zalo). */
    private function postForm(string $url, array $payload, array $extraHeaders = []): array
    {
        return $this->curl($url, http_build_query($payload), array_merge([
            'Content-Type: application/x-www-form-urlencoded',
        ], $extraHeaders));
    }

    /** POST application/json (API gửi ZNS của Zalo). */
    private function postJson(string $url, array $payload, array $extraHeaders = []): array
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        return $this->curl($url, $json, array_merge([
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json),
        ], $extraHeaders));
    }

    private function curl(string $url, string $body, array $headers): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \RuntimeException('Lỗi kết nối tới Zalo API: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true);

        if (! is_array($decoded)) {
            throw new \RuntimeException("Zalo API trả về dữ liệu không hợp lệ (HTTP {$httpCode}): " . $response);
        }

        return $decoded;
    }

    /** Ẩn bớt token trước khi ghi log. */
    private function maskToken(array $response): array
    {
        foreach (['access_token', 'refresh_token'] as $field) {
            if (isset($response[$field])) {
                $response[$field] = substr($response[$field], 0, 6) . '***';
            }
        }

        return $response;
    }

    private function writeLog(string $url, array $request, array $response): void
    {
        if (! $this->debug) {
            return;
        }

        unset($request['template_data']); // không log mã OTP

        Log::info('zalo_oa', [
            'url' => $url,
            'request' => $request,
            'response' => $response,
        ]);
    }
}
