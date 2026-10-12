<?php

namespace App\Http\Controllers;
use App\Models\Species;

class DiagnosticPageController extends Controller
{
     public function index()
    {
        $species = Species::orderBy('name')->get();

        return view(
            'diagnostic.index',
            compact('species')
        );
    }
}
