<?php

namespace App\Http\Controllers;

use App\Models\{Article, ClinicalSign, Disease, Drug, TherapeuticUse};
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->q ?? '');

        $diseases = $clinicalSigns = $articles = $therapeuticUses = $drugs = collect();

        if (! filled($query)) {
            return view('search.index', compact(
                'query',
                'diseases',
                'clinicalSigns',
                'articles',
                'therapeuticUses',
                'drugs'
            ));
        }

        $diseases = Disease::where(fn ($q) => $q
            ->where('name_en', 'like', "%{$query}%")
            ->orWhere('summary', 'like', "%{$query}%")
            ->orWhereHas('clinicalSigns', fn ($q) => $q
                ->where('canonical_name', 'like', "%{$query}%")
                ->orWhere('display_name', 'like', "%{$query}%")
            )
        )
        ->distinct()
        ->limit(20)
        ->get();

        $clinicalSigns = ClinicalSign::where(fn ($q) => $q
            ->where('canonical_name', 'like', "%{$query}%")
            ->orWhere('display_name', 'like', "%{$query}%")
        )
        ->limit(20)
        ->get();

        $articles = Article::where(fn ($q) => $q
            ->where('title', 'like', "%{$query}%")
            ->orWhere('summary', 'like', "%{$query}%")
        )
        ->limit(20)
        ->get();

        $therapeuticUses = TherapeuticUse::where('name_en', 'like', "%{$query}%")
            ->limit(20)
            ->get();

        $drugs = Drug::where(fn ($q) => $q
            ->where('name_en', 'like', "%{$query}%")
            ->orWhere('summary', 'like', "%{$query}%")
        )
        ->limit(20)
        ->get();

        return view('search.index', compact(
            'query',
            'diseases',
            'clinicalSigns',
            'articles',
            'therapeuticUses',
            'drugs'
        ));
    }
}