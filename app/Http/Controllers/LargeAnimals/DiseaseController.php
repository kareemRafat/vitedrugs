<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\Disease;

class DiseaseController extends Controller
{
    public function show(string $slug)
    {
        $disease = Disease::query()
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
                ?? __('large-animals.diagnosis.general_systemic'));

        return view('large-animals.diseases.show', compact('disease', 'clinicalSigns'));
    }
}
