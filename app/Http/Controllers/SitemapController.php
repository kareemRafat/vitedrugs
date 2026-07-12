<?php

namespace App\Http\Controllers;

use App\Models\ActiveIngredient;
use App\Models\Blog;
use App\Models\Company;
use App\Models\Disease;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $locales = ['en', 'ar'];

        return response()
            ->view('sitemap', [
                'locales' => $locales,
                'products' => Product::cursor(),
                'diseases' => Disease::cursor(),
                'companies' => Company::cursor(),
                'ingredients' => ActiveIngredient::cursor(),
                'blogs' => Blog::published()->cursor(),
            ])
            ->header('Content-Type', 'text/xml');
    }
}
