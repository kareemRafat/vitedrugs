<?php

namespace App\Services\Diagnostic\Explanation;

class DiagnosticExplanationService
{
    public function generate(
        array $matchedFindings,
        array $missingFindings,
        ?string $species = null,
        ?int $age = null
    ): array {

        $reasons = [];

        foreach ($matchedFindings as $finding) {

            $reasons[] =
                "{$finding} matched the disease profile.";
        }

        foreach (
            array_slice($missingFindings,0,3)
            as $finding
        ) {

            $reasons[] =
                "{$finding} was not observed which slightly reduces confidence.";
        }

        if ($species) {

            $reasons[] =
                "Species compatibility was considered ({$species}).";
        }

        if ($age) {

            $reasons[] =
                "Age compatibility was included in diagnostic scoring.";
        }

        return $reasons;
    }
}