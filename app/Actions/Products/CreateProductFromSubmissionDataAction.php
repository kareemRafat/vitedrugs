<?php

namespace App\Actions\Products;

use App\Enums\ProductStatus;
use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\DosageForm;
use App\Models\Product;
use App\Models\Species;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateProductFromSubmissionDataAction
{
    public function execute(array $data, ?User $user = null, ProductStatus $status = ProductStatus::Pending): Product
    {
        return DB::transaction(function () use ($data, $user, $status) {

            $company = Company::firstOrCreate(
                ['slug' => Str::slug($data['company'])],
                ['name' => $data['company'], 'company_type' => 'manufacturer', 'is_active' => true]
            );

            $dosageForm = DosageForm::firstOrCreate(
                ['name' => $data['dosage_form']],
                ['slug' => Str::slug($data['dosage_form']), 'is_active' => true]
            );

            $product = Product::withoutGlobalScope('approved')->create([
                'trade_name' => $data['trade_name'],
                'trade_name_ar' => $data['trade_name_ar'] ?? null,
                'slug' => Str::slug($data['trade_name']),
                'dosage_form_id' => $dosageForm->id,
                'description' => $data['description'] ?? null,
                'description_ar' => $data['description_ar'] ?? null,
                'package_size' => $data['package_size'] ?? null,
                'storage_conditions' => $data['storage_conditions'] ?? null,
                'product_type' => $data['product_type'] ?? 'pharmaceutical',
                'is_active' => true,
                'status' => $status,
                'created_by' => $user?->id,
            ]);

            $product->companies()->syncWithoutDetaching([
                $company->id => ['role' => 'manufacturer'],
            ]);

            foreach ($data['active_ingredients'] ?? [] as $item) {
                $ingredient = ActiveIngredient::firstOrCreate(
                    ['name' => $item['name']],
                    ['slug' => Str::slug($item['name']), 'is_active' => true]
                );
                $product->activeIngredients()->syncWithoutDetaching([
                    $ingredient->id => [
                        'strength' => is_numeric($item['strength'] ?? null) ? $item['strength'] : null,
                        'unit' => $item['unit'] ?? null,
                    ],
                ]);
            }

            foreach ($data['indications'] ?? [] as $item) {
                $product->indications()->create(['description' => $item]);
            }

            foreach ($data['contraindications'] ?? [] as $item) {
                $product->contraindications()->create(['description' => $item]);
            }

            foreach ($data['precautions'] ?? [] as $item) {
                $product->precautions()->create(['description' => $item]);
            }

            foreach ($data['side_effects'] ?? [] as $item) {
                $product->sideEffects()->create(['description' => $item]);
            }

            foreach ($data['diseases'] ?? [] as $name) {
                $disease = Disease::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name), 'is_active' => true]
                );
                $product->diseases()->syncWithoutDetaching([$disease->id]);
            }

            foreach ($data['dosages'] ?? [] as $item) {
                $species = Species::where('name', $item['species'])->first();
                if (! $species) {
                    continue;
                }
                $product->dosages()->create([
                    'species_id' => $species->id,
                    'dosage' => $item['dosage'] ?? null,
                    'route' => $item['route'] ?? null,
                    'duration' => $item['duration'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            foreach ($data['withdrawal_periods'] ?? [] as $item) {
                $species = Species::where('name', $item['species'])->first();
                if (! $species) {
                    continue;
                }
                $product->withdrawalPeriods()->create([
                    'species_id' => $species->id,
                    'meat_days' => $item['meat_days'] ?? null,
                    'milk_days' => $item['milk_days'] ?? null,
                    'egg_days' => $item['egg_days'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            foreach ($data['image_urls'] ?? [] as $url) {
                if (! empty($url)) {
                    $product->images()->create(['image' => $url]);
                }
            }

            foreach ($data['documents'] ?? [] as $doc) {
                if (! empty($doc['title']) && ! empty($doc['url'])) {
                    $product->documents()->create([
                        'title' => $doc['title'],
                        'file_path' => $doc['url'],
                    ]);
                }
            }

            return $product;
        });
    }
}
