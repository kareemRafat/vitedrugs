<?php

namespace App\Http\Controllers;

use App\Models\BodySystem;

class LesionAtlasController extends Controller
{
    public function index()
    {
        $systems = BodySystem::with([
            'anatomicalStructures.lesions.diseases'
        ])
        ->orderBy('display_name')
        ->get();

        return view(
            'atlas.index',
            compact('systems')
        );
    }
}