<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Slider/banner hiển thị ở nhiều vị trí (zone) khác nhau trên frontend.
 */
#[Fillable(['zone', 'title', 'image', 'mobile_image', 'url', 'description', 'sort', 'status'])]
class Slider extends Model
{
    /**
     * Danh sách vị trí hợp lệ trên frontend => nhãn hiển thị trong admin.
     * Thêm vị trí mới: chỉ cần thêm 1 dòng ở đây, form admin tự cập nhật.
     */
    public const ZONES = [
        'home_hero' => 'Trang chủ - Banner chính',
        'home_news' => 'Trang chủ - Banner cạnh bảng tin',
        'sidebar' => 'Cột bên (sidebar)',
        'category_top' => 'Đầu trang danh mục',
        'footer' => 'Cuối trang (footer)',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /** URL hiển thị ảnh chính: CDN URL trực tiếp hoặc local storage. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image
            ? (str_starts_with($this->image, 'http') ? $this->image : Storage::disk('public')->url($this->image))
            : null
        );
    }

    /** URL hiển thị ảnh mobile: CDN URL trực tiếp hoặc local storage. */
    protected function mobileImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->mobile_image
            ? (str_starts_with($this->mobile_image, 'http') ? $this->mobile_image : Storage::disk('public')->url($this->mobile_image))
            : null
        );
    }

    /**
     * Scope: slider đang bật của một vị trí, đúng thứ tự hiển thị.
     * Frontend gọi: Slider::forZone('home_hero')->get()
     */
    public function scopeForZone(Builder $query, string $zone): Builder
    {
        return $query
            ->where('zone', $zone)
            ->where('status', true)
            ->orderBy('sort');
    }
}

