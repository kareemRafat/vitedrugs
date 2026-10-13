<?php

namespace App\Http\Requests\LargeAnimals;

use App\Http\Requests\Concerns\ScopesToSitePart;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DiseaseClassification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterRequest extends FormRequest
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
            'host_species_id' => ['nullable', $this->speciesExists()],
            'clinical_signs' => ['required', 'array', 'min:1'],
            'clinical_signs.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists(ClinicalSign::class, 'id'),
                $this->clinicalSignInPart(__('validation.filter.sign_invalid')),
            ],
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

    /**
     * Etiology is free text in the database rather than an enum, so the allowed
     * list is read live. Restricted to this part's diseases, otherwise the
     * dropdown offers values that can never appear in the results.
     *
     * @return array<int, string>
     */
    private function validEtiologies(): array
    {
        return DiseaseClassification::query()
            ->whereNotNull('etiology_type')
            ->whereHas('disease', fn (Builder $disease): Builder => $disease->forDomain($this->domain()))
            ->distinct()
            ->pluck('etiology_type')
            ->all();
    }
}
