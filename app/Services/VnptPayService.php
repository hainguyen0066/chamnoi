<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Tích hợp VNPT Money (VNPT Pay) theo đặc tả v1.0.6 — cùng chuẩn checksum
 * với hệ thống cũ đã chạy thật: SHA-256 trên các trường nối bằng '|',
 * SECRET_KEY luôn đứng cuối.
 *
 * Cấu hình .env: VNPTPAY_MERCHANT_SERVICE_ID, VNPTPAY_SECRET_KEY,
 * VNPTPAY_API_KEY (JWT, gửi qua Authorization: Bearer), VNPTPAY_BASE_URL.
 */
class VnptPayService
{
    private string $baseUrl;
    private string $merchantServiceId;
    private string $apiKey;
    private string $secretKey;

    public function __construct()
    {
        $config = config('services.vnptpay');

        $this->baseUrl = rtrim((string) $config['base_url'], '/');
        $this->merchantServiceId = (string) $config['merchant_service_id'];
        $this->apiKey = (string) $config['api_key'];
        $this->secretKey = (string) $config['secret_key'];
    }

    /**
     * Đã đủ cấu hình để gọi VNPT Pay chưa.
     */
    public function isConfigured(): bool
    {
        return $this->merchantServiceId !== '' && $this->secretKey !== '' && $this->apiKey !== '';
    }

