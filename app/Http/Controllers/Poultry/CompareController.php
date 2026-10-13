<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;

/**
 * Side-by-side disease comparison for the poultry part.
 *
 * The page holds no logic of its own. The comparison is a Livewire component and
 * the component reads its part from the view, so this controller exists only to
 * hand it the right one — the same thin shape `DiagnosticController` and
 * `SignController` take.
 *
 * `PART` is passed explicitly rather than read from the session, so a link cannot
 * inherit the wrong part (§12.1).
 *
 * @see \App\Http\Controllers\LargeAnimals\CompareController
 */
class CompareController extends Controller
{
    private const PART = 'poultry';

    public function __invoke()
    {
        return view('compare.index', ['part' => self::PART]);
    }
}
