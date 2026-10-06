<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'category',
        'age_group',
        'reading_time',
        'excerpt',
        'content',
        'icon',
        'badge_text',
        'badge_color',
        'is_featured',
        'is_published',
        'order_index',
        'views_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'order_index' => 'integer',
        'views_count' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    public static function getCategoryLabels(): array
    {
        return [
            'nguyen-nhan' => 'Nguyên nhân',
            'phan-biet' => 'Phân biệt các loại',
            'cach-xu-ly' => 'Cách xử lý & Can thiệp',
            'tro-choi' => 'Trò chơi kích âm tại nhà',
            'moc-phat-trien' => 'Mốc phát triển chuẩn',
            'co-do' => 'Dấu hiệu cờ đỏ',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::getCategoryLabels()[$this->category] ?? ucfirst($this->category);
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: ($this->title . ' - Mầm Ngôn Ngữ');
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description ?: Str::limit(strip_tags($this->excerpt ?: $this->content), 160);
    }

    public function getJsonLdSchemaAttribute(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'MedicalWebPage',
            'headline' => $this->title,
            'description' => $this->seo_description,
            'url' => route('articles.show', $this->slug),
            'datePublished' => $this->created_at->toIso8601String(),
            'dateModified' => $this->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'Mầm Ngôn Ngữ - Ban Chuyên Môn Y Khoa Nhi',
                'url' => url('/'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Mầm Ngôn Ngữ',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => url('/logo.png'),
                ]
            ],
            'medicalAudience' => [
                '@type' => 'PeopleAudience',
                'suggestedGender' => 'unisex',
                'audienceType' => 'Parents, Caregivers of Children with Speech Delay',
            ],
        ];
    }

    public function pageVisits()
    {
        return $this->hasMany(PageVisit::class);
    }
}
