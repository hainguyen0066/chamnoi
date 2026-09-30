<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameKickLog extends Model
{
    protected $fillable = [
        'type',
        'name',
        'by',
        'admin_id',
        'reason',
        'nonce',
        'api_id',
        'code',
        'result',
        'msg',
        'gs_id',
        'role_name',
        'note',
        'target',
        'reused',
        'raw_response',
    ];

    protected $casts = [
        'reused'       => 'boolean',
        'raw_response' => 'array',
        'gs_id'        => 'integer',
        'api_id'       => 'integer',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function isSuccess(): bool
    {
        return $this->code === '1';
    }

    public function isPending(): bool
    {
        return $this->code === '0';
    }

    public function isFailed(): bool
    {
        return $this->code === '2' || empty($this->code);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->code) {
            '1' => 'Đã kick',
            '0' => 'Đang xử lý',
            '2' => 'Không thành công',
            default => 'Chưa rõ',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->code) {
            '1' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            '0' => 'bg-amber-100 text-amber-800 border-amber-200',
            '2' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match ($this->type) {
            'account' => 'Tài khoản',
            'role'    => 'Tên nhân vật',
            default   => $this->type,
        };
    }
}
