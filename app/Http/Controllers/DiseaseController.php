<?php

namespace App\Http\Controllers;

use App\Models\Disease;

class DiseaseController extends Controller
{
    public function index()
    {
        $query = Disease::query()
            ->withCount('products');

        if ($search = request('search')) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )

                    ->orWhere(
                        'name_ar',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        $diseases = $query
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'diseases.index',
            compact('diseases')
        );
    }

    public function show(Disease $disease)
    {
        $disease->load([
            'products.dosageForm',
            'products.activeIngredients',
            'products.companies',
        ]);

        return view(
            'diseases.show',
            compact('disease')
        );
    }
}
