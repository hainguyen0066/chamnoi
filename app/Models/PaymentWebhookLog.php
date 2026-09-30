<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

/**
 * Log request cổng thanh toán theo cả hai chiều. Chủ yếu do frontend
 * volam-laravel ghi; admin chỉ ghi khi tự tạo link thanh toán.
 */
class PaymentWebhookLog extends Model
{
    protected $table = 'payment_webhook_logs';

    protected $fillable = [
        'gateway', 'direction', 'event', 'transaction_id', 'deposit_id',
        'signature_valid', 'response_code', 'message', 'payload', 'response_body', 'ip',
    ];

    protected $casts = [
        'signature_valid' => 'boolean',
    ];

    public const GATEWAYS = [
        'vnpay'   => 'VNPay',
        'vnptpay' => 'VNPT Pay',
    ];

    public const DIRECTIONS = [
        'incoming' => 'Cổng → mình',
        'outgoing' => 'Mình → cổng',
    ];

    public const EVENTS = [
        'ipn'          => 'IPN',
        'return'       => 'Return URL',
        'payment_url'  => 'Tạo URL thanh toán',
    ];

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(Deposit::class);
    }

    /**
     * Ghi log an toàn: lỗi ghi log không được làm hỏng luồng nghiệp vụ.
     */
    public static function record(array $attributes): void
    {
        try {
            foreach (['payload', 'response_body'] as $field) {
                if (isset($attributes[$field]) && is_array($attributes[$field])) {
                    $attributes[$field] = json_encode($attributes[$field], JSON_UNESCAPED_UNICODE);
                }
            }
            static::query()->create($attributes);
        } catch (\Throwable $e) {
            Log::warning('PaymentWebhookLog: không ghi được log', ['error' => $e->getMessage()]);
        }
    }

    public function payloadArray(): array
    {
        $decoded = json_decode((string) $this->payload, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function gatewayLabel(): string
    {
        return self::GATEWAYS[$this->gateway] ?? $this->gateway;
    }

    public function eventLabel(): string
    {
        return self::EVENTS[$this->event] ?? $this->event;
    }

    public function directionLabel(): string
    {
        return self::DIRECTIONS[$this->direction] ?? (string) $this->direction;
    }

    public function isOutgoing(): bool
    {
        return $this->direction === 'outgoing';
    }
}
