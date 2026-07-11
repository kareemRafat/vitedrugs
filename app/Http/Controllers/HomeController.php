<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Company;
use App\Models\Disease;
use App\Models\ActiveIngredient;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'productsCount' => Product::count(),
            'companiesCount' => Company::count(),
            'diseasesCount' => Disease::count(),
            'ingredientsCount' => ActiveIngredient::count(),

            'latestProducts' => Product::latest()
                ->take(5)
                ->get(),

            'latestCompanies' => Company::latest()
                ->take(5)
                ->get(),
        ]);
    }
}
