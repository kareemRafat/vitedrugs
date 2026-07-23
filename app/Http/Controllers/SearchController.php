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
                $query->where('trade_name', 'like', "%{$q}%")
                    ->orWhere('trade_name_ar', 'like', "%{$q}%")
                    ->orWhereHas('activeIngredients', fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('name_ar', 'like', "%{$q}%"))
                    ->orWhereHas('companies', fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('name_ar', 'like', "%{$q}%"))
                    ->orWhereHas('diseases', fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('name_ar', 'like', "%{$q}%"));
            })
                ->limit(20)
                ->get();

            $companies = Company::where('name', 'like', "%{$q}%")
                ->orWhere('name_ar', 'like', "%{$q}%")
                ->limit(20)
                ->get();

            $diseases = Disease::where('name', 'like', "%{$q}%")
                ->orWhere('name_ar', 'like', "%{$q}%")
                ->limit(20)
                ->get();

            $ingredients = ActiveIngredient::where('name', 'like', "%{$q}%")
                ->orWhere('name_ar', 'like', "%{$q}%")
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
