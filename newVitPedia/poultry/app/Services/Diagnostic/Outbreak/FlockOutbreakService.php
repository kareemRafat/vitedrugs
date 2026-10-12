<?php

namespace App\Services\Diagnostic\Outbreak;

use App\DataTransferObjects\DiagnosticContext;

class FlockOutbreakService
{
    public function analyze(
        DiagnosticContext $context
    ): array {

        $alerts = [];

        /*
        |--------------------------------------------------------------------------
        | Morbidity
        |--------------------------------------------------------------------------
        */

        if (
            $context->flockSize
            &&
            $context->affectedBirds
        ) {

            $morbidity = round(

                ($context->affectedBirds
                    / $context->flockSize)

                * 100,

                1
            );

            if ($morbidity > 50) {

                $alerts[] =
                    "High morbidity outbreak detected ({$morbidity}%).";
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Mortality
        |--------------------------------------------------------------------------
        */

        if (
            $context->flockSize
            &&
            $context->deadBirds
        ) {

            $mortality = round(

                ($context->deadBirds
                    / $context->flockSize)

                * 100,

                1
            );

            if ($mortality > 10) {

                $alerts[] =
                    "High mortality outbreak detected ({$mortality}%).";
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Production
        |--------------------------------------------------------------------------
        */

        if (
            $context->productionDrop
            &&
            $context->productionDrop > 10
        ) {

            $alerts[] =
                "Significant production drop observed.";
        }

        /*
        |--------------------------------------------------------------------------
        | Biosecurity
        |--------------------------------------------------------------------------
        */

        if ($context->newBirdsIntroduced) {

            $alerts[] =
                "Recent bird introduction increases infectious disease risk.";
        }

        if ($context->mixedSpecies) {

            $alerts[] =
                "Mixed species rearing may facilitate disease transmission.";
        }

        if ($context->wetLitter) {

            $alerts[] =
                "Wet litter strongly favors enteric and respiratory diseases.";
        }

        return [

            'alerts' => $alerts,

            'morbidity_rate' =>

                $context->flockSize && $context->affectedBirds

                    ? round(
                        ($context->affectedBirds
                            / $context->flockSize) * 100,
                        1
                    )

                    : null,

            'mortality_rate' =>

                $context->flockSize && $context->deadBirds

                    ? round(
                        ($context->deadBirds
                            / $context->flockSize) * 100,
                        1
                    )

                    : null
        ];
    }
}