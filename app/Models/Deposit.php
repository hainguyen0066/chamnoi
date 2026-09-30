<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Giao dịch nạp tiền/xu — bảng hợp nhất cho cả nạp tay (admin) lẫn nạp qua
 * cổng thanh toán (VNPay/VNPT Pay). Danh sách deposits chính là log audit.
 *
 * QUY TẮC AN TOÀN: amount_received không bao giờ nhận từ client — luôn tính
 * bằng calculateAmountReceived() phía server; cộng số dư qua markCompleted()
 * trong DB transaction để log và xu_balance không bao giờ lệch nhau.
 */
#[Fillable([
    'user_id', 'account', 'type', 'method', 'amount',
    'promotion_percent', 'amount_received', 'note',
    'status', 'source', 'processed_by',
    'gateway_transaction_id', 'gateway_response',
])]
class Deposit extends Model
{
    /** Đơn giá quy đổi: 1 xu = 1000 VND (trước khuyến mãi). */
    public const VND_PER_XU = 1000;

    /** Các mức khuyến mãi hợp lệ (%). */
    public const PROMOTION_PERCENTS = [0, 5, 10, 20];

    /** Nhãn hiển thị các phương thức nạp. */
    public const METHODS = [
        'momo' => 'Momo',
        'bank_transfer' => 'Ngân hàng',
        'ctv' => 'CTV',
        'vnpay' => 'VNPay',
        'vnptpay' => 'VNPT Pay',
    ];

    /** Nhãn hiển thị loại hình nạp. */
    public const TYPES = [
        'xu' => 'Nạp KPoint',
        'gold' => 'Nạp vàng',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'promotion_percent' => 'integer',
            'amount_received' => 'integer',
            'gateway_response' => 'array',
        ];
    }

    /**
     * User được nạp.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin đã thao tác (với nạp tay).
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Công thức quy đổi xu, khớp nghiệp vụ hệ thống cũ:
     * xu = floor( số tiền * (1 + khuyến mãi%) / 1000 )
     */
    public static function calculateAmountReceived(int $amount, int $promotionPercent): int
    {
        return (int) floor($amount * (1 + $promotionPercent / 100) / self::VND_PER_XU);
    }

    /**
     * Đánh dấu giao dịch hoàn tất và cộng xu cho user trong cùng 1 DB transaction.
     * Idempotent: giao dịch đã completed thì gọi lại không cộng trùng.
     *
     * @param array|null $gatewayResponse payload callback từ cổng thanh toán (nếu có)
     */
    public function markCompleted(?array $gatewayResponse = null): void
    {
        if ($this->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($gatewayResponse) {
            $this->status = 'completed';

            if ($gatewayResponse !== null) {
                $this->gateway_response = $gatewayResponse;
            }

            $this->save();

            if ($this->user_id !== null) {
                // increment nguyên tử ở tầng SQL, tránh race khi 2 giao dịch cùng lúc.
                User::query()->whereKey($this->user_id)->increment('xu_balance', $this->amount_received);
            }
        });
    }

    /**
     * Đánh dấu giao dịch thất bại, lưu payload callback để đối soát.
     */
    public function markFailed(?array $gatewayResponse = null): void
    {
        if ($this->status === 'completed') {
            return; // không hạ cấp giao dịch đã hoàn tất
        }

        $this->status = 'failed';

        if ($gatewayResponse !== null) {
            $this->gateway_response = $gatewayResponse;
        }

        $this->save();
    }
}
