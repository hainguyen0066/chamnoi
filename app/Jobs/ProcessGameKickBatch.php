<?php

namespace App\Jobs;

use App\Models\GameKickLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Job xử lý danh sách kick nhiều tài khoản/nhân vật qua Queue.
 * Tự động giãn cách 2 giây giữa các yêu cầu để tuân thủ giới hạn 30 req/phút của GameServer.
 */
class ProcessGameKickBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Thời gian timeout cho toàn bộ job (ví dụ danh sách 30 acc * 4s = 120s).
     */
    public int $timeout = 600;

    /**
     * @param array<int> $logIds Danh sách ID của GameKickLog cần xử lý
     */
    public function __construct(
        public array $logIds
    ) {}

    public function handle(): void
    {
        $kickUrl = (string) (env('GAME_KICK_API_URL') ?: config('services.game_kick.url', 'http://103.206.216.8:8090/v2/kick.php'));
        $key = (string) (env('KICK_KEY') ?: env('GAME_KICK_KEY') ?: config('services.game_kick.key', ''));
        $httpTimeout = (int) (env('GAME_KICK_TIMEOUT') ?: config('services.game_kick.timeout', 30));

        $total = count($this->logIds);
        Log::info("ProcessGameKickBatch: Bắt đầu xử lý {$total} tài khoản trong hàng đợi.");

        foreach ($this->logIds as $index => $logId) {
            $kickLog = GameKickLog::find($logId);
            if (!$kickLog) {
                continue;
            }

            // Bỏ qua nếu lệnh đã có kết quả cuối cùng
            if ($kickLog->code === '1' || ($kickLog->code === '2' && $kickLog->result !== 'server_error')) {
                continue;
            }

            $type = in_array($kickLog->type, ['account', 'role'], true) ? $kickLog->type : 'account';
            $name = trim($kickLog->name);
            $by = str_replace('|', '_', trim($kickLog->by ?: 'admin'));
            $reason = str_replace('|', ' ', trim((string) $kickLog->reason));

            $nonce = bin2hex(random_bytes(8));
            $ts = (string) time();

            // Ký HMAC-SHA256: kick|type|name|nonce|by|reason|ts
            $signPayload = "kick|{$type}|{$name}|{$nonce}|{$by}|{$reason}|{$ts}";
            $sign = hash_hmac('sha256', $signPayload, $key);

            $postData = [
                'type'   => $type,
                'name'   => $name,
                'by'     => $by,
                'reason' => $reason,
                'nonce'  => $nonce,
                'ts'     => $ts,
                'sign'   => $sign,
            ];

            try {
                $response = Http::timeout($httpTimeout)
                    ->asForm()
                    ->post($kickUrl, $postData);

                $data = $response->json();

                if (is_array($data)) {
                    $code   = (string) ($data['code'] ?? '2');
                    $result = (string) ($data['result'] ?? 'unknown');
                    $msg    = (string) ($data['msg'] ?? 'Không có phản hồi');

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
                } else {
                    $rawBody = $response->body();
                    $kickLog->update([
                        'code'         => '2',
                        'result'       => 'server_error',
                        'msg'          => 'Phản hồi không hợp lệ từ máy chủ: ' . mb_substr($rawBody, 0, 100),
                        'raw_response' => ['raw_body' => $rawBody],
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("ProcessGameKickBatch error on {$name}: " . $e->getMessage());
                $kickLog->update([
                    'code'         => '2',
                    'result'       => 'server_error',
                    'msg'          => 'Lỗi kết nối API: ' . $e->getMessage(),
                    'raw_response' => ['error' => $e->getMessage()],
                ]);
            }

            // Nếu còn mục tiếp theo, giãn cách 2 giây để không vượt quá 30 req/phút
            if ($index < $total - 1) {
                sleep(2);
            }
        }

        Log::info("ProcessGameKickBatch: Đã xử lý xong {$total} tài khoản.");
    }
}
