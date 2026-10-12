<?php

namespace App\Services\Validation;

use App\Models\DiagnosticValidation;

class DiagnosticValidationService
{
    public function store(

        int $predictedDiseaseId,

        int $confirmedDiseaseId,

        int $rank = 1,

        ?float $confidence = null,

        ?string $notes = null

    ): DiagnosticValidation {

        return DiagnosticValidation::create([

            'predicted_disease_id' =>
                $predictedDiseaseId,

            'confirmed_disease_id' =>
                $confirmedDiseaseId,

            'predicted_rank' =>
                $rank,

            'predicted_confidence' =>
                $confidence,

            'is_correct' =>

                $predictedDiseaseId
                ===
                $confirmedDiseaseId,

            'notes' => $notes
        ]);
    }
}