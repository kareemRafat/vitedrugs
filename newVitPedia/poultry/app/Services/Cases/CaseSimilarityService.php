<?php

namespace App\Services\Cases;

use App\Models\ClinicalCase;
use Illuminate\Support\Collection;

class CaseSimilarityService
{
    public function findSimilarCases(
        array $findings = [],
        ?int $speciesId = null,
        int $limit = 5
    ): Collection {

        $normalizedFindings = collect($findings)

            ->map(
                fn($f) => strtolower(trim($f))
            )

            ->unique()

            ->values()

            ->toArray();

        $results = collect();

        $cases = ClinicalCase::with([
            'species',
            'disease'
        ])->get();

        foreach ($cases as $case) {

            $score = 0;

            $matchedTerms = [];

            /*
            |--------------------------------------------------------------------------
            | Species Match
            |--------------------------------------------------------------------------
            */

            if (
                $speciesId
                &&
                $case->species_id === $speciesId
            ) {

                $score += 20;
            }

            /*
            |--------------------------------------------------------------------------
            | Clinical Findings Match
            |--------------------------------------------------------------------------
            */

            $caseText = strtolower(

                implode(' ', [

                    $case->history,

                    $case->clinical_findings,

                    $case->necropsy_findings,

                    $case->laboratory_results
                ])
            );

            foreach ($normalizedFindings as $finding) {

                if (
                    str_contains(
                        $caseText,
                        strtolower($finding)
                    )
                ) {

                    $score += 10;

                    $matchedTerms[] =
                        $finding;
                }
            }

            if ($score <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Similarity %
            |--------------------------------------------------------------------------
            */

            $similarity = round(

                min(

                    100,

                    (
                        count($matchedTerms)
                        /
                        max(
                            1,
                            count($normalizedFindings)
                        )
                    ) * 100

                    +

                    ($score / 10)
                )
            );

            $results->push([

                'case' => $case,

                'similarity' => $similarity,

                'matched_terms' =>

                    array_values(
                        array_unique(
                            $matchedTerms
                        )
                    )
            ]);
        }

        return $results

            ->sortByDesc('similarity')

            ->take($limit)

            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Find Similar Cases To Existing Case
    |--------------------------------------------------------------------------
    */

    public function findRelatedCases(
        ClinicalCase $clinicalCase,
        int $limit = 5
    ): Collection {

        $text = strtolower(

            $clinicalCase->clinical_findings .
            ' ' .
            $clinicalCase->necropsy_findings
        );

        $tokens = collect(

            preg_split(
                '/[\s,.;:]+/',
                $text
            )

        )
        ->filter()

        ->unique()

        ->take(30)

        ->values()

        ->toArray();

        return $this->findSimilarCases(

            $tokens,

            $clinicalCase->species_id,

            $limit

        )->reject(

            fn($item) =>

                $item['case']->id
                ===
                $clinicalCase->id
        )
        ->values();
    }
}