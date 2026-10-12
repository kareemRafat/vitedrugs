<?php

namespace App\Services\Diagnostic\Confidence;

class ConfidenceCalibrationService
{
    public function calculate(
        int $score,
        int $maxPossibleScore,
        int $matchedFindingsCount,
        int $totalDiseaseFindings,
        int $pathognomonicMatchedCount = 0,
        int $missingCriticalFindingsCount = 0,
        int $necropsyMatches = 0,
        int $epidemiologyScore = 0,
        int $mortalityScore = 0,
        int $geographicScore = 0,
        int $differentialGap = 0
    ): int {

        /*
        |--------------------------------------------------------------------------
        | Base Confidence
        |--------------------------------------------------------------------------
        */

        $confidence = 0;

        if ($maxPossibleScore > 0) {

            $confidence =
                ($score / $maxPossibleScore) * 100;
        }

        /*
        |--------------------------------------------------------------------------
        | Coverage Bonus
        |--------------------------------------------------------------------------
        */

        $coverage = 0;

        if ($totalDiseaseFindings > 0) {

            $coverage =
                ($matchedFindingsCount
                / $totalDiseaseFindings) * 100;
        }

        $confidence += $coverage * 0.20;

        /*
        |--------------------------------------------------------------------------
        | Pathognomonic Bonus
        |--------------------------------------------------------------------------
        */

        $confidence +=
            $pathognomonicMatchedCount * 7;

        /*
        |--------------------------------------------------------------------------
        | Missing Critical Findings Penalty
        |--------------------------------------------------------------------------
        */

        $confidence -=
            $missingCriticalFindingsCount * 3;

        /*
        |--------------------------------------------------------------------------
        | Necropsy Bonus
        |--------------------------------------------------------------------------
        */

        $confidence +=
            $necropsyMatches * 4;

        /*
        |--------------------------------------------------------------------------
        | Epidemiology Bonus
        |--------------------------------------------------------------------------
        */

        $confidence +=
            min(10, $epidemiologyScore / 2);

        /*
        |--------------------------------------------------------------------------
        | Mortality Bonus
        |--------------------------------------------------------------------------
        */

        $confidence +=
            min(10, $mortalityScore / 2);

        /*
        |--------------------------------------------------------------------------
        | Geographic Bonus
        |--------------------------------------------------------------------------
        */

        $confidence +=
            min(5, $geographicScore / 2);

        /*
        |--------------------------------------------------------------------------
        | Differential Gap Bonus
        |--------------------------------------------------------------------------
        */

        $confidence +=
            min(10, $differentialGap / 5);

        /*
        |--------------------------------------------------------------------------
        | Clamp
        |--------------------------------------------------------------------------
        */

        return max(
            0,
            min(
                100,
                round($confidence)
            )
        );
    }
}

