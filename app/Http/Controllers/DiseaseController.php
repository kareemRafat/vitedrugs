<?php

namespace App\Http\Controllers;

use App\Models\Disease;

class DiseaseController extends Controller
{
    public function index()
    {
        return view('app.diseases.index');
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
