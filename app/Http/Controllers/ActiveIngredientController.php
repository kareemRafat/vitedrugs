<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;

class ActiveIngredientController extends Controller
{
    public function index()
    {
        $query = ActiveIngredient::query()->withCount('products');

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

        $ingredients = $query->paginate(21)->withQueryString();

        $availableLetters = ActiveIngredient::query()
            ->selectRaw('UPPER(LEFT(name, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter')
            ->toArray();

        return view('app.active-ingredients.index', compact('ingredients', 'availableLetters'));
    }

    public function show(
        ActiveIngredient $activeIngredient
    ) {
        $activeIngredient->load([
            'products.companies',
            'products.diseases',
            'drugClasses',
        ]);
        $diseases = collect();

        foreach ($activeIngredient->products as $product) {
            foreach ($product->diseases as $disease) {
                $diseases->push($disease);
            }
        }

        $diseases = $diseases->unique('id');

        return view(
            'app.active-ingredients.show',
            compact('activeIngredient', 'diseases')
        );
    }
}
