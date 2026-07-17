<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;
use App\Models\Blog;
use App\Models\Company;
use App\Models\Disease;
use App\Models\DosageForm;
use App\Models\Product;
use App\Models\Species;

class LandingController extends Controller
{
    public function __invoke()
    {
        return view('app.landing', [
            'productsCount' => Product::count(),
            'companiesCount' => Company::count(),
            'diseasesCount' => Disease::count(),
            'ingredientsCount' => ActiveIngredient::count(),
            'dosageFormsCount' => DosageForm::count(),
            'speciesCount' => Species::count(),
            'latestBlogs' => Blog::published()
                ->with('category')
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }
}
