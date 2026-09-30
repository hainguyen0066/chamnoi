<?php

namespace App\Services;

use App\Models\KnbExchange;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Xử lý kết quả gọi GameApiService::rechargeKnb() cho 1 giao dịch KnbExchange.
 * Dùng chung bởi KnbExchangeController (gọi lần đầu) và lệnh knb:retry-pending
 * (gọi lại) — KHÔNG được viết lại logic hoàn/không hoàn Point ở 2 nơi khác nhau,
 * đây là phần quyết định tiền có bị cộng trùng hay mất hay không.
 */
class KnbExchangeSettlementService
{
    /**
     * Mã lỗi CHẮC CHẮN không thể thành công dù gọi lại cùng txnid bao nhiêu lần
     * — an toàn để hoàn Point ngay. Các mã còn lại (-1,-2,-3,-5, exception, JSON
     * lỗi...) là mơ hồ/có thể do lỗi mạng tạm thời: cuộc gọi có thể đã cộng KNB
     * thành công phía server game dù client nhận lỗi, nên KHÔNG được hoàn Point
     * ngay — hoàn nhầm rồi retry thành công sau sẽ cộng KNB free cho người chơi.
     */
    private const NON_RETRYABLE_CODES = ['-4', '-6', '-7'];

    /**
     * @param  array{ok: bool, message: string, code: string, exists: bool, duplicate: bool}  $result
     * @return array{success: bool, message: string, knb?: int}
     */
    public function settle(KnbExchange $exchange, array $result): array
    {
        $exchange->game_response = $result;

        // ok=true bao gồm cả trường hợp duplicate=true (đã xử lý từ trước) — vẫn coi là thành công.
        if ($result['ok']) {
            $exchange->status = 'completed';
            $exchange->save();

            return ['success' => true, 'message' => 'Đổi KNB thành công.', 'knb' => $exchange->knb_amount];
        }

        if (in_array($result['code'], self::NON_RETRYABLE_CODES, true)) {
            DB::transaction(function () use ($exchange) {
                // Chỉ hoàn Point nếu CHÍNH lần gọi này chuyển được pending → failed.
                // Web, lệnh knb:retry-pending và nút "Thử lại" trong admin có thể
                // settle cùng 1 giao dịch đồng thời — không được hoàn 2 lần.
                $claimed = KnbExchange::query()
                    ->whereKey($exchange->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'failed', 'game_response' => json_encode($exchange->game_response), 'updated_at' => now()]);

                if ($claimed === 1) {
                    User::query()->whereKey($exchange->user_id)->increment('xu_balance', $exchange->point_amount);
                }

                $exchange->status = 'failed';
                $exchange->syncOriginal();
            });

            return [
                'success' => false,
                'message' => 'Đổi KNB thất bại: ' . $result['message'] . ' Point đã được hoàn lại.',
            ];
        }

        $exchange->status = 'pending';
        $exchange->save();

        Log::critical("KnbExchange #{$exchange->id} txnid={$exchange->txnid}: kết quả không rõ ràng, giữ pending chờ retry — {$result['message']}");

        return [
            'success' => false,
            'message' => 'Yêu cầu đang được xử lý, Point của bạn đang được giữ. Nếu sau ít phút KNB chưa về, vui lòng liên hệ hỗ trợ.',
        ];
    }
}
