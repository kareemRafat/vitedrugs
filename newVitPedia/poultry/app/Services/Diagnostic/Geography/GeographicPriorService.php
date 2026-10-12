<?php

namespace App\Services\Diagnostic\Geography;

use App\Models\Disease;

class GeographicPriorService
{
    public function calculate(
        Disease $disease,
        array $context
    ): int {

        $score = 0;

        foreach (
            $disease->regionalProfiles
            as $profile
        ) {

            if (
                !empty($context['country'])
                &&
                strtolower($profile->country)
                === strtolower($context['country'])
            ) {

                $score +=
                    $profile->regional_weight;

                if ($profile->endemic) {

                    $score += 10;
                }
            }

            if (
                !empty($context['month'])
                &&
                is_array(
                    $profile->high_risk_months
                )
            ) {

                if (
                    in_array(
                        $context['month'],
                        $profile->high_risk_months
                    )
                ) {

                    $score += 5;
                }
            }
        }

        return $score;
    }
}