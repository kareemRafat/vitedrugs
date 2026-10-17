<?php

namespace App\Models\LargeAnimals;

use App\Enums\OntologyType;
use Database\Factories\FindingFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Finding extends Model
{
    use HasFactory;

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
        // Cast rather than compared as a bare string. `signItemsOfType()` took a
        // string and compared it with `===` against this attribute — which would
        // have compared `OntologyType` to `string` and matched nothing at all,
        // silently emptying every lesion list in the application.
        'ontology_type' => OntologyType::class,
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

    /**
     * Photographs of this finding, most relevant first.
     *
     * Only lesions have one in practice — the atlas is the only page that renders
     * them — but the relation is on `Finding` rather than on a separate lesion
     * model because a lesion *is* a finding. Anything that mounts an anatomical
     * structure can have a picture attached.
     */
    public function images(): HasMany
    {
        return $this->hasMany(FindingImage::class)->orderBy('sort_order');
    }
}
