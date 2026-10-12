<?php

namespace App\Http\Controllers;

use App\Models\BodySystem;
use App\Models\AnatomicalStructure;

class AnatomicalNavigatorController extends Controller
{
    public function index()
    {
        $systems = BodySystem::with([
            'anatomicalStructures'
        ])
            ->orderBy('display_name')
            ->get();

        return view(
            'anatomy.index',
            compact('systems')
        );
    }

    public function show(string $slug)
    {
        $structure = AnatomicalStructure::with([

            'bodySystem',

            'lesions.diseases',

            'findings.diseases'

        ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view(
            'anatomy.show',
            compact('structure')
        );
    }
}
