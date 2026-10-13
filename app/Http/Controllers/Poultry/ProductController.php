<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;

/**
 * The poultry drug catalogue.
 *
 * The page holds no logic of its own. Membership in the part is
 * `Product::applicableTo()`, the filtering and eager loading are the shared
 * component's, and the controller exists only to hand it the right part — the
 * same thin shape `CompareController` takes.
 *
 * `PART` is passed explicitly rather than read from the session, so a link cannot
 * inherit the wrong part (§12.1).
 *
 * @see \App\Http\Controllers\LargeAnimals\ProductController
 */
class ProductController extends Controller
{
    private const PART = 'poultry';

    public function __invoke()
    {
        return view('products.index', ['part' => self::PART]);
    }
}
