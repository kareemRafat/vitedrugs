<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;

/**
 * The large-animal drug catalogue.
 *
 * The large-animal half of the shared catalogue page. See
 * `\App\Http\Controllers\Poultry\ProductController` for why this holds no logic.
 *
 * @see \App\Http\Controllers\Poultry\ProductController
 */
class ProductController extends Controller
{
    private const PART = 'large-animals';

    public function __invoke()
    {
        return view('products.index', ['part' => self::PART]);
    }
}
