<?php

namespace App\Services\Diagnostic;

use App\Models\DiagnosticSession;
use App\DataTransferObjects\DiagnosticContext;
use App\Services\Diagnostic\Questions\DifferentialQuestionGeneratorService;

class DiagnosticSessionService
{
    public function __construct(
        protected BayesianDiagnosticEngine $engine,
        protected QuestionRankingService $ranking,
        protected DiagnosisTerminationService $termination,
        protected DifferentialQuestionGeneratorService $questionGenerator
    ) {
    }

    public function create(array $data): DiagnosticSession
    {
        $session = DiagnosticSession::create([

            'species_id' => $data['species_id'] ?? null,

            'age_days' => $data['age_days'] ?? null,

            'positive_findings' => $data['findings'] ?? [],

            'negative_findings' => [],

            'asked_questions' => [],

            /*
            |--------------------------------------------------------------------------
            | Epidemiological Context
            |--------------------------------------------------------------------------
            */

            'vaccinated' =>
                $data['vaccinated'] ?? null,

            'wet_litter' =>
                $data['wet_litter'] ?? false,

            'poor_ventilation' =>
                $data['poor_ventilation'] ?? false,

            'mixed_species' =>
                $data['mixed_species'] ?? false,

            'new_birds_introduced' =>
                $data['new_birds_introduced'] ?? false,

            'mortality_rate' =>
                $data['mortality_rate'] ?? null,

            'mortality_pattern' =>
                $data['mortality_pattern'] ?? null,

            'sudden_deaths' =>
                $data['sudden_deaths'] ?? false,

            'peak_day' =>
                $data['peak_day'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Geographic Context
            |--------------------------------------------------------------------------
            */

            'country' =>
                $data['country'] ?? 'Egypt',

            'region' =>
                $data['region'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Necropsy Context
            |--------------------------------------------------------------------------
            */

            'lesions' =>
                $data['lesions'] ?? [],
        ]);

        return $this->recalculate($session);
    }

    public function answer(
        DiagnosticSession $session,
        string $finding,
        string $answer
    ): DiagnosticSession {

        $positive =
            $session->positive_findings ?? [];

        $negative =
            $session->negative_findings ?? [];

        $asked =
            $session->asked_questions ?? [];

        if (! in_array($finding, $asked)) {
            $asked[] = $finding;
        }

        if ($answer === 'yes') {

            if (! in_array($finding, $positive)) {
                $positive[] = $finding;
            }
        }

        if ($answer === 'no') {

            if (! in_array($finding, $negative)) {
                $negative[] = $finding;
            }
        }

        $session->update([

            'positive_findings' =>
                array_values(
                    array_unique($positive)
                ),

            'negative_findings' =>
                array_values(
                    array_unique($negative)
                ),

            'asked_questions' =>
                array_values(
                    array_unique($asked)
                )
        ]);

        return $this->recalculate($session);
    }

    public function recalculate(
        DiagnosticSession $session
    ): DiagnosticSession {

        $context = new DiagnosticContext(

            findings:
                $session->positive_findings ?? [],

            speciesId:
                $session->species_id,

            ageDays:
                $session->age_days,

            vaccinated:
                $session->vaccinated,

            wetLitter:
                $session->wet_litter ?? false,

            poorVentilation:
                $session->poor_ventilation ?? false,

            mixedSpecies:
                $session->mixed_species ?? false,

            newBirdsIntroduced:
                $session->new_birds_introduced ?? false,

            mortalityRate:
                $session->mortality_rate,

            mortalityPattern:
                $session->mortality_pattern,

            suddenDeaths:
                $session->sudden_deaths ?? false,

            peakDay:
                $session->peak_day,

            country:
                $session->country ?? 'Egypt',

            region:
                $session->region,

            month:
                now()->month,

            lesions:
                $session->lesions ?? []
        );

        /*
        |--------------------------------------------------------------------------
        | Run Diagnostic Engine
        |--------------------------------------------------------------------------
        */

        $results =
            $this->engine->diagnose(
                $context
            );

        /*
        |--------------------------------------------------------------------------
        | Check Termination
        |--------------------------------------------------------------------------
        */

        $termination =
            $this->termination->shouldStop(

                $results,

                $session->asked_questions ?? []
            );

        $nextQuestion = null;

        /*
        |--------------------------------------------------------------------------
        | Generate Next Question
        |--------------------------------------------------------------------------
        */

        if (! $termination['stop']) {

            /*
            |--------------------------------------------------------------------------
            | Differential Question First
            |--------------------------------------------------------------------------
            */

            $nextQuestion =
                $this->questionGenerator
                    ->generate(
                        $results->toArray()
                    );

            /*
            |--------------------------------------------------------------------------
            | Fallback to Ranking Engine
            |--------------------------------------------------------------------------
            */

            if (! $nextQuestion) {

                $nextQuestion =
                    $this->ranking
                        ->rank($results);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Persist Session State
        |--------------------------------------------------------------------------
        */

        $session->update([

            'current_results' =>
                $results->toArray(),

            'current_question' =>
                $nextQuestion,

            'is_finished' =>
                $termination['stop'],

            'termination_reason' =>
                $termination['reason']
        ]);

        $session->current_results =
            $results;

        $session->next_question =
            $nextQuestion;

        $session->termination_reason =
            $termination['reason'];

        return $session;
    }
}