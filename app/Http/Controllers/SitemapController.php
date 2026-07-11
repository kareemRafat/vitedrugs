<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        return response()
            ->view('sitemap', [

                'products' => Product::all(),

                'diseases' => Disease::all(),

                'companies' => Company::all(),

                'ingredients' => ActiveIngredient::all(),

            ])
            ->header(
                'Content-Type',
                'text/xml'
            );
    }
}
