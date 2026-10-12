<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Services\Diagnostic\Similarity\DiseaseSimilarityService;
use App\Models\Species;

class DiseaseExplorerController extends Controller
{
    public function __construct(
        protected DiseaseSimilarityService $similarity
    ) {}


    public function index()
    {
        $diseases = Disease::query()
            ->orderBy('name')
            ->paginate(24);

        return view(
            'diseases.index',
            compact('diseases')
        );
    }

    public function show(string $slug)
    {

        $disease = Disease::query()
            ->with([

                // Clinical data
                'findings',
                'lesions',
                'species',
                'ageGroups',

                // Diagnostic data
                'diagnosticTests',
                'transmissionRoutes',
                'differentialDiagnoses',

                // Epidemiology
                'mortalityProfile',
                'epidemiologyProfile',

                // Media
                'media',

            ])
            ->where('slug', $slug)
            ->firstOrFail();

        $similarDiseases = $this->similarity
            ->findSimilarDiseases($disease);

        return view(
            'diseases.show',
            [
                'disease' => $disease,
                'similarDiseases' => $similarDiseases,
            ]
        );
    }
}
