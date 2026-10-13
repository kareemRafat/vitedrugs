<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;
use App\Models\Disease;

/**
 * Disease detail page for the poultry part.
 *
 * Same markup as the large-animal page — the knowledge payload, clinical signs
 * and classification are identical shapes — but resolved strictly against
 * `domain = poultry` so a large-animal slug is a 404 here rather than a page
 * that leaks out of the section.
 *
 * @see \App\Http\Controllers\LargeAnimals\DiseaseController
 */
class DiseaseController extends Controller
{
    private const PART = 'poultry';

    public function show(string $slug)
    {
        $disease = Disease::query()
            ->forDomain(Disease::DOMAIN_POULTRY)
            ->with([
                'clinicalSigns.anatomicalStructure.bodySystem',
                'clinicalSigns.finding',
                'hostSpecies',
                'microorganisms',
                'diseaseClassification',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $clinicalSigns = $disease->clinicalSigns
            ->groupBy(fn ($sign) => $sign->anatomicalStructure?->bodySystem?->localized_display_name
                ?? __('poultry.diagnosis.general_systemic'));

        return view('large-animals.diseases.show', [
            'part' => self::PART,
            'disease' => $disease,
            'clinicalSigns' => $clinicalSigns,
        ]);
    }
}
