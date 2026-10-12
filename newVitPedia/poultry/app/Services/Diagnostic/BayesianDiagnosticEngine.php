<?php

namespace App\Services\Diagnostic;

use App\Models\Disease;
use Illuminate\Support\Collection;
use App\Services\Diagnostic\Epidemiology\EpidemiologicalPriorService;
use App\Services\Diagnostic\Geography\GeographicPriorService;
use App\Services\Diagnostic\Mortality\MortalityPriorService;
use App\DataTransferObjects\DiagnosticContext;

class BayesianDiagnosticEngine
{
    public function __construct(
        protected EpidemiologicalPriorService $epi,
        protected GeographicPriorService $geo,
        protected MortalityPriorService $mortality
    ) {}

    public function diagnose(
        DiagnosticContext $context
    ): Collection {

        $positiveFindings = collect($context->findings)
            ->map(fn($f) => strtolower(trim($f)))
            ->unique()
            ->values()
            ->toArray();

        $negativeFindings = collect(
            $context->negativeFindings ?? []
        )
            ->map(fn($f) => strtolower(trim($f)))
            ->unique()
            ->values()
            ->toArray();

        $diseases = Disease::with([
            'findings',
            'species',
            'ageGroups',
            'epidemiologyProfile',
            'regionalProfiles',
            'mortalityProfile'
        ])->get();

        $results = collect();

        foreach ($diseases as $disease) {

            /*
            |--------------------------------------------------------------------------
            | Prior Probability
            |--------------------------------------------------------------------------
            */

            $probability = 0.10;

            $matched = [];
            $missing = [];
            $reasons = [];

            /*
            |--------------------------------------------------------------------------
            | Species Prior
            |--------------------------------------------------------------------------
            */

            if ($context->speciesId) {

                $speciesMatched = $disease
                    ->species
                    ->contains('id', $context->speciesId);

                if ($speciesMatched) {

                    $probability *= 2.0;

                    $reasons[] =
                        'Species matched';
                } else {

                    $probability *= 0.30;

                    $reasons[] =
                        'Species mismatch';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Age Prior
            |--------------------------------------------------------------------------
            */

            if (
                $context->ageDays &&
                $disease->ageGroups->isNotEmpty()
            ) {

                $ageMatched = false;

                foreach ($disease->ageGroups as $group) {

                    if (
                        ($group->min_days === null
                            || $context->ageDays >= $group->min_days)

                        &&

                        ($group->max_days === null
                            || $context->ageDays <= $group->max_days)
                    ) {

                        $ageMatched = true;

                        break;
                    }
                }

                if ($ageMatched) {

                    $probability *= 1.5;

                    $reasons[] =
                        'Age matched';
                } else {

                    $probability *= 0.7;

                    $reasons[] =
                        'Age mismatch';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Findings Likelihoods
            |--------------------------------------------------------------------------
            */

            foreach ($disease->findings as $finding) {

                $canonical = strtolower(
                    trim($finding->canonical_name)
                );

                $found = in_array(
                    $canonical,
                    $positiveFindings
                );

                $explicitlyAbsent = in_array(
                    $canonical,
                    $negativeFindings
                );

                $pivot = $finding->pivot;

                if ($found) {

                    $matched[] =
                        $finding->display_name;

                    $reasons[] =
                        "Matched {$finding->display_name}";

                    if (
                        $pivot->likelihood_ratio_positive
                    ) {

                        $probability *= max(
                            1,
                            $pivot->likelihood_ratio_positive
                        );
                    } else {

                        $probability *= max(
                            1,
                            ($pivot->weight ?? 1) / 2
                        );
                    }

                    if (
                        $pivot->is_pathognomonic
                    ) {

                        $probability *= 3;

                        $reasons[] =
                            "{$finding->display_name} is pathognomonic.";
                    }
                } elseif ($explicitlyAbsent) {

                    if (
                        $pivot->is_pathognomonic
                    ) {

                        $probability *= 0.05;

                        $reasons[] =
                            "Absent pathognomonic finding: {$finding->display_name}";
                    } elseif (
                        $pivot->required_for_diagnosis
                    ) {

                        $probability *= 0.30;

                        $reasons[] =
                            "Absent important finding: {$finding->display_name}";
                    } elseif (
                        $pivot->likelihood_ratio_negative
                    ) {

                        $probability *=
                            $pivot->likelihood_ratio_negative;
                    }
                } else {

                    if (
                        $pivot->required_for_diagnosis
                    ) {

                        $probability *= 0.70;

                        $missing[] =
                            $finding->display_name;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Skip Diseases Without Matches
            |--------------------------------------------------------------------------
            */

            if (empty($matched)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Epidemiological Prior
            |--------------------------------------------------------------------------
            */

            $epiScore =
                $this->epi->calculate(
                    $disease,
                    [
                        'age_days' => $context->ageDays,
                        'vaccinated' => $context->vaccinated,
                        'wet_litter' => $context->wetLitter,
                        'poor_ventilation' => $context->poorVentilation,
                        'mixed_species' => $context->mixedSpecies,
                        'new_birds_introduced' => $context->newBirdsIntroduced,
                        'mortality_rate' => $context->mortalityRate,
                    ]
                );

            if ($epiScore > 0) {

                $probability *=
                    (1 + ($epiScore / 100));

                $reasons[] =
                    "Epidemiological profile favored this disease.";
            }

            /*
            |--------------------------------------------------------------------------
            | Geographic Prior
            |--------------------------------------------------------------------------
            */

            $geoScore =
                $this->geo->calculate(
                    $disease,
                    [
                        'country' => $context->country,
                        'month' => $context->month,
                    ]
                );

            if ($geoScore > 0) {

                $probability *=
                    (1 + ($geoScore / 100));

                $reasons[] =
                    "Regional prevalence favored this disease.";
            }

            /*
            |--------------------------------------------------------------------------
            | Mortality Prior
            |--------------------------------------------------------------------------
            */

            $mortalityScore =
                $this->mortality->calculate(
                    $disease,
                    [
                        'mortality_rate' => $context->mortalityRate,
                        'mortality_pattern' => $context->mortalityPattern,
                        'sudden_deaths' => $context->suddenDeaths,
                        'peak_day' => $context->peakDay,
                    ]
                );

            if ($mortalityScore > 0) {

                $probability *=
                    (1 + ($mortalityScore / 100));

                $reasons[] =
                    "Mortality pattern favored this disease.";
            }

            /*
            |--------------------------------------------------------------------------
            | Numerical Stability
            |--------------------------------------------------------------------------
            */

            $probability = max(
                0.00001,
                $probability
            );

            /*
            |--------------------------------------------------------------------------
            | Confidence
            |--------------------------------------------------------------------------
            */

            $confidence = round(

                min(
                    99.9,
                    max(
                        0,
                        $probability * 100
                    )
                ),

                1
            );

            if ($confidence < 5) {
                continue;
            }

            $results->push([

                'disease' => $disease,

                'confidence' => $confidence,

                'matched_findings' => array_values(
                    array_unique($matched)
                ),

                'missing_findings' => array_slice(

                    array_values(
                        array_unique($missing)
                    ),

                    0,
                    5
                ),

                'explanations' => array_values(
                    array_unique($reasons)
                )
            ]);
        }

        return $results
            ->sortByDesc('confidence')
            ->take(10)
            ->values();
    }
}
