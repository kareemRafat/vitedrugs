<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticSession;
use App\Models\Disease;
use App\Services\Diagnostic\DiagnosticSessionService;
use App\Services\Diagnostic\DiagnosticReportService;
use Illuminate\Http\Request;

class DiagnosticSessionController extends Controller
{
    public function __construct(
        protected DiagnosticSessionService $service,
        protected DiagnosticReportService $reportService
    ) {}

    /**
     * Start new diagnostic session
     */
    public function start(Request $request)
    {
        $request->validate([
            'findings'     => ['required', 'array'],
            'findings.*'   => ['string'],
            'species_id'   => ['nullable'],
            'age_days'     => ['nullable', 'integer'],
            'vaccinated' => ['nullable', 'boolean'],
            'wet_litter' => ['nullable', 'boolean'],
            'poor_ventilation' => ['nullable', 'boolean'],
            'mixed_species' => ['nullable', 'boolean'],
        ]);

        $session = $this->service->create(
            $request->all()
        );

        return response()->json([
            'success' => true,

            'session_id' => $session->id,

            'next_question' =>
            $session->next_question ?? null,

            'results' =>
            $session->current_results ?? [],

            'message' =>
            'Diagnostic session started'
        ]);
    }

    /**
     * Submit answer to adaptive question
     */
    public function answer(Request $request)
    {
        $request->validate([
            'session_id' => ['required'],
            'finding'    => ['required', 'string'],
            'answer'     => ['required', 'in:yes,no,unknown'],
        ]);

        $session = DiagnosticSession::findOrFail(
            $request->session_id
        );

        $session = $this->service->answer(
            $session,
            $request->finding,
            $request->answer
        );

        $finalReport = null;

        if ($session->is_finished) {

            $topResult = collect(
                $session->current_results
            )->first();

            if ($topResult) {

                $topDisease = Disease::with([
                    'diagnosticTests',
                    'differentialDiagnoses'
                ])->find(
                    $topResult['disease']['id']
                );

                if ($topDisease) {

                    $topResult['disease'] = $topDisease;

                    $finalReport =
                        $this->reportService
                        ->generate(
                            $topResult,
                            $context ?? null
                        );
                }
            }
        }

        return response()->json([

            'success' => true,

            'is_finished' =>
            $session->is_finished,

            'termination_reason' =>
            $session->termination_reason ?? null,

            'next_question' =>
            $session->next_question ?? null,

            'results' =>
            $session->current_results ?? [],

            'final_report' =>
            $finalReport
        ]);
    }

    /**
     * Show session details
     */
    public function show(
        DiagnosticSession $session
    ) {
        return response()->json([

            'success' => true,

            'data' => [

                'id' =>
                $session->id,

                'species_id' =>
                $session->species_id,

                'age_days' =>
                $session->age_days,

                'positive_findings' =>
                $session->positive_findings,

                'negative_findings' =>
                $session->negative_findings,

                'asked_questions' =>
                $session->asked_questions,

                'current_results' =>
                $session->current_results,

                'is_finished' =>
                $session->is_finished,
            ]
        ]);
    }
}
