<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Disease;
use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\MedicalArticle;
use App\Models\Drugs\Microorganism;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->query('q', ''));

        $diseases = $clinicalSigns = $articles = $products = $microorganisms = $medicalArticles = collect();

        if (filled($query)) {
            $diseases = Disease::query()
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('name_ar', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('clinicalSigns', fn ($q) => $q
                        ->where('canonical_name', 'like', "%{$query}%")
                        ->orWhere('display_name', 'like', "%{$query}%"))
                )
                ->distinct()
                ->limit(20)
                ->get();

            $clinicalSigns = ClinicalSign::query()
                ->where(fn ($q) => $q
                    ->where('canonical_name', 'like', "%{$query}%")
                    ->orWhere('display_name', 'like', "%{$query}%"))
                ->limit(20)
                ->get();

            $articles = Blog::query()
                ->where(fn ($q) => $q
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%")
                    ->orWhere('body', 'like', "%{$query}%"))
                ->limit(20)
                ->get();

            $products = Product::query()
                ->where(fn ($q) => $q
                    ->where('trade_name', 'like', "%{$query}%")
                    ->orWhere('trade_name_ar', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%"))
                ->limit(20)
                ->get();

            $microorganisms = Microorganism::query()
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('normalized_name', 'like', "%{$query}%")
                    ->orWhere('tags', 'like', "%{$query}%"))
                ->limit(20)
                ->get();

            $medicalArticles = MedicalArticle::query()
                ->where(fn ($q) => $q
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%"))
                ->limit(20)
                ->get();
        }

        return view('drugs.search.index', compact(
            'query',
            'diseases',
            'clinicalSigns',
            'articles',
            'products',
            'microorganisms',
            'medicalArticles',
        ));
    }
}
