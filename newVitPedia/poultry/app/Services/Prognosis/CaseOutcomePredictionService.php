<?php

namespace App\Services\Prognosis;

use App\Models\Disease;
use App\DataTransferObjects\DiagnosticContext;

class CaseOutcomePredictionService
{
    public function predict(
        Disease $disease,
        DiagnosticContext $context,
        int $confidence
    ): array {

        $mortalityRisk = 'Low';

        $prognosis = 'Good';

        $productionLoss = 'Minimal';

        $expectedDuration = 'Unknown';

        /*
        |--------------------------------------------------------------------------
        | Disease Mortality Profile
        |--------------------------------------------------------------------------
        */

        $profile = $disease->mortalityProfile;

        if ($profile) {

            $maxMortality =
                $profile->expected_mortality_max ?? 0;

            if ($maxMortality >= 50) {

                $mortalityRisk = 'Very High';

                $prognosis = 'Guarded';

                $productionLoss = 'Severe';

            } elseif ($maxMortality >= 20) {

                $mortalityRisk = 'High';

                $prognosis = 'Fair';

                $productionLoss = 'Moderate';

            } elseif ($maxMortality >= 5) {

                $mortalityRisk = 'Moderate';

            }

            if ($profile->outbreak_duration_days) {

                $expectedDuration =
                    $profile->outbreak_duration_days .
                    ' days';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Confidence Adjustment
        |--------------------------------------------------------------------------
        */

        if ($confidence < 50) {

            $prognosis =
                'Uncertain diagnosis';
        }

        /*
        |--------------------------------------------------------------------------
        | Flock Adjustment
        |--------------------------------------------------------------------------
        */

        if (
            $context->flockSize
            &&
            $context->deadBirds
        ) {

            $currentMortality = round(

                ($context->deadBirds
                    / $context->flockSize) * 100
            );

            if ($currentMortality > 20) {

                $mortalityRisk =
                    'Very High';

                $prognosis =
                    'Poor';
            }
        }

        return [

            'mortality_risk' =>
                $mortalityRisk,

            'expected_prognosis' =>
                $prognosis,

            'production_loss' =>
                $productionLoss,

            'expected_outbreak_duration' =>
                $expectedDuration,

            'confidence_in_prediction' =>
                $confidence
        ];
    }
}