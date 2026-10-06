<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, string $label = '', string $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'label' => $label ?: $key, 'group' => $group]
        );
    }
}
