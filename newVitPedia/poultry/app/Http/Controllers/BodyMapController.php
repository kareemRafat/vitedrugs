<?php

namespace App\Http\Controllers;

use App\Models\AnatomicalStructure;

class BodyMapController extends Controller
{
    public function index()
    {
        $structures = AnatomicalStructure::with([
            'bodySystem'
        ])
        ->orderBy('display_name')
        ->get();

        return view(
            'anatomy.body-map',
            compact('structures')
        );
    }
}