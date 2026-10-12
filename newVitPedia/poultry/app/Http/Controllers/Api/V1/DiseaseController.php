<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Disease;

class DiseaseController extends Controller
{
    public function show(string $slug)
    {
        $disease = Disease::with([
            'findings',
            'species',
            'diagnosticTests'
        ])
        ->where('slug', $slug)
        ->firstOrFail();

        return response()->json([
            'id' => $disease->id,
            'name' => $disease->name,
            'slug' => $disease->slug,
            'description' => $disease->description,

            'findings' => $disease->findings
                ->map(function ($finding) {

                    return [
                        'display_name' =>
                            $finding->display_name
                    ];
                }),

            'species' => $disease->species
                ->pluck('name'),

            'diagnostic_tests' =>
                $disease->diagnosticTests
                    ->pluck('name'),
        ]);
    }
}