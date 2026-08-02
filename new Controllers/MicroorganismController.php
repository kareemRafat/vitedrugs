<?php

namespace App\Http\Controllers;

use App\Models\Microorganism;

class MicroorganismController extends Controller
{
    public function show(string $slug)
    {
        $microorganism = Microorganism::where(
            'slug',
            $slug
        )->firstOrFail();

        return view(
            'microorganisms.show',
            compact('microorganism')
        );
    }
}
