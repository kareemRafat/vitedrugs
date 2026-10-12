<?php

namespace App\Services\Diagnostic;

use Illuminate\Support\Collection;

class AdaptiveQuestionService
{
    public function generate(Collection $results): ?array
    {
        /*
        |--------------------------------------------------------------------------
        | Need at least 2 diseases
        |--------------------------------------------------------------------------
        */

        if ($results->count() < 2) {
            return null;
        }

        $topDiseases = $results->take(3);

        $allFindings = collect();

        foreach ($topDiseases as $result) {

            foreach ($result['disease']->findings as $finding) {

                $allFindings->push([
                    'finding' => $finding,
                    'disease' => $result['disease']->name
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Count finding occurrence
        |--------------------------------------------------------------------------
        */

        $grouped = $allFindings
            ->groupBy(fn ($item) =>
                strtolower(
                    $item['finding']->canonical_name
                )
            );

        /*
        |--------------------------------------------------------------------------
        | Best discriminator:
        | appears in some diseases but not all
        |--------------------------------------------------------------------------
        */

        foreach ($grouped as $findingName => $items) {

            $count = $items
                ->pluck('disease')
                ->unique()
                ->count();

            if (
                $count >= 1
                &&
                $count < $topDiseases->count()
            ) {

                $finding = $items->first()['finding'];

                return [

                    'question_type' => 'finding_presence',

                    'canonical_name' =>
                        $finding->canonical_name,

                    'question' =>
                        'Check for: '
                        .$finding->display_name,

                    'display_name' =>
                        $finding->display_name,

                    'body_system' =>
                        $finding->body_system
                ];
            }
        }

        return null;
    }
}