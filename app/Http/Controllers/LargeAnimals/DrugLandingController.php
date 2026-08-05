<?php

namespace App\Http\Controllers\LargeAnimals;

use App\Http\Controllers\Controller;

class DrugLandingController extends Controller
{
    public function __invoke()
    {
        return view('large-animals.landing');
    }
}
