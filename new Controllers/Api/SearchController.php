<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchResultResource;
use App\Models\ClinicalSign;
use App\Models\Disease;
use App\Models\Drug;
use App\Models\TherapeuticUse;
use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->q ?? '');

        if ($query === '') {

            return response()->json([
                'results' => [],
            ]);
        }

        $results = collect();

        Disease::query()

            ->where('name_en', 'like', "%{$query}%")

            ->orWhere('name_ar', 'like', "%{$query}%")

            ->limit(10)

            ->get()

            ->each(function ($disease) use ($results) {

                $results->push([

                    'type' => 'disease',

                    'title' => $disease->name_en,

                    'subtitle' => $disease->name_ar,

                    'slug' => $disease->slug,

                    'url' => "/api/v1/diseases/{$disease->slug}",
                ]);
            });

        Drug::query()

            ->where('name_en', 'like', "%{$query}%")

            ->orWhere('name_ar', 'like', "%{$query}%")

            ->limit(10)

            ->get()

            ->each(function ($drug) use ($results) {

                $results->push([

                    'type' => 'drug',

                    'title' => $drug->name_en,

                    'subtitle' => $drug->name_ar,

                    'slug' => $drug->slug,

                    'url' => "/api/v1/drugs/{$drug->slug}",
                ]);
            });

        ClinicalSign::query()

            ->where(function ($q) use ($query) {

                $q->where('display_name', 'like', "%{$query}%")

                    ->orWhere('canonical_name', 'like', "%{$query}%");
            })

            ->limit(10)

            ->get()

            ->each(function ($sign) use ($results) {

                $results->push([

                    'type' => 'clinical_sign',

                    'title' => $sign->display_name,

                    'subtitle' => null,

                    'slug' => null,

                    'url' => null,
                ]);
            });

        Article::query()

            ->where('title', 'like', "%{$query}%")

            ->limit(10)

            ->get()

            ->each(function ($article) use ($results) {

                $results->push([

                    'type' => 'article',

                    'title' => $article->title,

                    'subtitle' => $article->summary,

                    'slug' => $article->slug,

                    'url' => "/articles/{$article->slug}",
                ]);
            });

        TherapeuticUse::query()

            ->where('name_en', 'like', "%{$query}%")

            ->limit(10)

            ->get()

            ->each(function ($use) use ($results) {

                $results->push([

                    'type' => 'therapeutic_use',

                    'title' => $use->name_en,

                    'subtitle' => $use->name_ar,

                    'slug' => $use->slug,

                    'url' => null,
                ]);
            });

        return response()->json([

            'results' => SearchResultResource::collection(
                $results
                    ->take(30)
                    ->values()
            ),
        ]);
    }
}
