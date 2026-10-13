<?php

namespace App\Http\Requests\LargeAnimals;

use App\Http\Requests\Concerns\ScopesToSitePart;
use App\Models\LargeAnimals\ClinicalSign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitialDiagnosisRequest extends FormRequest
{
    use ScopesToSitePart;

    public function authorize(): bool
    {
        return true;
    }

    protected function part(): string
    {
        return 'large-animals';
    }

    public function rules(): array
    {
        return [
            'host_species_id' => ['required', $this->speciesExists()],
            'clinical_signs' => ['required', 'array', 'min:3'],
            'clinical_signs.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists(ClinicalSign::class, 'id'),
                $this->clinicalSignInPart(__('validation.diagnosis.sign_invalid')),
            ],
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
        ];
    }
}
