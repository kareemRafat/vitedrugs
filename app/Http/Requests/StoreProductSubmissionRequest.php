<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'submitted_by_name' => ['required', 'string', 'max:255'],
            'submitted_by_email' => ['required', 'email', 'max:255'],
            'submitted_by_phone' => ['nullable', 'string', 'max:30'],
            'trade_name' => ['required', 'string', 'max:255'],
            'trade_name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'description_ar' => ['nullable', 'string', 'max:10000'],
            'product_type' => ['required', 'string', 'in:pharmaceutical,vaccine,supplement,feed_additive,disinfectant,biological'],
            'package_size' => ['nullable', 'string', 'max:255'],
            'storage_conditions' => ['nullable', 'string', 'max:2000'],
            'company' => ['required', 'string', 'max:255'],
            'dosage_form' => ['required', 'string', 'max:255'],
            'active_ingredients' => ['nullable', 'array'],
            'active_ingredients.*.name' => ['required_with:active_ingredients', 'string', 'max:255'],
            'active_ingredients.*.strength' => ['nullable', 'string', 'max:100'],
            'active_ingredients.*.unit' => ['nullable', 'string', 'max:50'],
            'diseases' => ['nullable', 'array'],
            'diseases.*' => ['string', 'max:255'],
            'indications' => ['nullable', 'array'],
            'indications.*' => ['string', 'max:5000'],
            'contraindications' => ['nullable', 'array'],
            'contraindications.*' => ['string', 'max:5000'],
            'precautions' => ['nullable', 'array'],
            'precautions.*' => ['string', 'max:5000'],
            'side_effects' => ['nullable', 'array'],
            'side_effects.*' => ['string', 'max:5000'],
            'dosages' => ['nullable', 'array'],
            'dosages.*.species' => ['required_with:dosages', 'string', 'max:255'],
            'dosages.*.dosage' => ['nullable', 'string', 'max:1000'],
            'dosages.*.route' => ['nullable', 'string', 'max:255'],
            'dosages.*.duration' => ['nullable', 'string', 'max:255'],
            'dosages.*.notes' => ['nullable', 'string', 'max:1000'],
            'withdrawal_periods' => ['nullable', 'array'],
            'withdrawal_periods.*.species' => ['required_with:withdrawal_periods', 'string', 'max:255'],
            'withdrawal_periods.*.meat_days' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'withdrawal_periods.*.milk_days' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'withdrawal_periods.*.egg_days' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'withdrawal_periods.*.notes' => ['nullable', 'string', 'max:1000'],
            'image_urls' => ['nullable', 'array'],
            'image_urls.*' => ['url', 'max:2000'],
            'documents' => ['nullable', 'array'],
            'documents.*.title' => ['required_with:documents', 'string', 'max:255'],
            'documents.*.url' => ['required_with:documents', 'url', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return __('messages.pages.products.submission.attributes');
    }
}
