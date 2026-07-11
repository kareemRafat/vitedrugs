<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\Product;

class SearchController extends Controller
{
    public function index()
    {
        $q = request('q');

        $products = collect();
        $companies = collect();
        $diseases = collect();
        $ingredients = collect();

        if ($q) {

            $products = Product::where(function ($query) use ($q) {
                $query->whereFullText(['trade_name', 'trade_name_ar'], $q)
                    ->orWhereHas('activeIngredients', fn ($q2) => $q2->whereFullText(['name', 'name_ar'], $q))
                    ->orWhereHas('companies', fn ($q2) => $q2->whereFullText(['name', 'name_ar'], $q))
                    ->orWhereHas('diseases', fn ($q2) => $q2->whereFullText(['name', 'name_ar'], $q));
            })
                ->limit(20)
                ->get();

            $companies = Company::whereFullText(['name', 'name_ar'], $q)
                ->limit(20)
                ->get();

            $diseases = Disease::whereFullText(['name', 'name_ar'], $q)
                ->limit(20)
                ->get();

            $ingredients = ActiveIngredient::whereFullText(['name', 'name_ar'], $q)
                ->limit(20)
                ->get();
        }

        return view(
            'app.search.index',
            compact(
                'q',
                'products',
                'companies',
                'diseases',
                'ingredients'
            )
        );
    }
}
