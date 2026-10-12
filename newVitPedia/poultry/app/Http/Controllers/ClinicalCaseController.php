<?php

namespace App\Http\Controllers;

use App\Models\ClinicalCase;
use App\Services\Cases\CaseSimilarityService;

class ClinicalCaseController extends Controller
{
    public function __construct(
        protected CaseSimilarityService $similarity
    ) {}
    public function index()
    {
        $cases = ClinicalCase::with([
            'species',
            'disease'
        ])
            ->latest()
            ->paginate(20);

        return view(
            'cases.index',
            compact('cases')
        );
    }

    public function show(string $slug)
    {
        $case = ClinicalCase::with([
            'species',
            'disease'
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedCases =

            $this->similarity
            ->findRelatedCases(
                $case
            );

        return view(

            'cases.show',

            compact(
                'case',
                'relatedCases'
            )
        );
    }
}
