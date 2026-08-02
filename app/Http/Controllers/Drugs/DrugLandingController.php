<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;

class DrugLandingController extends Controller
{
    public function __invoke()
    {
        return view('drugs.landing');
    }
}
