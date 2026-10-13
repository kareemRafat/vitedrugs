<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;

/**
 * Side-by-side disease comparison for the large-animals part.
 *
 * @see \App\Http\Controllers\Poultry\CompareController
 */
class CompareController extends Controller
{
    private const PART = 'large-animals';

    public function __invoke()
    {
        return view('compare.index', ['part' => self::PART]);
    }
}