    /**
     * Tạo giao dịch thanh toán QR.
     *
     * @param string $orderId mã giao dịch phía mình (deposits.gateway_transaction_id)
     * @param int $amount số tiền VND
     * @param string $description mô tả đơn hàng
     * @param string $clientIp IP người thanh toán
     * @return array ['success' => bool, 'code' => string, 'message' => string, 'data' => array]
     * @throws \RuntimeException khi chưa cấu hình hoặc lỗi kết nối
     */
    public function createQr(string $orderId, int $amount, string $description, string $clientIp): array
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Chưa cấu hình VNPT Pay trong .env (VNPTPAY_*).');
        }

        $data = [
            'ACTION' => 'PAY',
            'VERSION' => '1.0.6',
            'MERCHANT_SERVICE_ID' => $this->merchantServiceId,
            'MERCHANT_ORDER_ID' => $orderId,
            'AMOUNT' => $amount,
            'PAYMENT_ACTION' => 'PAY',
            'SERVICE_CATEGORY' => '57',
            'CHANNEL_ID' => '1',
            'DEVICE' => '1',
            'LOCALE' => 'vi-VN',
            'CURRENCY_CODE' => 'VND',
            'PAYMENT_METHOD' => 'VNPTPAY',
            'DESCRIPTION' => mb_substr($description, 0, 255),
            'MERCHANT_DATA' => '',
            'CREATE_DATE' => now()->format('YmdHis'),
            'CLIENT_IP' => $clientIp,
        ];

        // Thứ tự trường checksum theo đặc tả tạo QR của VNPT Money.
        $data['SECURE_CODE'] = $this->sha256Pipe([
            $data['ACTION'], $data['VERSION'], $data['MERCHANT_SERVICE_ID'],
            $data['MERCHANT_ORDER_ID'], $data['AMOUNT'], $data['PAYMENT_ACTION'],
            $data['SERVICE_CATEGORY'], $data['CHANNEL_ID'], $data['DEVICE'],
            $data['LOCALE'], $data['CURRENCY_CODE'], $data['PAYMENT_METHOD'],
            $data['DESCRIPTION'], $data['CREATE_DATE'], $data['CLIENT_IP'],
        ]);

        return $this->request('/pay_qrcode', $data);
    }

    /**
     * Truy vấn trạng thái giao dịch đã tạo (đối soát chủ động).
     *
     * @param string $orderId mã giao dịch phía mình
     * @param string $createDate thời điểm tạo giao dịch gốc (YmdHis)
     */
    public function query(string $orderId, string $createDate): array
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Chưa cấu hình VNPT Pay trong .env (VNPTPAY_*).');
        }

        $data = [
            'ACTION' => 'QUERY',
            'VERSION' => '1.0.6',
            'MERCHANT_SERVICE_ID' => $this->merchantServiceId,
            'MERCHANT_ORDER_ID' => $orderId,
            'CREATE_DATE' => $createDate,
        ];

        $data['SECURE_CODE'] = $this->sha256Pipe([
            $data['ACTION'], $data['VERSION'], $data['MERCHANT_SERVICE_ID'],
            $data['MERCHANT_ORDER_ID'], $data['CREATE_DATE'],
        ]);

        return $this->request('/query', $data);
    }

    /**
     * Xác thực checksum bản tin IPN VNPT Pay gửi về.
     *
     * @param array $post toàn bộ body IPN
     * @return bool true nếu chữ ký hợp lệ
     */
    public function verifyIpn(array $post): bool
    {
        if (empty($post['SECURE_CODE'])) {
            return false;
        }

        $receivedCode = (string) $post['SECURE_CODE'];

        // Thứ tự trường checksum IPN — khớp với hệ thống cũ đang chạy.
        $expected = $this->sha256Pipe([
            $post['ACTION'] ?? '',
            $post['RESPONSE_CODE'] ?? '',
            $post['MERCHANT_SERVICE_ID'] ?? '',
            $post['MERCHANT_ORDER_ID'] ?? '',
            $post['AMOUNT'] ?? '',
            $post['CURRENCY_CODE'] ?? '',
            $post['VNPTPAY_TRANSACTION_ID'] ?? '',
            $post['PAYMENT_METHOD'] ?? '',
            $post['PAY_DATE'] ?? '',
            $post['ADDITIONAL_INFO'] ?? '',
        ]);

        return hash_equals(strtolower($expected), strtolower($receivedCode));
    }

    /**
     * Giao dịch VNPT Pay thành công khi RESPONSE_CODE = '00'.
     */
    public function isSuccessResponse(array $post): bool
    {
        return ($post['RESPONSE_CODE'] ?? null) === '00';
    }

    /**
     * Dựng bản tin trả lời IPN cho VNPT Pay (họ yêu cầu response có chữ ký).
     */
    public function buildIpnResponse(string $code, string $description, string $orderId): array
    {
        $response = [
            'RESPONSE_CODE' => $code,
            'DESCRIPTION' => $description,
            'MERCHANT_SERVICE_ID' => $this->merchantServiceId,
            'MERCHANT_ORDER_ID' => $orderId,
            'CREATE_DATE' => now()->format('YmdHis'),
        ];

        $response['SECURE_CODE'] = $this->sha256Pipe([
            $response['RESPONSE_CODE'], $response['DESCRIPTION'],
            $response['MERCHANT_SERVICE_ID'], $response['MERCHANT_ORDER_ID'],
            $response['CREATE_DATE'],
        ]);

        return $response;
    }

    /**
     * Gửi request JSON tới VNPT Pay và chuẩn hoá kết quả.
     */
    private function request(string $api, array $payload): array
    {
        $url = $this->baseUrl . $api;

        $response = Http::withToken($this->apiKey)
            ->timeout((int) config('services.vnptpay.timeout', 30))
            // Sandbox VNPT dev dùng chứng chỉ tự ký — chỉ tắt verify khi cấu hình nói rõ.
            ->withOptions(['verify' => (bool) config('services.vnptpay.verify_ssl', true)])
            ->post($url, $payload);

        $body = $response->json();

        Log::info('VNPT Pay request', ['url' => $url, 'request' => $payload, 'response' => $body]);

        if (! is_array($body) || ! isset($body['RESPONSE_CODE'])) {
            throw new \RuntimeException('VNPT Pay trả về dữ liệu không hợp lệ (HTTP ' . $response->status() . ').');
        }

        return [
            'success' => $body['RESPONSE_CODE'] === '00',
            'code' => $body['RESPONSE_CODE'],
            'message' => $body['DESCRIPTION'] ?? '',
            'data' => $body,
        ];
    }

    /**
     * SHA-256 trên các trường nối bằng '|', SECRET_KEY đứng cuối — đặc tả VNPT Money.
     */
    private function sha256Pipe(array $parts): string
    {
        $parts[] = $this->secretKey;

        return hash('sha256', implode('|', $parts));
    }
}
