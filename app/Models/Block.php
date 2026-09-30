<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['name', 'label', 'content', 'is_active'])]
class Block extends Model
{
    protected $table = 'content_blocks';

    protected $casts = ['is_active' => 'boolean'];

    private const CACHE_PREFIX = 'block.';

    public static function findByName(string $name): ?static
    {
        $key = self::CACHE_PREFIX . $name;
        $cached = Cache::get($key);

        if (is_array($cached)) {
            return static::make($cached);
        }

        $block = static::query()
            ->where('name', $name)
            ->where('is_active', true)
            ->first();

        if ($block) {
            Cache::put($key, $block->toArray(), 3600);
        }

        return $block;
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . $this->name);
    }
}
