<?php

namespace App\Models;

use App\Models\TransmissionRoute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disease extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',

        'agent_type',
        'agent_name',

        'family',
        'genus',

        'incubation_period',

        'morbidity_min',
        'morbidity_max',

        'mortality_min',
        'mortality_max',

        'zoonotic',
    ];

    protected $casts = [

        'zoonotic' => 'boolean',

        'morbidity_min' => 'float',
        'morbidity_max' => 'float',

        'mortality_min' => 'float',
        'mortality_max' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | Clinical Findings
    |--------------------------------------------------------------------------
    */

    public function findings(): BelongsToMany
    {
        return $this->belongsToMany(Finding::class)
            ->withPivot([
                'weight',
                'specificity',
                'is_pathognomonic',
                'required_for_diagnosis',
                'negative_finding',
                'likelihood_ratio_positive',
                'likelihood_ratio_negative',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Necropsy Lesions
    |--------------------------------------------------------------------------
    */

    public function lesions(): BelongsToMany
    {
        return $this->belongsToMany(Lesion::class)
            ->withPivot([
                'weight',
                'specificity',
                'is_pathognomonic',
                'is_confirmatory',
            ])
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Species
    |--------------------------------------------------------------------------
    */

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Risk Factors
    |--------------------------------------------------------------------------
    */

    public function riskFactors(): BelongsToMany
    {
        return $this->belongsToMany(RiskFactor::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Age Groups
    |--------------------------------------------------------------------------
    */

    public function ageGroups(): HasMany
    {
        return $this->hasMany(DiseaseAgeGroup::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Diagnostic Tests
    |--------------------------------------------------------------------------
    */

    public function diagnosticTests(): HasMany
    {
        return $this->hasMany(DiagnosticTest::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Epidemiology
    |--------------------------------------------------------------------------
    */

    public function epidemiologyProfile(): HasOne
    {
        return $this->hasOne(
            DiseaseEpidemiologyProfile::class
        );
    }

    public function regionalProfiles(): HasMany
    {
        return $this->hasMany(
            DiseaseRegionProfile::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mortality
    |--------------------------------------------------------------------------
    */

    public function mortalityProfile(): HasOne
    {
        return $this->hasOne(
            DiseaseMortalityProfile::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Transmission
    |--------------------------------------------------------------------------
    */

    public function transmissionRoutes(): BelongsToMany
    {
        return $this->belongsToMany(
            TransmissionRoute::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Images
    |--------------------------------------------------------------------------
    */



    public function clinicalImages(): HasMany
    {
        return $this->images()
            ->where(
                'image_type',
                'clinical'
            );
    }

    public function necropsyImages(): HasMany
    {
        return $this->images()
            ->where(
                'image_type',
                'necropsy'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Media Library
    |--------------------------------------------------------------------------
    */

    public function media(): MorphMany
    {
        return $this->morphMany(
            MediaAsset::class,
            'mediable'
        )->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Clinical Pathways
    |--------------------------------------------------------------------------
    */

    public function clinicalPathways(): HasMany
    {
        return $this->hasMany(
            ClinicalPathway::class
        )->orderBy('step_number');
    }

    public function differentialDiagnoses()
    {
        return $this->belongsToMany(
            Disease::class,
            'disease_differential_diagnosis',
            'disease_id',
            'differential_disease_id'
        )
            ->withPivot([
                'similarity',
                'notes'
            ])
            ->withTimestamps();
    }

    public function differentialFor()
    {
        return $this->belongsToMany(
            Disease::class,
            'disease_differential_diagnosis',
            'differential_disease_id',
            'disease_id'
        );
    }
}
