<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Concerns\ResolvesPartClinicalSign;
use App\Http\Controllers\Controller;

/**
 * Clinical sign detail page for the poultry part — what a sign is, and which
 * poultry diseases carry it.
 *
 * This is where a clinical sign in the search results goes. It used to go to the
 * diagnosis tool, which required a species and at least three signs and so could
 * not be submitted with the single sign it had been handed; and then briefly to
 * the filter tool, which worked but answered the question one level later than
 * it needed to. A sign is a real entry in the knowledge base, so it gets a real
 * page, and the page offers both hand-offs onward at the bottom.
 *
 * Resolved strictly against `domain = poultry`, so a sign carried only by
 * large-animal diseases is a 404 here rather than a page that leaks out of the
 * section.
 *
 * @see \App\Http\Controllers\LargeAnimals\SignController
 */
class SignController extends Controller
{
    use ResolvesPartClinicalSign;

    private const PART = 'poultry';

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
