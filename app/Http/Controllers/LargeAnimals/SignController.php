<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Concerns\ResolvesPartClinicalSign;
use App\Http\Controllers\Controller;

/**
 * Clinical sign detail page for the large-animals part — what a sign is, and
 * which large-animal diseases carry it.
 *
 * One page of knowledge, reached from the global search. The poultry controller
 * is a thin sibling; the scoping rule and the ordering live in the shared trait,
 * because a sign reachable from both domains must behave identically on both
 * sides or the section boundary quietly stops holding.
 *
 * @see \App\Http\Controllers\Poultry\SignController
 */
class SignController extends Controller
{
    use ResolvesPartClinicalSign;

    private const PART = 'large-animals';

    public function show(string $sign)
    {
        $signs = $this->clinicalSignsOrFail(self::PART, $sign);

        return view('signs.show', [
            'part' => self::PART,
            'sign' => $signs->first(),
            'recordCount' => $signs->count(),
            'variants' => $this->recordVariants($signs),
            'diseases' => $this->diseasesFor($signs, self::PART),
        ]);
    }
}
