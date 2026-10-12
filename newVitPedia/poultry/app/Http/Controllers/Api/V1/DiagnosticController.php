<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTransferObjects\DiagnosticContext;
use App\Services\Diagnostic\BayesianDiagnosticEngine;
use App\Services\Diagnostic\DifferentialDiagnosisService;
use App\Services\Diagnostic\AdaptiveQuestionService;
use App\Services\Diagnostic\QuestionRankingService;
use Throwable;

class DiagnosticController extends Controller
{
    public function __construct(
        protected BayesianDiagnosticEngine $engine,
        protected DifferentialDiagnosisService $differential,
        protected AdaptiveQuestionService $questions,
        protected QuestionRankingService $ranking
    ) {
    }

    public function diagnose(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Clinical Findings
            |--------------------------------------------------------------------------
            */

            'findings' => ['required', 'array'],
            'findings.*' => ['string'],

            'negative_findings' => ['nullable', 'array'],
            'negative_findings.*' => ['string'],

            /*
            |--------------------------------------------------------------------------
            | Patient Information
            |--------------------------------------------------------------------------
            */

            'species_id' => ['nullable', 'string'],

            'age_days' => [
                'nullable',
                'integer',
                'min:0'
            ],

            /*
            |--------------------------------------------------------------------------
            | Epidemiology
            |--------------------------------------------------------------------------
            */

            'vaccinated' => ['nullable', 'boolean'],

            'wet_litter' => ['nullable', 'boolean'],

            'poor_ventilation' => ['nullable', 'boolean'],

            'mixed_species' => ['nullable', 'boolean'],

            'new_birds_introduced' => ['nullable', 'boolean'],

            /*
            |--------------------------------------------------------------------------
            | Mortality Intelligence
            |--------------------------------------------------------------------------
            */

            'mortality_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],

            'mortality_pattern' => [
                'nullable',
                'in:peracute,acute,subacute,chronic'
            ],

            'sudden_deaths' => ['nullable', 'boolean'],

            'peak_day' => [
                'nullable',
                'integer',
                'min:0'
            ],

            /*
            |--------------------------------------------------------------------------
            | Geographic Intelligence
            |--------------------------------------------------------------------------
            */

            'country' => ['nullable', 'string'],

            'region' => ['nullable', 'string'],
        ]);

        try {

            /*
            |--------------------------------------------------------------------------
            | Build Diagnostic Context DTO
            |--------------------------------------------------------------------------
            */

            $context = new DiagnosticContext(

                findings:
                    $validated['findings'] ?? [],

                speciesId:
                    $validated['species_id'] ?? null,

                ageDays:
                    $validated['age_days'] ?? null,

                vaccinated:
                    $validated['vaccinated'] ?? null,

                wetLitter:
                    $validated['wet_litter'] ?? false,

                poorVentilation:
                    $validated['poor_ventilation'] ?? false,

                mixedSpecies:
                    $validated['mixed_species'] ?? false,

                newBirdsIntroduced:
                    $validated['new_birds_introduced'] ?? false,

                mortalityRate:
                    $validated['mortality_rate'] ?? null,

                mortalityPattern:
                    $validated['mortality_pattern'] ?? null,

                suddenDeaths:
                    $validated['sudden_deaths'] ?? false,

                peakDay:
                    $validated['peak_day'] ?? null,

                country:
                    $validated['country'] ?? 'Egypt',

                region:
                    $validated['region'] ?? null,

                month:
                    now()->month
            );

            /*
            |--------------------------------------------------------------------------
            | Diagnostic Engine
            |--------------------------------------------------------------------------
            */

            $results = $this->engine->diagnose(
                $context
            );

            /*
            |--------------------------------------------------------------------------
            | Differential Diagnosis
            |--------------------------------------------------------------------------
            */

            $differential =
                $this->differential
                    ->explain(
                        $results->toArray()
                    );

            /*
            |--------------------------------------------------------------------------
            | Adaptive Next Question
            |--------------------------------------------------------------------------
            */

            $nextQuestion =
                $this->ranking
                    ->rank($results);

            return response()->json([

                'success' => true,

                'count' => $results->count(),

                'next_question' => $nextQuestion,

                'differential' => $differential,

                'data' => $results,

            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Diagnostic engine failed.',

                'error' => app()->environment('local')
                    ? $e->getMessage()
                    : null,

            ], 500);
        }
    }
}

