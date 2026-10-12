<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Disease;

class DiseaseApiController extends Controller
{
    public function show($slug)
    {
        $disease = Disease::with([

            'findings',

            'diagnosticTests',

            'species'

        ])->where(

            'slug',
            $slug

        )->firstOrFail();

        return response()->json(
            $disease
        );
    }
}

