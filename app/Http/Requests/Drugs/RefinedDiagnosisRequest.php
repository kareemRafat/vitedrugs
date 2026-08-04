<?php

namespace App\Http\Requests\Drugs;

use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\HostSpecies;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RefinedDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host_species_id' => ['required', Rule::exists(HostSpecies::class, 'id')],
            'clinical_signs' => ['required', 'array', 'min:3'],
            'clinical_signs.*' => ['required', 'integer', 'distinct', Rule::exists(ClinicalSign::class, 'id')],
            'refinement_signs' => ['nullable', 'array'],
            'refinement_signs.*' => ['integer', 'distinct', Rule::exists(ClinicalSign::class, 'id')],
        ];
    }
}
