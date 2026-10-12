<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Search\ClinicalSearchService;

class ClinicalSearchController extends Controller
{
    public function __construct(
        protected ClinicalSearchService $search
    ) {
    }

    public function index(Request $request)
    {
        $results = collect();

        if ($request->filled('q')) {

            $results =
                $this->search->search(
                    $request->q
                );
        }

        return view(
            'search.clinical',
            compact(
                'results'
            )
        );
    }
}