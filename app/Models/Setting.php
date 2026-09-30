<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Bảng cấu hình dạng key-value, thay cho 3 thiết kế settings rời rạc của
 * hệ thống Yii2 cũ. Đọc qua Setting::get()/Setting::set() để luôn có cache.
 */
#[Fillable(['key', 'value', 'type', 'group'])]
class Setting extends Model
{
    /** Cache key lưu toàn bộ settings dưới dạng mảng key => value. */
    const CACHE_KEY = 'settings.all';

    /**
     * Lấy giá trị 1 setting theo key, có cache để tránh query lại mỗi request.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever(self::CACHE_KEY, function () {
            return self::query()->pluck('value', 'key')->all();
        });

        return $all[$key] ?? $default;
    }

    /**
     * Tạo/ghi đè giá trị 1 setting và xoá cache để lần đọc sau lấy giá trị mới.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget(self::CACHE_KEY);
    }
}
