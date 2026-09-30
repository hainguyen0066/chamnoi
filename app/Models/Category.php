<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Danh mục bài viết, hỗ trợ phân cấp cha-con qua parent_id.
 */
#[Fillable(['parent_id', 'name', 'slug', 'description', 'meta_title', 'meta_description', 'sort', 'status', 'show_on_homepage'])]
class Category extends Model
{
    /**
     * Danh mục cha.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Các danh mục con trực tiếp.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Bài viết thuộc danh mục này (nhiều-nhiều qua category_post).
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
