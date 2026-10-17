<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LargeAnimals\ProductController;
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

    /**
     * The disease list.
     *
     * Holds no logic of its own. Scoping to the domain, the alphabetisation, the
     * letter rail and the three per-disease counts are the shared component's —
     * this exists only to hand it the right part, the same thin shape
     * `ProductController` takes.
     *
     * @see ProductController
     */
    public function index()
    {
        return view('diseases.index', ['part' => self::PART]);
    }

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

                // The shared disease view renders a related-products block, and
                // without this it is one extra query per disease page. `products`
                // is not scoped to the part here: a poultry disease may
                // legitimately list a drug whose dosing record is only held for
                // cattle, and silently dropping it would hide a real treatment.
                'products',
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
