<?php

namespace App\Services\Diagnostic\Evidence;

use App\Models\Disease;

class EvidenceAnalysisService
{
    public function analyze(
        Disease $disease,
        array $userFindings
    ): array {

        $supporting = [];

        $against = [];

        $userFindings = collect($userFindings)
            ->map(fn($f) => strtolower(trim($f)))
            ->toArray();

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
                    $userFindings
                )
            ) {

                $supporting[] =
                    $finding->display_name;

            } else {

                if (

                    ($finding->pivot->weight ?? 0) >= 8

                    ||

                    ($finding->pivot->is_pathognomonic ?? false)

                ) {

                    $against[] =
                        $finding->display_name;
                }
            }
        }

        return [

            'supporting_evidence' =>
                array_values(
                    array_unique(
                        $supporting
                    )
                ),

            'contradicting_evidence' =>
                array_values(
                    array_unique(
                        $against
                    )
                )
        ];
    }
}