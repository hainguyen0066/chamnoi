<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mã OTP xác thực số điện thoại khi đăng ký.
 * Quy ước: dòng MỚI NHẤT theo phone là dòng có hiệu lực (findLatestByPhone).
 */
class PhoneVerification extends Model
{
    /** OTP có hiệu lực trong 5 phút. */
    public const OTP_EXPIRE_SECONDS = 300;

    /** Khoảng cách tối thiểu giữa 2 lần gửi OTP cho cùng 1 số. */
    public const OTP_RESEND_INTERVAL = 60;

    /** Sau khi verify, số điện thoại dùng được để đăng ký trong 30 phút. */
    public const VERIFIED_VALID_SECONDS = 1800;

    /** Số lần nhập OTP sai tối đa trước khi phải gửi mã mới. */
    public const MAX_ATTEMPTS = 5;

    protected $fillable = [
        'phone',
        'otp',
        'verified',
        'attempts',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
            'attempts' => 'integer',
            'expired_at' => 'integer',
        ];
    }

    public static function findLatestByPhone(string $phone): ?self
    {
        return static::query()->where('phone', $phone)->orderByDesc('id')->first();
    }

    /**
     * Số điện thoại đã verify và còn trong hạn 30 phút để đăng ký.
     */
    public static function isPhoneVerified(string $phone): bool
    {
        $record = static::findLatestByPhone($phone);

        if ($record === null || ! $record->verified) {
            return false;
        }

        return $record->updated_at->getTimestamp() >= (time() - self::VERIFIED_VALID_SECONDS);
    }
}
