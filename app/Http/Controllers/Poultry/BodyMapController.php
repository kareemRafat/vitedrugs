<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LargeAnimals\ProductController;

/**
 * The poultry body map.
 *
 * The page holds no logic of its own. Which diseases belong to which organ is
 * `OrganAttachmentService`, the selection state is the shared component's, and
 * the controller exists only to hand it the right part — the same thin shape
 * `ProductController` and `CompareController` take.
 *
 * Poultry only for now. The anatomy vocabulary is shared between the species and
 * `anatomical_structures.domain` is what will keep a cattle organ off a bird's
 * diagram, but a second diagram needs its own geometry before it can be drawn,
 * and inventing one is not part of this phase.
 *
 * `PART` is passed explicitly rather than read from the session, so a link cannot
 * inherit the wrong part (§12.1).
 *
 * @see ProductController
 */
class BodyMapController extends Controller
{
    private const PART = 'poultry';

    public function __invoke()
    {
        return view('anatomy.body-map', ['part' => self::PART]);
    }
}
