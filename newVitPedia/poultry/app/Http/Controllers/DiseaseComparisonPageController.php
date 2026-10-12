<?php

namespace App\Http\Controllers;

use App\Models\Disease;

class DiseaseComparisonPageController extends Controller
{
    public function index()
    {
        $diseases = Disease::orderBy('name')
            ->select('id', 'name')
            ->get();

        return view(
            'diseases.comparison',
            compact('diseases')
        );
    }
}