<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;
use App\Models\LargeAnimals\Microorganism;
use Illuminate\Support\Str;

class MicroorganismController extends Controller
{
    public const MICROORGANISM_TYPES = [
        'bacteria',
        'virus',
        'fungus',
        'parasite',
        'other',
    ];

    public function index()
    {
        $microorganisms = Microorganism::query()
            ->catalogue()
            ->withCount([
                'diseases',
                'activeIngredients',
            ])
            ->orderBy('name')
            ->get();

        $grouped = $microorganisms
            ->groupBy(fn ($microorganism) => $microorganism->microorganism_type ?? 'other')
            ->sortBy(fn ($items, $type) => array_search($type, self::MICROORGANISM_TYPES, true));

        return view('large-animals.microorganisms.index', compact('grouped'));
    }

    public function show(string $slug)
    {
        $microorganism = Microorganism::query()
            ->where('slug', $slug)
            ->first();

        if (! $microorganism) {
            $microorganism = Microorganism::all()->first(fn ($m) => Str::slug($m->name) === $slug);
        }

        if (! $microorganism) {
            abort(404);
        }

        $microorganism->load([
            'diseases',
            'activeIngredients',
        ]);

        return view('large-animals.microorganisms.show', compact('microorganism'));
    }
}
