<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiseaseController extends Controller
{
    public function show(string $slug): RedirectResponse|View
    {
        $disease = Disease::query()
            ->with([
                'clinicalSigns.anatomicalStructure.bodySystem',
                'clinicalSigns.finding',
                'hostSpecies',
                'microorganisms',
                'diseaseClassification',

                // The shared disease view renders a related-products block, and
                // without this it is one extra query per disease page.
                'products',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Search, specializations and microorganisms all list diseases without a
        // domain filter, so this page still receives slugs belonging to other
        // parts. Sending them onward beats a 404, and beats rendering a poultry
        // disease under the large-animal theme.
        if ($disease->domain === Disease::DOMAIN_POULTRY) {
            return redirect()->route('poultry.diseases.show', $disease->slug);
        }

        $clinicalSigns = $disease->clinicalSigns
            ->groupBy(fn ($sign) => $sign->anatomicalStructure?->bodySystem?->localized_display_name
                ?? __('large-animals.diagnosis.general_systemic'));

        return view('large-animals.diseases.show', [
            'part' => 'large-animals',
            'disease' => $disease,
            'clinicalSigns' => $clinicalSigns,
        ]);
    }
}
