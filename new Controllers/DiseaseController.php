<?php

namespace App\Http\Controllers;

use App\Models\ClinicalSign;
use App\Models\Disease;


class DiseaseController extends Controller
{
    public function show(
        string $slug
    ) {
        $disease = Disease::query()

            ->with([

                'clinicalSigns.anatomicalStructure.bodySystem',
            ])

            ->where(
                'slug',
                $slug
            )

            ->firstOrFail();

        return view(
            'diseases.show',
            compact('disease')
        );
    }

  
}
