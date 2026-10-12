<?php

namespace App\Services\Diagnostic;

use App\DataTransferObjects\DiagnosticContext;
use App\Services\Diagnostic\Similarity\DiseaseSimilarityService;
use App\Services\Diagnostic\Outbreak\FlockOutbreakService;
use App\Services\Prognosis\CaseOutcomePredictionService;


class DiagnosticReportService
{
    public function __construct(
        protected DiseaseSimilarityService $similarity,
        protected FlockOutbreakService $outbreak,
        protected CaseOutcomePredictionService $outcome
    ) {}

    public function generate(
        array $result,
        ?DiagnosticContext $context = null
    ): array {

        $disease = $result['disease'];

        /*
        |--------------------------------------------------------------------------
        | Outbreak Analysis
        |--------------------------------------------------------------------------
        */

        $outbreakAnalysis = null;

        if ($context) {

            $outbreakAnalysis =
                $this->outbreak
                ->analyze($context);
        }

        $outcomePrediction =
            $this->outcome->predict(

                $result['disease'],

                $context,

                $result['confidence']
            );

        /*
        |--------------------------------------------------------------------------
        | Similar Diseases
        |--------------------------------------------------------------------------
        */

        $similarDiseases = $this->similarity
            ->findSimilarDiseases($disease)
            ->map(function ($item) {

                return [

                    'id' => $item['disease']->id,

                    'name' => $item['disease']->name,

                    'slug' => $item['disease']->slug,

                    'similarity' => $item['similarity'],

                    'shared_findings' =>
                    $item['shared_findings'],

                    'shared_lesions' =>
                    $item['shared_lesions'],
                ];
            })
            ->values()
            ->toArray();

        return [

            /*
            |--------------------------------------------------------------------------
            | Final Diagnosis
            |--------------------------------------------------------------------------
            */

            'most_likely_diagnosis' => [

                'name' => $disease->name,

                'slug' => $disease->slug,

                'confidence' =>
                $result['confidence'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Explanation
            |--------------------------------------------------------------------------
            */

            'why_suggested' =>
            $result['explanations'] ?? [],

            'matched_findings' =>
            $result['matched_findings'] ?? [],

            'important_missing_findings' =>
            $result['missing_findings'] ?? [],

            'supporting_evidence' =>
            $result['supporting_evidence'] ?? [],

            'contradicting_evidence' =>
            $result['contradicting_evidence'] ?? [],

            /*
            |--------------------------------------------------------------------------
            | Confirmation Tests
            |--------------------------------------------------------------------------
            */

            'recommended_confirmation_tests' =>

            $disease->diagnosticTests
                ->map(function ($test) {

                    return [

                        'test_name' =>
                        $test->test_name,

                        'sample' =>
                        $test->sample,

                        'purpose' =>
                        $test->purpose,
                    ];
                })
                ->values()
                ->toArray(),

            /*
            |--------------------------------------------------------------------------
            | Differential Diagnoses
            |--------------------------------------------------------------------------
            */

            'differential_diagnoses' =>

            $disease->differentialDiagnoses
                ->pluck('name')
                ->values()
                ->toArray(),

            /*
            |--------------------------------------------------------------------------
            | Similar Diseases
            |--------------------------------------------------------------------------
            */

            'similar_diseases' =>
            $similarDiseases,

            /*
            |--------------------------------------------------------------------------
            | Outbreak Analysis
            |--------------------------------------------------------------------------
            */

            'outbreak_analysis' =>
            $outbreakAnalysis,


            'outcome_prediction' =>
            $outcomePrediction,

            /*
            |--------------------------------------------------------------------------
            | Recommendations
            |--------------------------------------------------------------------------
            */

            'recommended_actions' =>
            $this->generateActions($disease),
        ];
    }

    protected function generateActions($disease): array
    {
        $actions = [];

        foreach (
            $disease->preventionAndControl ?? []
            as $item
        ) {

            $actions[] = $item;
        }

        return array_slice(
            array_unique($actions),
            0,
            10
        );
    }
}
