<?php

namespace App\Models\LargeAnimals;

use Database\Factories\FindingImageFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FindingImage extends Model
{
    use HasFactory;
    use HasUlids;

    protected static function newFactory(): Factory
    {
        return FindingImageFactory::new();
    }

    protected $fillable = [
        'finding_id',
        'image',
        'caption',
        'caption_ar',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    /**
     * The caption in the current locale, falling back to the English one.
     *
     * Same shape as every other localized name in this application: Arabic when
     * the locale is Arabic *and* the row carries one, English otherwise. An
     * Arabic page showing an English caption beats showing nothing.
     */
    public function getLocalizedCaptionAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->caption_ar
            ? $this->caption_ar
            : (string) $this->caption;
    }

    /**
     * The public URL of the stored file.
     *
     * The `str_starts_with` branch is not defensive padding: it is what lets a
     * row carry a path already stored as an absolute URL, which is how an image
     * sourced from outside the upload flow is represented. Copy of
     * `ProductImage::url()`, kept identical so there is one way to resolve a
     * stored image in this codebase.
     */
    public function url(): string
    {
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }
}
