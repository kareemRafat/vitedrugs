<?php

namespace App\Services\Diagnostic;

use Illuminate\Support\Collection;

class DiagnosisTerminationService
{
    public function shouldStop(
        Collection $results,
        array $askedQuestions = []
    ): array {

        if ($results->isEmpty()) {

            return [
                'stop' => true,
                'reason' => 'No diagnosis found'
            ];
        }

        $top = $results->first();

        /*
        |--------------------------------------------------------------------------
        | High confidence reached
        |--------------------------------------------------------------------------
        */

        if ($top['confidence'] >= 90) {

            return [

                'stop' => true,

                'reason' =>
                    'High confidence diagnosis achieved'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Large gap between first and second disease
        |--------------------------------------------------------------------------
        */

        if ($results->count() >= 2) {

            $second = $results[1];

            $difference =
                $top['confidence']
                - $second['confidence'];

            if ($difference >= 25) {

                return [

                    'stop' => true,

                    'reason' =>
                        'Clear winner diagnosis'
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Too many questions asked
        |--------------------------------------------------------------------------
        */

        if (count($askedQuestions) >= 10) {

            return [

                'stop' => true,

                'reason' =>
                    'Maximum number of questions reached'
            ];
        }

        return [

            'stop' => false,

            'reason' => null
        ];
    }
}