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

    public function messages(): array
    {
        return [
            'host_species_id.required' => __('validation.diagnosis.species_required'),
            'host_species_id.exists' => __('validation.diagnosis.species_invalid'),
            'clinical_signs.required' => __('validation.diagnosis.signs_required'),
            'clinical_signs.min' => __('validation.diagnosis.signs_min'),
            'clinical_signs.*.required' => __('validation.diagnosis.sign_invalid'),
            'clinical_signs.*.integer' => __('validation.diagnosis.sign_invalid'),
            'clinical_signs.*.exists' => __('validation.diagnosis.sign_invalid'),
            'clinical_signs.*.distinct' => __('validation.diagnosis.sign_duplicate'),
            'refinement_signs.*.integer' => __('validation.diagnosis.sign_invalid'),
            'refinement_signs.*.exists' => __('validation.diagnosis.sign_invalid'),
            'refinement_signs.*.distinct' => __('validation.diagnosis.sign_duplicate'),
        ];
    }
}
