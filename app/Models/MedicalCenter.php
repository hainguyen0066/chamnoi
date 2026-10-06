<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'address',
        'phone',
        'website',
        'specialty',
        'description',
        'booking_tip',
        'is_verified',
        'order_index',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'order_index' => 'integer',
    ];

    public static function getCities(): array
    {
        return ['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng', 'Bình Dương'];
    }
}
