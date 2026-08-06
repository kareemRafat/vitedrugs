<?php

namespace App\Models\LargeAnimals;

use App\Models\Disease;
use Database\Factories\MedicalArticleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalArticle extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return MedicalArticleFactory::new();
    }

    protected $fillable = [
        'title',
        'title_ar',
        'slug',
        'content',
        'content_ar',
        'disease_id',
        'species',
        'species_ar',
        'article_type',
        'is_published',
        'summary',
        'summary_ar',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function getLocalizedTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->title_ar
            ? $this->title_ar
            : $this->title;
    }

    public function getLocalizedSummaryAttribute(): ?string
    {
        return app()->getLocale() === 'ar' && $this->summary_ar
            ? $this->summary_ar
            : $this->summary;
    }

    public function getLocalizedContentAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->content_ar
            ? $this->content_ar
            : $this->content;
    }

    public function getLocalizedSpeciesAttribute(): ?string
    {
        return app()->getLocale() === 'ar' && $this->species_ar
            ? $this->species_ar
            : $this->species;
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}
