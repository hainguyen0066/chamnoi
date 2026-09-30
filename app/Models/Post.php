<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Bài viết tin tức. Một bài thuộc nhiều danh mục (pivot category_post).
 * author_id không nằm trong Fillable — luôn gán từ auth()->id() trong controller.
 */
#[Fillable([
    'title', 'slug', 'excerpt', 'content', 'thumbnail',
    'status', 'is_featured', 'published_at',
    'meta_title', 'meta_description', 'meta_keywords',
])]
class Post extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Tác giả bài viết.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Các danh mục của bài viết (nhiều-nhiều qua category_post).
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Scope: chỉ lấy bài đã đăng và đến giờ đăng (dùng cho frontend sau này).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }
}
