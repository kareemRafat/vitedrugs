<?php

namespace App\Http\Controllers;

use App\Models\Disease;

class DiseaseController extends Controller
{
    public function index()
    {
        $query = Disease::query()->withCount('products');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereFullText(['name', 'name_ar'], $search.'*', ['mode' => 'boolean']);

                if (mb_strlen($search) < 3) {
                    $q->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%");
                }
            });
        }

        if ($letter = request('letter')) {
            $query->where('name', 'LIKE', $letter.'%');
        }

        $query->orderBy('name');

        $diseases = $query->paginate(21)->withQueryString();

        $availableLetters = Disease::query()
            ->selectRaw('UPPER(LEFT(name, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter')
            ->toArray();

        return view('app.diseases.index', compact('diseases', 'availableLetters'));
    }

    public function show(Disease $disease)
    {
        $disease->load([
            'products.dosageForm',
            'products.activeIngredients',
            'products.companies',
        ]);

        $ingredients = $disease->products
            ->flatMap(fn ($product) => $product->activeIngredients)
            ->unique('id');

        return view(
            'app.diseases.show',
            compact('disease', 'ingredients')
        );
    }
}
