<?php

namespace App\Models;

use App\Models\Concerns\HasDomain;
use App\Models\Concerns\HasLocalizedName;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DifferentialSyndrome;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\Finding;
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
    use HasDomain;
    use HasFactory;
    use HasLocalizedName;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'is_active',
        'is_zoonotic',
        'is_internal',
        'domain',
        'knowledge_payload',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_zoonotic' => 'boolean',
        'is_internal' => 'boolean',
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

    /**
     * Clinical signs and gross lesions are the same relation.
     *
     * `clinical_signs` is a layer over `findings` — a sign is a finding plus an
     * anatomical structure plus a modifier — and the dump imported gross lesions
     * into the same table with `findings.ontology_type = 'lesion'` (§4.1). So
     * "the signs of this disease" and "the lesions of this disease" are the same
     * pivot read through `finding.ontology_type`, which splits them.
     *
     * Reading the pivot directly rather than the `knowledge_payload` is what
     * makes this usable across the whole section: signs reach 159 diseases and
     * lesions 148, while only 2 of the 151 poultry diseases carry a payload at
     * all. `finding` must be eager-loaded or this is one query per sign.
     *
     * @return array<string, string> Lower-cased canonical name => display name.
     */
    public function signItemsOfType(string $ontologyType): array
    {
        return $this->clinicalSigns
            ->filter(fn (ClinicalSign $sign): bool => $sign->finding?->ontology_type === $ontologyType)
            ->mapWithKeys(fn (ClinicalSign $sign): array => [
                mb_strtolower(trim((string) $sign->canonical_name)) => $sign->localized_display_name,
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     *
     * @see signItemsOfType()
     */
    public function lesionItems(): array
    {
        return $this->signItemsOfType(Finding::ONTOLOGY_LESION);
    }

    /**
     * Every finding that is not a post-mortem lesion.
     *
     * Matched by *excluding* lesions rather than by matching `'sign'` exactly,
     * because `findings.ontology_type` is nullable — and is null on the curated
     * signs: **Pyrexia**, **Inappetence** and **Hemorrhagic diarrhea** on the
     * poultry side today. Matching `'sign'` silently dropped all three, pyrexia
     * being the single most common sign in the clinical vocabulary.
     *
     * A post-mortem finding is explicitly typed `lesion`; anything not typed as
     * one is a clinical observation. That is the assumption encoded here.
     *
     * @return array<string, string>
     *
     * @see signItemsOfType()
     */
    public function signItems(): array
    {
        return $this->clinicalSigns
            ->filter(fn (ClinicalSign $sign): bool => $sign->finding?->ontology_type !== Finding::ONTOLOGY_LESION)
            ->mapWithKeys(fn (ClinicalSign $sign): array => [
                mb_strtolower(trim((string) $sign->canonical_name)) => $sign->localized_display_name,
            ])
            ->all();
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
