<?php

namespace App\Models\LargeAnimals;

use App\Models\Disease;
use Database\Factories\ClinicalSignFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClinicalSign extends Model
{
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return ClinicalSignFactory::new();
    }

    protected $fillable = [
        'anatomical_structure_id',
        'finding_id',
        'modifier_id',
        'canonical_name',
        'display_name',
        'display_name_ar',
        'stage',
        'severity_level_id',
        'semantic_slug',
    ];

    public function getLocalizedDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->display_name_ar
            ? $this->display_name_ar
            : $this->display_name;
    }

    public function anatomicalStructure(): BelongsTo
    {
        return $this->belongsTo(AnatomicalStructure::class);
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(Modifier::class);
    }

    public function diseases(): BelongsToMany
    {
        return $this->belongsToMany(Disease::class, 'disease_clinical_sign')
            ->withPivot([
                'weight',
                'is_specific',
                'is_required',
                'is_pathognomonic',
            ]);
    }
}
