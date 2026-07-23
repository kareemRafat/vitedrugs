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

            $products = Product::with('manufacturer')->where(function ($query) use ($q) {
                $query->where('trade_name', 'like', "%{$q}%")
                    ->orWhere('trade_name_ar', 'like', "%{$q}%")
                    ->orWhereHas('activeIngredients', fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('name_ar', 'like', "%{$q}%"))
                    ->orWhereHas('companies', fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('name_ar', 'like', "%{$q}%"))
                    ->orWhereHas('diseases', fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('name_ar', 'like', "%{$q}%"));
            })
                ->paginate(10, ['*'], 'product_page')
                ->withQueryString();

            $companies = Company::where('name', 'like', "%{$q}%")
                ->orWhere('name_ar', 'like', "%{$q}%")
                ->paginate(10, ['*'], 'company_page')
                ->withQueryString();

            $diseases = Disease::where('name', 'like', "%{$q}%")
                ->orWhere('name_ar', 'like', "%{$q}%")
                ->paginate(10, ['*'], 'disease_page')
                ->withQueryString();

            $ingredients = ActiveIngredient::where('name', 'like', "%{$q}%")
                ->orWhere('name_ar', 'like', "%{$q}%")
                ->paginate(10, ['*'], 'ingredient_page')
                ->withQueryString();
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
