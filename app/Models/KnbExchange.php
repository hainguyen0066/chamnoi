<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Giao dịch đổi K Point sang KNB trong game. txnid là khoá idempotent gửi
 * thẳng sang recharge.php — KHÔNG được sinh lại khi retry cùng 1 giao dịch
 * (xem GameApiService::rechargeKnb và KnbExchangeSettlementService).
 */
#[Fillable(['user_id', 'username', 'point_amount', 'knb_amount', 'txnid', 'status', 'game_response'])]
class KnbExchange extends Model
{
    /** Tỉ giá quy đổi: 10 point = 1 KNB. */
    public const POINT_PER_KNB = 10;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'point_amount' => 'integer',
            'knb_amount' => 'integer',
            'game_response' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
