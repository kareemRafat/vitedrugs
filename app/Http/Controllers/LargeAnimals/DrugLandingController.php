<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\LargeAnimals\MedicalArticle;
use App\Models\LargeAnimals\Microorganism;
use App\Models\LargeAnimals\VeterinaryProject;

class DrugLandingController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'diseases' => Disease::query()->where('is_active', true)->count(),
            'clinical_signs' => ClinicalSign::query()->count(),
            'microorganisms' => Microorganism::query()->catalogue()->count(),
            'articles' => MedicalArticle::query()->where('is_published', true)->count(),
            'projects' => VeterinaryProject::query()->where('is_published', true)->count(),
            'species' => HostSpecies::query()->count(),
        ];

        return view('large-animals.landing', compact('stats'));
    }
}
