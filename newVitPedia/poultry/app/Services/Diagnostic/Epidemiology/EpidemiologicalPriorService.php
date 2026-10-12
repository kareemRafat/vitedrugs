<?php

namespace App\Services\Diagnostic\Epidemiology;

use App\Models\Disease;

class EpidemiologicalPriorService
{
    public function calculate(
        Disease $disease,
        array $context
    ): int {

        $profile =
            $disease->epidemiologyProfile;

        if (!$profile) {
            return 0;
        }

        $score = 0;

        /*
        |--------------------------------------------------------------------------
        | Age
        |--------------------------------------------------------------------------
        */

        if (
            isset($context['age_days'])
            &&
            $context['age_days']
        ) {

            if (
                $context['age_days']
                >= ($profile->min_age_days ?? 0)

                &&

                $context['age_days']
                <= ($profile->max_age_days ?? 999999)
            ) {

                $score += 15;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Vaccination
        |--------------------------------------------------------------------------
        */

        if (
            !empty($context['vaccinated'])
            &&
            $profile->common_in_unvaccinated
        ) {

            $score -= 15;
        }

        if (
            empty($context['vaccinated'])
            &&
            $profile->common_in_unvaccinated
        ) {

            $score += 15;
        }

        /*
        |--------------------------------------------------------------------------
        | Wet litter
        |--------------------------------------------------------------------------
        */

        if (
            !empty($context['wet_litter'])
            &&
            $profile->associated_with_wet_litter
        ) {

            $score += 10;
        }

        /*
        |--------------------------------------------------------------------------
        | Poor ventilation
        |--------------------------------------------------------------------------
        */

        if (
            !empty($context['poor_ventilation'])
            &&
            $profile->associated_with_poor_ventilation
        ) {

            $score += 10;
        }

        /*
        |--------------------------------------------------------------------------
        | New Birds
        |--------------------------------------------------------------------------
        */

        if (
            !empty($context['new_birds_introduced'])
            &&
            $profile->associated_with_new_birds
        ) {

            $score += 10;
        }

        /*
        |--------------------------------------------------------------------------
        | Mortality
        |--------------------------------------------------------------------------
        */

        if (
            isset($context['mortality_rate'])
            &&
            $profile->expected_mortality_min
        ) {

            if (

                $context['mortality_rate']
                >= $profile->expected_mortality_min

                &&

                $context['mortality_rate']
                <= $profile->expected_mortality_max
            ) {

                $score += 15;
            }
        }

        return $score;
    }
}