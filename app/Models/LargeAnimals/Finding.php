<?php

namespace App\Models\LargeAnimals;

use Database\Factories\FindingFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Finding extends Model
{
    use HasFactory;

    /**
     * A post-mortem finding.
     *
     * The only value on `ontology_type` that changes how a finding is read: a
     * lesion is something you see when you open the bird, everything else is a
     * clinical observation. The column is nullable and *is* null on the curated
     * signs — Pyrexia, Inappetence and Hemorrhagic diarrhea — so "is it a
     * lesion?" has to be asked by exclusion rather than by matching a positive
     * clinical value. `Disease::signItems()` and `Disease::lesionItems()` are
     * the two places that question is asked.
     */
    public const ONTOLOGY_LESION = 'lesion';

    protected static function newFactory(): Factory
    {
        return FindingFactory::new();
    }

    protected $fillable = [
        'canonical_name',
        'display_name',
        'display_name_ar',
        'category',
        'ontology_type',
        'parent_id',
        'is_noisy',
        'is_general_sign',
        'slug',
    ];

    public function getLocalizedDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->display_name_ar
            ? $this->display_name_ar
            : $this->display_name;
    }

    protected $casts = [
        'is_noisy' => 'boolean',
        'is_general_sign' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function clinicalSigns(): HasMany
    {
        return $this->hasMany(ClinicalSign::class);
    }
}
