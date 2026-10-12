<?php

namespace App\Services\Diagnostic;

class DifferentialDiagnosisService
{
    public function explain(array $results): array
    {
        if (count($results) < 2) {
            return [];
        }

        $primary = $results[0];
        $secondary = $results[1];

        $primaryMatched = collect(
            $primary['matched_findings']
        );

        $secondaryMatched = collect(
            $secondary['matched_findings']
        );

        $primaryMissing = collect(
            $primary['missing_findings'] ?? []
        );

        $secondaryMissing = collect(
            $secondary['missing_findings'] ?? []
        );

        return [

            'favored_disease' =>
                $primary['disease']->name,

            'less_likely_disease' =>
                $secondary['disease']->name,

            'reasons_favoring_primary' =>

                $primaryMatched
                    ->diff($secondaryMatched)
                    ->values()
                    ->toArray(),

            'reasons_against_secondary' =>

                $secondaryMissing
                    ->take(5)
                    ->values()
                    ->toArray(),
        ];
    }
}