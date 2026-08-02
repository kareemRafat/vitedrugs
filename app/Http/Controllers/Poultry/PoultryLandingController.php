<?php

namespace App\Http\Controllers\Poultry;

use App\Http\Controllers\Controller;

class PoultryLandingController extends Controller
{
    public function __invoke()
    {
        return view('poultry.landing');
    }
}
