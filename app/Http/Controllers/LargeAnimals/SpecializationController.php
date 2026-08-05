<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\LargeAnimals\MedicalArticle;
use App\Models\LargeAnimals\Microorganism;
use App\Models\Product;

class SpecializationController extends Controller
{
    protected const GROUPS = [
        'ruminant',
        'poultry',
        'fish',
    ];

    public function index()
    {
        $groups = collect(self::GROUPS)->map(function (string $group) {
            return [
                'key' => $group,
                'species' => $this->speciesForGroup($group),
                'disease_count' => $this->diseasesQuery($group)->count(),
            ];
        });

        return view('large-animals.specializations.index', compact('groups'));
    }

    public function show(string $group)
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        $species = $this->speciesForGroup($group);
        $speciesNames = $species->pluck('display_name')->all();

        $diseases = $this->diseasesQuery($group)->get();

        $products = Product::query()
            ->whereHas('diseases', fn ($q) => $q->whereHas('hostSpecies', fn ($q) => $q->where('taxonomy_group', $group)))
            ->orderBy('trade_name')
            ->get();

        $articles = MedicalArticle::query()
            ->where('is_published', true)
            ->latest()
            ->get()
            ->filter(function (MedicalArticle $article) use ($speciesNames) {
                $tokens = collect(explode(',', mb_strtolower($article->species ?? '')))
                    ->map('trim')
                    ->filter();

                return $tokens->intersect(array_map('mb_strtolower', $speciesNames))->isNotEmpty();
            })
            ->values();

        $microorganisms = Microorganism::query()
            ->whereHas('diseases', fn ($q) => $q->whereHas('hostSpecies', fn ($q) => $q->where('taxonomy_group', $group)))
            ->orderBy('name')
            ->get();

        return view('large-animals.specializations.show', compact('group', 'species', 'diseases', 'products', 'articles', 'microorganisms'));
    }

    private function speciesForGroup(string $group)
    {
        return HostSpecies::query()
            ->where('taxonomy_group', $group)
            ->orderBy('display_name')
            ->get();
    }

    private function diseasesQuery(string $group)
    {
        return Disease::query()
            ->whereHas('hostSpecies', fn ($q) => $q->where('taxonomy_group', $group))
            ->orderBy('name');
    }
}
