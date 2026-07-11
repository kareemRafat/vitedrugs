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

            $products = Product::where(
                'trade_name',
                'like',
                "%{$q}%"
            )
                ->limit(20)
                ->get();

            $companies = Company::where(
                'name',
                'like',
                "%{$q}%"
            )
                ->limit(20)
                ->get();

            $diseases = Disease::where(
                'name',
                'like',
                "%{$q}%"
            )
                ->limit(20)
                ->get();

            $ingredients = ActiveIngredient::where(
                'name',
                'like',
                "%{$q}%"
            )
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
