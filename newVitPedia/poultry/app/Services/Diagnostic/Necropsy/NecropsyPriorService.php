<?php

namespace App\Services\Diagnostic\Necropsy;

use App\Models\Disease;

class NecropsyPriorService
{
    public function calculate(
        Disease $disease,
        array $lesions
    ): array {

        $score = 0;

        $reasons = [];

        foreach ($disease->lesions as $lesion) {

            $canonical = strtolower(
                trim(
                    $lesion->canonical_name
                )
            );

            if (
                ! in_array(
                    $canonical,
                    $lesions
                )
            ) {
                continue;
            }

            $pivot = $lesion->pivot;

            $score +=
                ($pivot->weight ?? 5);

            if (
                ($pivot->specificity ?? null)
                === 'high'
            ) {

                $score += 5;
            }

            if (
                ($pivot->specificity ?? null)
                === 'very_high'
            ) {

                $score += 10;
            }

            if (
                $pivot->is_pathognomonic
            ) {

                $score += 20;

                $reasons[] =
                    "{$lesion->display_name} is pathognomonic.";
            }

            if (
                $pivot->is_confirmatory
            ) {

                $score += 25;

                $reasons[] =
                    "{$lesion->display_name} strongly confirms this disease.";
            }

            $reasons[] =
                "Necropsy lesion matched: {$lesion->display_name}";
        }

        return [

            'score' => $score,

            'reasons' => $reasons
        ];
    }
}