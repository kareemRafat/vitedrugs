<?php

namespace App\Actions\Products;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\DosageForm;
use App\Models\Product;
use App\Models\Species;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateProductFromSubmissionDataAction
{
    public function execute(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {

            $company = Company::firstOrCreate(
                ['slug' => Str::slug($data['company'])],
                ['name' => $data['company'], 'company_type' => 'manufacturer', 'is_active' => true]
            );

            $dosageForm = DosageForm::firstOrCreate(
                ['name' => $data['dosage_form']],
                ['slug' => Str::slug($data['dosage_form']), 'is_active' => true]
            );

            $product->update([
                'trade_name' => $data['trade_name'],
                'trade_name_ar' => $data['trade_name_ar'] ?? null,
                'slug' => (Str::slug($data['trade_name']) ?: 'product').'-'.Str::lower(Str::random(8)),
                'dosage_form_id' => $dosageForm->id,
                'description' => $data['description'] ?? null,
                'description_ar' => $data['description_ar'] ?? null,
                'package_size' => $data['package_size'] ?? null,
                'storage_conditions' => $data['storage_conditions'] ?? null,
                'product_type' => $data['product_type'] ?? 'pharmaceutical',
            ]);

            $product->companies()->sync([
                $company->id => ['role' => 'manufacturer'],
            ]);

            $ingredientIds = [];
            foreach ($data['active_ingredients'] ?? [] as $item) {
                $ingredient = ActiveIngredient::firstOrCreate(
                    ['name' => $item['name']],
                    ['slug' => Str::slug($item['name']), 'is_active' => true]
                );
                $ingredientIds[$ingredient->id] = [
                    'strength' => is_numeric($item['strength'] ?? null) ? $item['strength'] : null,
                    'unit' => $item['unit'] ?? null,
                ];
            }
            $product->activeIngredients()->sync($ingredientIds);

            $product->indications()->delete();
            foreach ($data['indications'] ?? [] as $item) {
                $product->indications()->create(['description' => $item]);
            }

            $product->contraindications()->delete();
            foreach ($data['contraindications'] ?? [] as $item) {
                $product->contraindications()->create(['description' => $item]);
            }

            $product->precautions()->delete();
            foreach ($data['precautions'] ?? [] as $item) {
                $product->precautions()->create(['description' => $item]);
            }

            $product->sideEffects()->delete();
            foreach ($data['side_effects'] ?? [] as $item) {
                $product->sideEffects()->create(['description' => $item]);
            }

            $diseaseIds = [];
            foreach ($data['diseases'] ?? [] as $name) {
                $disease = Disease::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name), 'is_active' => true]
                );
                $diseaseIds[] = $disease->id;
            }
            $product->diseases()->sync($diseaseIds);

            $product->dosages()->delete();
            foreach ($data['dosages'] ?? [] as $item) {
                $species = Species::firstOrCreate(
                    ['name' => $item['species']],
                    ['slug' => Str::slug($item['species']), 'name_ar' => $item['species'], 'is_active' => true]
                );
                $product->dosages()->create([
                    'species_id' => $species->id,
                    'dosage' => $item['dosage'] ?? null,
                    'route' => $item['route'] ?? null,
                    'duration' => $item['duration'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $product->withdrawalPeriods()->delete();
            foreach ($data['withdrawal_periods'] ?? [] as $item) {
                $species = Species::firstOrCreate(
                    ['name' => $item['species']],
                    ['slug' => Str::slug($item['species']), 'name_ar' => $item['species'], 'is_active' => true]
                );
                $product->withdrawalPeriods()->create([
                    'species_id' => $species->id,
                    'meat_days' => $item['meat_days'] ?? null,
                    'milk_days' => $item['milk_days'] ?? null,
                    'egg_days' => $item['egg_days'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $product->images()->delete();
            foreach ($data['image_urls'] ?? [] as $url) {
                if (! empty($url)) {
                    $product->images()->create(['image' => $url]);
                }
            }

            $product->documents()->delete();
            foreach ($data['documents'] ?? [] as $doc) {
                if (! empty($doc['title']) && ! empty($doc['url'])) {
                    $product->documents()->create([
                        'title' => $doc['title'],
                        'file_path' => $doc['url'],
                    ]);
                }
            }

            return $product->fresh();
        });
    }
}
