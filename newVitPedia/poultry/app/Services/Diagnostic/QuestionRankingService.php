<?php

namespace App\Services\Diagnostic;

use Illuminate\Support\Collection;

class QuestionRankingService
{
    public function rank(Collection $results): ?array
    {
        if ($results->count() < 2) {
            return null;
        }

        $topDiseases = $results->take(5);

        $findingScores = [];

        foreach ($topDiseases as $result) {

            foreach ($result['disease']->findings as $finding) {

                $name = strtolower(
                    $finding->canonical_name
                );

                if (!isset($findingScores[$name])) {

                    $findingScores[$name] = [
                        'finding' => $finding,
                        'present' => 0,
                    ];
                }

                $findingScores[$name]['present']++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Information Gain Approximation
        |--------------------------------------------------------------------------
        |
        | Best question:
        | present in some diseases but not all
        |
        */

        $bestFinding = null;
        $bestScore = -1;

        foreach ($findingScores as $item) {

            $present = $item['present'];

            $absent =
                $topDiseases->count() - $present;

            if ($present == 0 || $absent == 0) {
                continue;
            }

            /*
            | Maximum discrimination when
            | split is near 50/50
            */

            $score = min(
                $present,
                $absent
            );

            if ($score > $bestScore) {

                $bestScore = $score;

                $bestFinding =
                    $item['finding'];
            }
        }

        if (!$bestFinding) {
            return null;
        }

        return [

            'question_type' => 'finding_presence',

            'canonical_name' =>
                $bestFinding->canonical_name,

            'display_name' =>
                $bestFinding->display_name,

            'body_system' =>
                $bestFinding->body_system,

            'question' =>
                "Does the bird have: "
                .$bestFinding->display_name
                .' ?',
        ];
    }
}