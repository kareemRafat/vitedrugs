<?php

namespace App\Services\Diagnostic\Mortality;

use App\Models\Disease;

class MortalityPriorService
{
    public function calculate(
        Disease $disease,
        array $context
    ): int {

        $profile =
            $disease->mortalityProfile;

        if (!$profile) {
            return 0;
        }

        $score = 0;

        /*
        |--------------------------------------------------------------------------
        | Mortality %
        |--------------------------------------------------------------------------
        */

        if (
            isset($context['mortality_rate'])
            &&
            $profile->expected_mortality_min !== null
        ) {

            if (

                $context['mortality_rate']
                >= $profile->expected_mortality_min

                &&

                $context['mortality_rate']
                <= $profile->expected_mortality_max
            ) {

                $score += 20;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pattern
        |--------------------------------------------------------------------------
        */

        if (
            !empty($context['mortality_pattern'])
            &&
            $profile->mortality_pattern
            === $context['mortality_pattern']
        ) {

            $score += 15;
        }

        /*
        |--------------------------------------------------------------------------
        | Sudden Death
        |--------------------------------------------------------------------------
        */

        if (

            !empty($context['sudden_deaths'])

            &&

            $profile->sudden_deaths
        ) {

            $score += 15;
        }

        /*
        |--------------------------------------------------------------------------
        | Peak Day
        |--------------------------------------------------------------------------
        */

        if (
            isset($context['peak_day'])
            &&
            $profile->peak_day_min
        ) {

            if (

                $context['peak_day']
                >= $profile->peak_day_min

                &&

                $context['peak_day']
                <= $profile->peak_day_max
            ) {

                $score += 10;
            }
        }

        return $score;
    }
}