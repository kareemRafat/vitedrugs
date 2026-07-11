<?php

namespace App\Services\Imports;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductImportValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        Validator::make($data, [

            'trade_name' => ['required', 'string'],
            'company' => ['required', 'string'],
            'dosage_form' => ['required', 'string'],

            'active_ingredients' => ['array'],

            'indications' => ['array'],
            'contraindications' => ['array'],
            'precautions' => ['array'],
            'side_effects' => ['array'],

            'diseases' => ['array'],
            'dosages' => ['array'],
            'withdrawal_periods' => ['array'],

        ])->validate();
    }
}
