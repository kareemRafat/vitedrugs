<?php

namespace App\Http\Controllers;

use App\Models\Disease;

class DiseaseKnowledgeGraphController extends Controller
{
    public function show(string $slug)
    {
        $disease = Disease::with([

            'findings',
            'lesions',
            'species',
            'diagnosticTests',
            'differentialDiagnoses'

        ])
        ->where('slug', $slug)
        ->firstOrFail();

        return view(
            'diseases.graph',
            compact('disease')
        );
    }
}