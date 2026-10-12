<?php

namespace App\Services\Diagnostic;

use App\Models\Disease;
use Illuminate\Support\Collection;
use App\DataTransferObjects\DiagnosticContext;
use App\Services\Diagnostic\Explanation\DiagnosticExplanationService;
use App\Services\Diagnostic\Epidemiology\EpidemiologicalPriorService;
use App\Services\Diagnostic\Geography\GeographicPriorService;
use App\Services\Diagnostic\Mortality\MortalityPriorService;
use App\Services\Diagnostic\Necropsy\NecropsyPriorService;
use App\Services\Diagnostic\Confidence\ConfidenceCalibrationService;
use App\Services\Diagnostic\Evidence\EvidenceAnalysisService;

class DiagnosticEngineService
{
    public function __construct(
        protected DiagnosticExplanationService $explainer,
        protected EpidemiologicalPriorService $epi,
        protected GeographicPriorService $geo,
        protected MortalityPriorService $mortality,
        protected NecropsyPriorService $necropsy,
        protected ConfidenceCalibrationService $confidence,
        protected EvidenceAnalysisService $evidence
    ) {}

    public function diagnose(
        DiagnosticContext $context
    ): Collection {

        $inputFindings = collect(
            $context->findings
        )
            ->map(function ($finding) {

                if (is_array($finding)) {

                    return strtolower(
                        trim(
                            $finding['canonical_name']
                                ?? ''
                        )
                    );
                }

                return strtolower(
                    trim((string) $finding)
                );
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $diseases = Disease::with([
            'findings',
            'lesions',
            'species',
            'ageGroups',
            'epidemiologyProfile',
            'regionalProfiles',
            'mortalityProfile'
        ])->get();

        $results = collect();

        foreach ($diseases as $disease) {

            $score = 0;

            $matchedFindings = [];
            $missingFindings = [];
            $reasons = [];

            /*
            |--------------------------------------------------------------------------
            | Clinical Findings Matching
            |--------------------------------------------------------------------------
            */

            foreach ($disease->findings as $finding) {

                $canonical = strtolower(
                    trim($finding->canonical_name)
                );

                if (! in_array($canonical, $inputFindings)) {
                    continue;
                }

                $pivot = $finding->pivot;

                $weight = $pivot->weight ?? 1;

                $score += $weight;

                if (($pivot->specificity ?? null) === 'high') {
                    $score += 3;
                }

                if (($pivot->specificity ?? null) === 'very_high') {
                    $score += 5;
                }

                if ($pivot->is_pathognomonic ?? false) {

                    $score += 10;

                    $reasons[] =
                        "{$finding->display_name} is pathognomonic.";
                }

                $matchedFindings[] =
                    $finding->display_name;

                $reasons[] =
                    "Matched finding: {$finding->display_name}";
            }

            /*
            |--------------------------------------------------------------------------
            | Skip diseases without matched findings
            |--------------------------------------------------------------------------
            */

            if (empty($matchedFindings)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Missing Important Findings
            |--------------------------------------------------------------------------
            */

            foreach ($disease->findings as $finding) {

                $canonical = strtolower(
                    trim($finding->canonical_name)
                );

                if (
                    ! in_array($canonical, $inputFindings)
                ) {

                    if (
                        ($finding->pivot->weight ?? 0) >= 8
                        ||
                        ($finding->pivot->is_pathognomonic ?? false)
                    ) {

                        $missingFindings[] =
                            $finding->display_name;
                    }
                }
            }

            /*
|--------------------------------------------------------------------------
| Species Matching
|--------------------------------------------------------------------------
*/

            if (!empty($context->speciesId)) {

                $speciesMatched = $disease
                    ->species
                    ->contains(
                        'id',
                        $context->speciesId
                    );

                /*
    |--------------------------------------------------------------
    | Disease species unknown -> neutral
    |--------------------------------------------------------------
    */

                if ($disease->species->isEmpty()) {

                    $reasons[] =
                        'Disease species not specified';
                } elseif ($speciesMatched) {

                    $score += 20;

                    $reasons[] =
                        'Species matched';
                } else {

                    /*
        |----------------------------------------------------------
        | Strong penalty only if disease has defined species
        |----------------------------------------------------------
        */

                    $score -= 50;

                    $reasons[] =
                        'Species mismatch';
                }
            }

            /*
|--------------------------------------------------------------------------
| Age Matching
|--------------------------------------------------------------------------
*/

            if (
                !empty($context->ageDays)
            ) {

                /*
    |--------------------------------------------------------------
    | No age profile -> neutral
    |--------------------------------------------------------------
    */

                if ($disease->ageGroups->isEmpty()) {

                    $reasons[] =
                        'Disease age profile unavailable';
                } else {

                    $ageMatched = false;

                    foreach (
                        $disease->ageGroups
                        as $group
                    ) {

                        if (
                            (
                                $group->min_days === null
                                ||
                                $context->ageDays >= $group->min_days
                            )
                            &&
                            (
                                $group->max_days === null
                                ||
                                $context->ageDays <= $group->max_days
                            )
                        ) {

                            $ageMatched = true;

                            break;
                        }
                    }

                    if ($ageMatched) {

                        $score += 15;

                        $reasons[] =
                            'Age matched';
                    } else {

                        $score -= 15;

                        $reasons[] =
                            'Age mismatch';
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Epidemiological Prior
            |--------------------------------------------------------------------------
            */
            $hasEpidemiologicalData =

                $context->vaccinated !== null
                ||
                $context->wetLitter
                ||
                $context->poorVentilation
                ||
                $context->mixedSpecies
                ||
                $context->newBirdsIntroduced
                ||
                $context->mortalityRate !== null;

            $epiScore = $this->epi->calculate(
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

            if ($hasEpidemiologicalData) {

                $score += $epiScore;

                if ($epiScore > 0) {

                    $reasons[] =
                        "Epidemiological profile favored this disease (+{$epiScore})";
                }
            }

            if ($epiScore > 0) {

                $reasons[] =
                    "Epidemiological profile favored this disease (+{$epiScore})";
            }

            /*
            |--------------------------------------------------------------------------
            | Geographic Prior
            |--------------------------------------------------------------------------
            */

            $geoScore = $this->geo->calculate(
                $disease,
                [
                    'country' => $context->country,
                    'month' => $context->month
                ]
            );

            $score += $geoScore;

            if (
                !empty($context->country)
                &&
                $geoScore > 0
            ) {

                $reasons[] =
                    "Regional prevalence favored this disease (+{$geoScore})";
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
                        'peak_day' => $context->peakDay
                    ]
                );

            $score += $mortalityScore;

            if (
                (
                    $context->mortalityRate !== null
                    ||
                    !empty($context->mortalityPattern)
                    ||
                    $context->suddenDeaths
                    ||
                    !empty($context->peakDay)
                )
                &&
                $mortalityScore > 0
            ) {

                $reasons[] =
                    "Mortality pattern strongly favored this disease (+{$mortalityScore})";
            }

            $necropsyResult =
                $this->necropsy->calculate(

                    $disease,

                    collect(
                        $context->lesions
                    )
                        ->map(
                            fn($l) => strtolower(trim($l))
                        )
                        ->toArray()
                );

            $score +=
                $necropsyResult['score'];

            $reasons = array_merge(

                $reasons,

                $necropsyResult['reasons']
            );


            $pathognomonicMatchedCount = 0;

            foreach ($disease->findings as $finding) {

                $canonical =
                    strtolower(
                        trim(
                            $finding->canonical_name
                        )
                    );

                if (
                    in_array(
                        $canonical,
                        $inputFindings
                    )
                    &&
                    ($finding->pivot->is_pathognomonic ?? false)
                ) {

                    $pathognomonicMatchedCount++;
                }
            }

            $necropsyMatches = count(

                array_intersect(

                    collect($context->lesions)
                        ->map(fn($l) => strtolower(trim($l)))
                        ->toArray(),

                    $disease->lesions
                        ->pluck('canonical_name')
                        ->map(fn($l) => strtolower(trim($l)))
                        ->toArray()
                )
            );
            $missingCriticalCount =
                count($missingFindings);

            /*
            |--------------------------------------------------------------------------
            | Confidence Calculation
            |--------------------------------------------------------------------------
            */

            $maxPossible = max(
                1,
                $disease->findings->sum(
                    fn($f) => ($f->pivot->weight ?? 1) + 15
                )
            );

            $confidence =
                $this->confidence->calculate(

                    score: $score,

                    maxPossibleScore: $maxPossible,

                    matchedFindingsCount: count($matchedFindings),

                    totalDiseaseFindings: $disease->findings->count(),

                    pathognomonicMatchedCount: $pathognomonicMatchedCount,

                    missingCriticalFindingsCount: $missingCriticalCount,

                    necropsyMatches: $necropsyMatches,

                    epidemiologyScore: $epiScore,

                    mortalityScore: $mortalityScore,

                    geographicScore: $geoScore,

                    differentialGap: 0
                );

            /*
            |--------------------------------------------------------------------------
            | AI Explanations
            |--------------------------------------------------------------------------
            */

            $explanations =
                $this->explainer->generate(
                    $matchedFindings,
                    $missingFindings,
                    $context->speciesId,
                    $context->ageDays
                );

            /*
            |--------------------------------------------------------------------------
            | Ignore weak matches
            |--------------------------------------------------------------------------
            */

            if ($confidence < 10) {
                continue;
            }

            $evidence =
                $this->evidence->analyze(

                    $disease,

                    $inputFindings
                );

            if ($score <= 0) {
                continue;
            }

            $results->push([

                'disease' => $disease,

                'score' => $score,

                'confidence' => $confidence,

                'matched_findings' => array_values(
                    array_unique($matchedFindings)
                ),

                'missing_findings' => array_slice(
                    array_values(
                        array_unique($missingFindings)
                    ),
                    0,
                    5
                ),

                'explanations' => array_values(
                    array_unique(
                        array_merge(
                            $reasons,
                            $explanations
                        )
                    )
                ),
                'supporting_evidence' =>
                $evidence['supporting_evidence'],

                'contradicting_evidence' =>
                $evidence['contradicting_evidence'],
            ]);
        }

        return $results
            ->sortByDesc('score')
            ->take(10)
            ->values();
    }
}
