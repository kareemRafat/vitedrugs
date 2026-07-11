<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;

class ActiveIngredientController extends Controller
{
    public function index()
    {
        $ingredients = ActiveIngredient::query()
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20);

        return view(
            'app.active-ingredients.index',
            compact('ingredients')
        );
    }

    public function show(
        ActiveIngredient $activeIngredient
    ) {
        $activeIngredient->load([
            'products.dosageForm',
            'products.companies',
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
