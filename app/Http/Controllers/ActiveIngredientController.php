<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;

class ActiveIngredientController extends Controller
{
    public function index()
    {
        return view('app.active-ingredients.index');
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
