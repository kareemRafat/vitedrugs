<?php

namespace App\Http\Requests\LargeAnimals;

use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host_species_id' => ['nullable', Rule::exists(HostSpecies::class, 'id')],
            'clinical_signs' => ['required', 'array', 'min:1'],
            'clinical_signs.*' => ['required', 'integer', 'distinct', Rule::exists(ClinicalSign::class, 'id')],
            'etiology_type' => ['nullable', Rule::in($this->validEtiologies())],
            'body_system_id' => ['nullable', Rule::exists(BodySystem::class, 'id')],
            'zoonotic' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'host_species_id.required' => __('validation.diagnosis.species_required'),
            'host_species_id.exists' => __('validation.diagnosis.species_invalid'),
            'clinical_signs.required' => __('validation.filter.signs_required'),
            'clinical_signs.min' => __('validation.filter.signs_min'),
            'clinical_signs.*.required' => __('validation.filter.sign_invalid'),
            'clinical_signs.*.integer' => __('validation.filter.sign_invalid'),
            'clinical_signs.*.exists' => __('validation.filter.sign_invalid'),
            'clinical_signs.*.distinct' => __('validation.filter.sign_duplicate'),
        ];
    }

    private function validEtiologies(): array
    {
        return DiseaseClassification::query()
            ->whereNotNull('etiology_type')
            ->distinct()
            ->pluck('etiology_type')
            ->all();
    }
}
