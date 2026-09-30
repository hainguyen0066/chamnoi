<?php

namespace App\Services;

use App\Models\GameKickLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service gọi API Kick Người Chơi (Tình Trong Thiên Hạ Mobile v2).
 */
class GameKickService
{
    private string $kickUrl;
    private string $statusUrl;
    private string $key;
    private int $timeout;

    public function __construct()
    {
        $this->kickUrl = (string) (env('GAME_KICK_API_URL') ?: config('services.game_kick.url', 'http://103.206.218.156:8090/v2/kick.php'));
        $this->statusUrl = (string) (env('GAME_KICK_STATUS_API_URL') ?: config('services.game_kick.status_url', 'http://103.206.218.156:8090/v2/kick_status.php'));
        $this->key = (string) (env('KICK_KEY') ?: env('GAME_KICK_KEY') ?: config('services.game_kick.key', ''));
        $this->timeout = (int) (env('GAME_KICK_TIMEOUT') ?: config('services.game_kick.timeout', 30));
    }

    public function getKickUrl(): string
    {
        return $this->kickUrl;
    }

    public function getStatusUrl(): string
    {
        return $this->statusUrl;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Gửi yêu cầu kick người chơi khỏi game.
     *
     * @param string $type 'account' hoặc 'role'
     * @param string $name Tên tài khoản hoặc tên nhân vật (UTF-8 có dấu)
     * @param string $by Tên admin thực hiện
     * @param string|null $reason Lý do kick
     * @param int|null $adminId ID admin trong CSDL
     * @return array{ok: bool, code: string, result: string, msg: string, log: GameKickLog}
     */
    public function kick(string $type, string $name, string $by, ?string $reason = '', ?int $adminId = null): array
    {
        $type = in_array($type, ['account', 'role'], true) ? $type : 'account';
        $name = trim($name);
        // by không được chứa ký tự '|' theo tài liệu
        $by = str_replace('|', '_', trim($by ?: 'admin'));
        $reason = str_replace('|', ' ', trim((string) $reason));
        
        $nonce = bin2hex(random_bytes(8));
        $ts = (string) time();

        // Chuỗi ký: kick|type|name|nonce|by|reason|ts (theo chuẩn HMAC-SHA256 UTF-8)
        $signPayload = "kick|{$type}|{$name}|{$nonce}|{$by}|{$reason}|{$ts}";
        $sign = hash_hmac('sha256', $signPayload, $this->key);

        $postData = [
            'type'   => $type,
            'name'   => $name,
            'by'     => $by,
            'reason' => $reason,
            'nonce'  => $nonce,
            'ts'     => $ts,
            'sign'   => $sign,
            'wait'   => 12,
        ];

        // Tạo bản ghi log sơ bộ
        $kickLog = GameKickLog::create([
            'type'     => $type,
            'name'     => $name,
            'by'       => $by,
            'admin_id' => $adminId,
            'reason'   => $reason ?: null,
            'nonce'    => $nonce,
            'code'     => '0',
            'result'   => 'pending',
            'msg'      => 'Đang gửi lệnh tới GameServer...',
        ]);

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->kickUrl, $postData);

            $data = $response->json();

            if (!is_array($data)) {
                $rawBody = $response->body();
                Log::warning('GameKick API non-json response', ['status' => $response->status(), 'body' => $rawBody]);
                
                $kickLog->update([
                    'code'         => '2',
                    'result'       => 'server_error',
                    'msg'          => 'Máy chủ API phản hồi không hợp lệ: ' . mb_substr($rawBody, 0, 150),
                    'raw_response' => ['raw_body' => $rawBody],
                ]);

                return [
                    'ok'     => false,
                    'code'   => '2',
                    'result' => 'server_error',
                    'msg'    => $kickLog->msg,
                    'log'    => $kickLog->fresh(),
                ];
            }

            $code   = (string) ($data['code'] ?? '2');
            $result = (string) ($data['result'] ?? 'unknown');
            $msg    = (string) ($data['msg'] ?? 'Không có thông báo');

            $kickLog->update([
                'api_id'       => isset($data['id']) ? (int) $data['id'] : null,
                'code'         => $code,
                'result'       => $result,
                'msg'          => $msg,
                'gs_id'        => isset($data['gs_id']) ? (int) $data['gs_id'] : null,
                'role_name'    => $data['role_name'] ?? null,
                'note'         => $data['note'] ?? null,
                'target'       => $data['target'] ?? null,
                'reused'       => !empty($data['reused']),
                'raw_response' => $data,
            ]);

            return [
                'ok'     => $code === '1',
                'code'   => $code,
                'result' => $result,
                'msg'    => $msg,
                'log'    => $kickLog->fresh(),
            ];

        } catch (\Throwable $e) {
            Log::error('GameKick API Exception: ' . $e->getMessage(), [
                'payload' => $postData,
            ]);

            $kickLog->update([
                'code'         => '2',
                'result'       => 'server_error',
                'msg'          => 'Lỗi kết nối máy chủ API: ' . $e->getMessage(),
                'raw_response' => ['error' => $e->getMessage()],
            ]);

            return [
                'ok'     => false,
                'code'   => '2',
                'result' => 'server_error',
                'msg'    => $kickLog->msg,
                'log'    => $kickLog->fresh(),
            ];
        }
    }

    /**
     * Tra cứu lại trạng thái lệnh kick đang pending (code = 0).
     *
     * @param GameKickLog $kickLog
     * @param int $wait Số giây chờ kết quả (0..15)
     * @return array{ok: bool, code: string, result: string, msg: string, log: GameKickLog}
     */
    public function checkStatus(GameKickLog $kickLog, int $wait = 10): array
    {
        if (empty($kickLog->api_id)) {
            return [
                'ok'     => false,
                'code'   => '2',
                'result' => 'bad_request',
                'msg'    => 'Lệnh này không có API ID để tra cứu lại',
                'log'    => $kickLog,
            ];
        }

        $id = (string) $kickLog->api_id;
        $ts = (string) time();

        // sign = HMAC_SHA256(KICK_KEY, "status|" + id + "|" + ts)
        $signPayload = "status|{$id}|{$ts}";
        $sign = hash_hmac('sha256', $signPayload, $this->key);

        $postData = [
            'id'   => $id,
            'ts'   => $ts,
            'sign' => $sign,
            'wait' => min(15, max(0, $wait)),
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->statusUrl, $postData);

            $data = $response->json();

            if (!is_array($data)) {
                return [
                    'ok'     => false,
                    'code'   => '2',
                    'result' => 'server_error',
                    'msg'    => 'Máy chủ trả về kết quả không hợp lệ',
                    'log'    => $kickLog,
                ];
            }

            $code   = (string) ($data['code'] ?? '2');
            $result = (string) ($data['result'] ?? 'unknown');
            $msg    = (string) ($data['msg'] ?? 'Không có thông báo');

            $kickLog->update([
                'code'         => $code,
                'result'       => $result,
                'msg'          => $msg,
                'gs_id'        => isset($data['gs_id']) ? (int) $data['gs_id'] : $kickLog->gs_id,
                'role_name'    => $data['role_name'] ?? $kickLog->role_name,
                'note'         => $data['note'] ?? $kickLog->note,
                'target'       => $data['target'] ?? $kickLog->target,
                'raw_response' => $data,
            ]);

            return [
                'ok'     => $code === '1',
                'code'   => $code,
                'result' => $result,
                'msg'    => $msg,
                'log'    => $kickLog->fresh(),
            ];

        } catch (\Throwable $e) {
            Log::error('GameKick checkStatus API Exception: ' . $e->getMessage());

            return [
                'ok'     => false,
                'code'   => '2',
                'result' => 'server_error',
                'msg'    => 'Lỗi kết nối tra cứu trạng thái: ' . $e->getMessage(),
                'log'    => $kickLog,
            ];
        }
    }
}
