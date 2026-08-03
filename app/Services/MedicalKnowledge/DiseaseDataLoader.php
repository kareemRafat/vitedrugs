<?php

namespace App\Services\MedicalKnowledge;

use App\Models\Disease;

class DiseaseDataLoader
{
    public function load(string $slug): Disease
    {
        return Disease::query()
            ->where('slug', $slug)
            ->with([
                'clinicalSigns',
                'diseaseClassification',
                'hostSpecies',
                'microorganisms',
            ])
            ->firstOrFail();
    }
}
