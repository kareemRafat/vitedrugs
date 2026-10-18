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
    /**
     * Rows shown by the article band. Deliberately small: this is evidence that
     * the library has depth, not a second index page. The view hides the band
     * outright when nothing comes back.
     */
    private const TEASER_COUNT = 3;

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

        return view('large-animals.landing', [
            'stats' => $stats,

            // Newest first, matching the ordering the full article index uses, so
            // the teaser and the page it links to agree.
            //
            // Only the linked disease's display columns are selected: the row
            // renders a disease name and nothing else, and `Disease` carries a
            // JSON payload column that would otherwise be dragged in with it.
            'latestArticles' => MedicalArticle::query()
                ->where('is_published', true)
                ->with('disease:id,name,name_ar')
                ->latest()
                ->take(self::TEASER_COUNT)
                ->get(),
        ]);
    }
}
