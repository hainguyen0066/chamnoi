<?php

namespace App\Jobs;

use App\Models\GameKickLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

    public function handle(\App\Services\GameKickService $kickService): void
    {
        $total = count($this->logIds);
        Log::info("ProcessGameKickBatch: Bắt đầu xử lý {$total} tài khoản trong hàng đợi.");

        foreach ($this->logIds as $index => $logId) {
            $kickLog = GameKickLog::find($logId);
            if (!$kickLog) {
                continue;
            }

            // Bỏ qua nếu lệnh đã có kết quả cuối cùng (thành công hoặc offline/thất bại)
            if ($kickLog->code === '1' || ($kickLog->code === '2' && $kickLog->result !== 'server_error')) {
                continue;
            }

            // Thực thi kick và tự động phân giải trạng thái cuối cùng (không bao giờ để pending)
            $kickService->executeForLog($kickLog);

            // Nếu còn mục tiếp theo, giãn cách 2 giây để không vượt quá 30 req/phút của GameServer
            if ($index < $total - 1) {
                sleep(2);
            }
        }

        Log::info("ProcessGameKickBatch: Đã xử lý xong {$total} tài khoản.");
    }
}
