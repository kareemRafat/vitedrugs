<?php

namespace App\Services\Search;

use App\Models\Disease;
use Illuminate\Support\Collection;

class ClinicalSearchService
{
    public function search(
        string $query
    ): Collection {

        $tokens = collect(
            preg_split(
                '/[\s,+]+/',
                strtolower($query)
            )
        )
        ->filter()
        ->unique()
        ->values();

        $results = collect();

        $diseases = Disease::with([
            'findings',
            'lesions'
        ])->get();

        foreach ($diseases as $disease) {

            $score = 0;

            $matchedTerms = [];

            foreach ($tokens as $token) {

                /*
                |--------------------------------------------------------------------------
                | Findings
                |--------------------------------------------------------------------------
                */

                foreach ($disease->findings as $finding) {

                    if (

                        str_contains(
                            strtolower(
                                $finding->display_name
                            ),
                            $token
                        )

                        ||

                        str_contains(
                            strtolower(
                                $finding->canonical_name
                            ),
                            $token
                        )

                    ) {

                        $score +=
                            ($finding->pivot->weight ?? 5);

                        $matchedTerms[] =
                            $finding->display_name;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Lesions
                |--------------------------------------------------------------------------
                */

                foreach ($disease->lesions as $lesion) {

                    if (

                        str_contains(
                            strtolower(
                                $lesion->display_name
                            ),
                            $token
                        )

                        ||

                        str_contains(
                            strtolower(
                                $lesion->canonical_name
                            ),
                            $token
                        )

                    ) {

                        $score += 8;

                        $matchedTerms[] =
                            $lesion->display_name;
                    }
                }
            }

            if ($score === 0) {
                continue;
            }

            $results->push([

                'disease' => $disease,

                'score' => $score,

                'matched_terms' => array_values(
                    array_unique(
                        $matchedTerms
                    )
                )
            ]);
        }

        return $results
            ->sortByDesc('score')
            ->take(25)
            ->values();
    }
}