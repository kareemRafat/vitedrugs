<?php

namespace App\Services\Diagnostic\Questions;

use App\Models\Disease;

class DifferentialQuestionGeneratorService
{
    public function generate(array $results): ?array
    {
        /*
        |--------------------------------------------------------------------------
        | Need at least two diseases
        |--------------------------------------------------------------------------
        */

        if (count($results) < 2) {
            return null;
        }

        /** @var Disease $primaryDisease */
        $primaryDisease = $results[0]['disease'];

        /** @var Disease $secondaryDisease */
        $secondaryDisease = $results[1]['disease'];

        /*
        |--------------------------------------------------------------------------
        | Load relations if missing
        |--------------------------------------------------------------------------
        */

        $primaryDisease->loadMissing('findings');

        $secondaryDisease->loadMissing('findings');

        $primaryFindings = $primaryDisease
            ->findings
            ->keyBy('canonical_name');

        $secondaryFindings = $secondaryDisease
            ->findings
            ->keyBy('canonical_name');

        /*
        |--------------------------------------------------------------------------
        | Findings present only in primary disease
        |--------------------------------------------------------------------------
        */

        foreach ($primaryFindings as $canonical => $finding) {

            if (
                $secondaryFindings->has($canonical)
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Prefer pathognomonic findings
            |--------------------------------------------------------------------------
            */

            if (
                $finding->pivot->is_pathognomonic
            ) {

                return [

                    'question' =>

                    "Do you observe {$finding->display_name}?",

                    'canonical_name' =>
                    $finding->canonical_name,

                    'finding_type' =>
                    'clinical',

                    'favored_disease' =>
                    $primaryDisease->name,

                    'against_disease' =>
                    $secondaryDisease->name,

                    'importance' =>
                    'very_high'
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Otherwise choose highest weighted finding
        |--------------------------------------------------------------------------
        */

        $bestFinding = null;

        $highestWeight = 0;

        foreach ($primaryFindings as $canonical => $finding) {

            if (
                $secondaryFindings->has($canonical)
            ) {
                continue;
            }

            $weight =
                $finding->pivot->weight ?? 0;

            if ($weight > $highestWeight) {

                $highestWeight = $weight;

                $bestFinding = $finding;
            }
        }

        if ($bestFinding) {

            return [

                'question' =>

                "Do you observe {$bestFinding->display_name}?",

                'canonical_name' =>
                $bestFinding->canonical_name,

                'finding_type' =>
                'clinical',

                'favored_disease' =>
                $primaryDisease->name,

                'against_disease' =>
                $secondaryDisease->name,

                'importance' =>
                'high'
            ];
        }

        return null;
    }
}
