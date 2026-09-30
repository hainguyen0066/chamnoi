<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Token Zalo OA — mỗi app_id đúng 1 dòng, upsert tại chỗ.
 * expired_at là unix timestamp (so sánh với time()).
 */
class ZaloOaToken extends Model
{
    protected $fillable = [
        'app_id',
        'access_token',
        'refresh_token',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'expired_at' => 'integer',
        ];
    }

    public static function findByAppId(string $appId): ?self
    {
        return static::query()->where('app_id', $appId)->first();
    }
}
