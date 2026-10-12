<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\Finding;
use App\Models\Lesion;
use App\Models\ClinicalCase;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [

            'diseases' => Disease::count(),

            'findings' => Finding::count(),

            'lesions' => Lesion::count(),

            'cases' => ClinicalCase::count(),
        ];

        $featuredDiseases = Disease::latest()
            ->take(6)
            ->get();

        $recentCases = ClinicalCase::latest()
            ->take(4)
            ->get();

        return view(
            'pages.home',
            compact(
                'stats',
                'featuredDiseases',
                'recentCases'
            )
        );
    }
}