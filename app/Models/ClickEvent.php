<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickEvent extends Model
{
    protected $fillable = [
        'event_name',
        'event_label',
        'page_url',
        'ip_address',
        'event_date',
    ];
}
