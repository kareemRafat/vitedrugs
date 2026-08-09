<?php

namespace App\Models;

use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DifferentialSyndrome;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\LargeAnimals\Microorganism;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disease extends Model
{
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'is_active',
        'knowledge_payload',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'knowledge_payload' => 'array',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'disease_product'
        )
            ->withPivot([
                'sort_order',
            ])
            ->withTimestamps();
    }

    public function clinicalSigns(): BelongsToMany
    {
        return $this->belongsToMany(
            ClinicalSign::class,
            'disease_clinical_sign'
        )
            ->withPivot([
                'weight',
                'is_specific',
                'is_required',
                'is_pathognomonic',
            ]);
    }

    public function hostSpecies(): BelongsToMany
    {
        return $this->belongsToMany(
            HostSpecies::class,
            'disease_host_species'
        )
            ->withPivot([
                'role',
                'susceptibility',
                'is_primary_host',
                'is_reservoir',
                'is_vector',
                'is_carrier',
                'is_incidental_host',
                'notes',
            ])
            ->withTimestamps();
    }

    public function microorganisms(): BelongsToMany
    {
        return $this->belongsToMany(
            Microorganism::class,
            'disease_microorganism'
        )
            ->withPivot('role')
            ->withTimestamps();
    }

    public function diseaseClassification(): HasOne
    {
        return $this->hasOne(DiseaseClassification::class);
    }

    public function differentialSyndromes(): BelongsToMany
    {
        return $this->belongsToMany(
            DifferentialSyndrome::class,
            'differential_syndrome_disease'
        )
            ->withPivot('boost_score');
    }
}
