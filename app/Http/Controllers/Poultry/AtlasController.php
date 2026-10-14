<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;

/**
 * The poultry lesion atlas.
 *
 * No logic here, the same shape `BodyMapController` takes: which lesions exist,
 * which organ each one names and which diseases carry it is
 * `LesionAtlasService`, the filtering is the component's, and this exists only to
 * hand it the part. `PART` is passed explicitly rather than read from the session
 * so a link cannot inherit the wrong part.
 *
 * @see LesionAtlasService
 */
class AtlasController extends Controller
{
    private const PART = 'poultry';

    public function __invoke()
    {
        return view('poultry.atlas', ['part' => self::PART]);
    }
}
