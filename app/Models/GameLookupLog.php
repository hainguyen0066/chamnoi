<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameLookupLog extends Model
{
    protected $fillable = [
        'by',
        'query_name',
        'admin_name',
        'admin_id',
        'code',
        'result',
        'account',
        'roles',
        'msg',
        'raw_response',
    ];

    protected $casts = [
        'roles'        => 'array',
        'raw_response' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function isSuccess(): bool
    {
        return $this->code === '1';
    }

    public function isNotFound(): bool
    {
        return $this->result === 'not_found';
    }

    public function getByTextAttribute(): string
    {
        return match ($this->by) {
            'role'    => 'Tên nhân vật',
            'account' => 'Tài khoản',
            default   => $this->by,
        };
    }

    public function getResultBadgeAttribute(): string
    {
        return match ($this->result) {
            'ok'          => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'not_found'   => 'bg-amber-100 text-amber-800 border-amber-200',
            'bad_sign'    => 'bg-purple-100 text-purple-800 border-purple-200',
            'bad_request' => 'bg-orange-100 text-orange-800 border-orange-200',
            'forbidden'   => 'bg-red-100 text-red-800 border-red-200',
            default       => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
