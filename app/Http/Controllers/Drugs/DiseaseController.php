<?php

namespace App\Http\Controllers\Drugs;

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
            ->groupBy(fn ($sign) => $sign->anatomicalStructure?->bodySystem?->display_name ?? 'General Systemic Signs');

        return view('drugs.diseases.show', compact('disease', 'clinicalSigns'));
    }
}
