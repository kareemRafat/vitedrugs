<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Diagnostic\Epidemiology\OutbreakAnalyzerService;
use Illuminate\Http\Request;

class OutbreakController extends Controller
{
    public function __construct(
        protected OutbreakAnalyzerService $analyzer
    ) {
    }

    public function analyze(Request $request)
    {
        $request->validate([

            'flock_size' => 'required|integer',

            'affected_birds' => 'required|integer',

            'dead_birds' => 'required|integer'
        ]);

        return response()->json(

            $this->analyzer
                ->analyze(
                    $request->all()
                )
        );
    }
}