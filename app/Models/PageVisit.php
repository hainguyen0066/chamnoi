<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'route_name',
        'article_id',
        'ip_address',
        'device_type',
        'referrer_domain',
        'user_agent',
        'visited_date',
    ];

    protected $casts = [
        'visited_date' => 'date',
        'article_id' => 'integer',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
