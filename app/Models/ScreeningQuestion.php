<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreeningQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'age_group',
        'question',
        'explanation',
        'category',
        'is_red_flag',
        'points_yes',
        'points_no',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'is_red_flag' => 'boolean',
        'is_active' => 'boolean',
        'points_yes' => 'integer',
        'points_no' => 'integer',
        'order_index' => 'integer',
    ];

    public static function getAgeGroups(): array
    {
        return [
            '12-18m' => 'Trẻ 12 – 18 tháng',
            '18-24m' => 'Trẻ 18 – 24 tháng',
            '2-3y'   => 'Trẻ 2 – 3 tuổi',
            '3-5y'   => 'Trẻ 3 – 5 tuổi',
        ];
    }
}
