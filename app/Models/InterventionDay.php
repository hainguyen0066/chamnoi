<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_number',
        'week_number',
        'title',
        'goal',
        'activity_name',
        'instructions',
        'target_words',
        'parent_tip',
        'icon',
        'duration_minutes',
    ];

    public static function getWeeks(): array
    {
        return [
            1 => 'Tuần 1: Cai thiết bị số & Kích hoạt giao tiếp mắt',
            2 => 'Tuần 2: Âm thanh tượng thanh & Bắt chước cử chỉ',
            3 => 'Tuần 3: Vốn từ nhu cầu & Chỉ tay chủ động',
            4 => 'Tuần 4: Ghép từ đôi & Mở rộng ngữ cảnh',
        ];
    }
}
