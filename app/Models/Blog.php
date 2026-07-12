<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'blog_category_id',
        'title',
        'title_ar',
        'slug',
        'excerpt',
        'excerpt_ar',
        'body',
        'body_ar',
        'cover_image',
        'author_id',
        'published_at',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function getReadTimeAttribute(): int
    {
        $body = app()->getLocale() === 'ar' && $this->body_ar
            ? $this->body_ar
            : $this->body;

        return max(1, (int) round(str_word_count(strip_tags($body ?? '')) / 200));
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
