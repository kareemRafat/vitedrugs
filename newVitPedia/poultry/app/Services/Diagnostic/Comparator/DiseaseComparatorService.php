<?php

namespace App\Services\Diagnostic\Comparator;

use App\Models\Disease;
use Illuminate\Support\Collection;

class DiseaseComparatorService
{
    public function compare(
        Disease $diseaseA,
        Disease $diseaseB
    ): array {

        $this->loadRelationships($diseaseA);
        $this->loadRelationships($diseaseB);

        return [

            'disease_a' => $this->formatDisease(
                $diseaseA
            ),

            'disease_b' => $this->formatDisease(
                $diseaseB
            ),

            'species_comparison' => [

                'disease_a' => $diseaseA
                    ->species
                    ->pluck('name')
                    ->values(),

                'disease_b' => $diseaseB
                    ->species
                    ->pluck('name')
                    ->values(),
            ],

            'age_comparison' => [

                'disease_a' => $this->formatAgeGroups(
                    $diseaseA
                ),

                'disease_b' => $this->formatAgeGroups(
                    $diseaseB
                ),
            ],

            'mortality_comparison' => [

                'disease_a' => $this->formatMortality(
                    $diseaseA
                ),

                'disease_b' => $this->formatMortality(
                    $diseaseB
                ),
            ],

            'epidemiology_comparison' => [

                'disease_a' => $this->formatEpidemiology(
                    $diseaseA
                ),

                'disease_b' => $this->formatEpidemiology(
                    $diseaseB
                ),
            ],

            'transmission_comparison' => [

                'disease_a' => $diseaseA
                    ->transmissionRoutes
                    ->pluck('name')
                    ->values(),

                'disease_b' => $diseaseB
                    ->transmissionRoutes
                    ->pluck('name')
                    ->values(),
            ],

            'vaccination_comparison' => [

                'disease_a' => $this->formatVaccination(
                    $diseaseA
                ),

                'disease_b' => $this->formatVaccination(
                    $diseaseB
                ),
            ],

            'diagnostic_tests_comparison' => [

                'disease_a' => $diseaseA
                    ->diagnosticTests
                    ->pluck('name')
                    ->values(),

                'disease_b' => $diseaseB
                    ->diagnosticTests
                    ->pluck('name')
                    ->values(),
            ],

            'comparison' => $this->buildFeatureMatrix(
                $diseaseA,
                $diseaseB
            ),
        ];
    }

    protected function loadRelationships(
        Disease $disease
    ): void {

        $disease->loadMissing([

            'findings',

            'lesions',

            'species',

            'ageGroups',

            'mortalityProfile',

            'epidemiologyProfile',

            'transmissionRoutes',

            'diagnosticTests'
        ]);
    }

    protected function formatDisease(
        Disease $disease
    ): array {

        return [

            'id' => $disease->id,

            'name' => $disease->name,

            'slug' => $disease->slug,
        ];
    }

    protected function formatAgeGroups(
        Disease $disease
    ): Collection {

        return $disease->ageGroups
            ->map(fn ($group) => [

                'min_days' => $group->min_days,

                'max_days' => $group->max_days,
            ])
            ->values();
    }

    protected function formatMortality(
        Disease $disease
    ): array {

        return [

            'mortality_min' => optional(
                $disease->mortalityProfile
            )->expected_mortality_min,

            'mortality_max' => optional(
                $disease->mortalityProfile
            )->expected_mortality_max,

            'pattern' => optional(
                $disease->mortalityProfile
            )->mortality_pattern,
        ];
    }

    protected function formatEpidemiology(
        Disease $disease
    ): array {

        return [

            'outbreak_speed' => optional(
                $disease->epidemiologyProfile
            )->outbreak_speed,

            'common_seasons' => optional(
                $disease->epidemiologyProfile
            )->common_seasons,

            'unvaccinated' => optional(
                $disease->epidemiologyProfile
            )->common_in_unvaccinated,
        ];
    }

    protected function formatVaccination(
        Disease $disease
    ): string {

        return optional(
            $disease->epidemiologyProfile
        )->common_in_unvaccinated

            ? 'Vaccination Important'

            : 'Unknown';
    }

    protected function buildFeatureMatrix(
        Disease $diseaseA,
        Disease $diseaseB
    ): array {

        $features = collect()

            ->merge(
                $diseaseA->findings
                    ->pluck('display_name')
            )

            ->merge(
                $diseaseB->findings
                    ->pluck('display_name')
            )

            ->merge(
                $diseaseA->lesions
                    ->pluck('display_name')
            )

            ->merge(
                $diseaseB->lesions
                    ->pluck('display_name')
            )

            ->map(
                fn ($feature) => strtolower(
                    trim($feature)
                )
            )

            ->unique()

            ->sort()

            ->values();

        return $features
            ->map(function ($feature) use (
                $diseaseA,
                $diseaseB
            ) {

                return [

                    'feature' => ucfirst(
                        $feature
                    ),

                    'disease_a' => $this->hasFeature(
                        $diseaseA,
                        $feature
                    ),

                    'disease_b' => $this->hasFeature(
                        $diseaseB,
                        $feature
                    ),
                ];
            })
            ->values()
            ->all();
    }

    protected function hasFeature(
        Disease $disease,
        string $feature
    ): bool {

        $findings = $disease->findings
            ->pluck('display_name')
            ->map(
                fn ($finding) => strtolower(
                    trim($finding)
                )
            )
            ->toArray();

        $lesions = $disease->lesions
            ->pluck('display_name')
            ->map(
                fn ($lesion) => strtolower(
                    trim($lesion)
                )
            )
            ->toArray();

        return in_array(
            $feature,
            $findings
        ) || in_array(
            $feature,
            $lesions
        );
    }
}