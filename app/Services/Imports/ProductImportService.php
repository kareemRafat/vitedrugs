<?php

namespace App\Services\Imports;

use App\Models\Product;
use App\Models\Company;
use App\Models\Disease;
use App\Models\Species;
use App\Models\DosageForm;
use App\Models\Indication;
use App\Models\Precaution;
use App\Models\SideEffect;
use App\Models\Contraindication;
use App\Models\ActiveIngredient;
use App\Models\WithdrawalPeriod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductImportService
{
    public function import(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            $company = Company::firstOrCreate(
                [
                    'slug' => Str::slug($data['company']),
                ],
                [
                    'name' => $data['company'],
                    'company_type' => 'manufacturer',
                    'is_active' => true,
                ]
            );

            $dosageForm = DosageForm::firstOrCreate(
                [
                    'name' => $data['dosage_form'],
                ],
                [
                    'slug' => Str::slug($data['dosage_form']),
                    'is_active' => true,
                ]
            );

            $product = Product::firstOrCreate(
                [
                    'trade_name' => $data['trade_name'],
                ],
                [
                    'company_id' => $company->id, // Legacy
                    'trade_name_ar' => $data['trade_name_ar'] ?? null,
                    'slug' => Str::slug($data['trade_name']),
                    'dosage_form_id' => $dosageForm->id,
                    'description' => $data['description'] ?? null,
                    'description_ar' => $data['description_ar'] ?? null,
                    'package_size' => $data['package_size'] ?? null,
                    'storage_conditions' => $data['storage_conditions'] ?? null,
                    'product_type' => $data['product_type'] ?? 'pharmaceutical',
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Cleanup old imported relations
            |--------------------------------------------------------------------------
            */

            $product->activeIngredients()->detach();

            $product->indications()->delete();

            $product->contraindications()->delete();

            $product->precautions()->delete();

            $product->sideEffects()->delete();

            $product->dosages()->delete();

            $product->withdrawalPeriods()->delete();

            /*
            |--------------------------------------------------------------------------
            | Companies
            |--------------------------------------------------------------------------
            */

            $product->companies()->syncWithoutDetaching([
                $company->id => [
                    'role' => 'manufacturer',
                ],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Active Ingredients
            |--------------------------------------------------------------------------
            */

            foreach ($data['active_ingredients'] ?? [] as $item) {

                $ingredient = ActiveIngredient::firstOrCreate(
                    [
                        'name' => $item['name'],
                    ],
                    [
                        'slug' => Str::slug($item['name']),
                        'is_active' => true,
                    ]
                );

                $product->activeIngredients()->syncWithoutDetaching([
                    $ingredient->id => [
                        'strength' => is_numeric($item['strength'] ?? null)
                            ? $item['strength']
                            : null,
                        'unit' => $item['unit'] ?: null,
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Indications
            |--------------------------------------------------------------------------
            */

            foreach ($data['indications'] ?? [] as $item) {

                Indication::updateOrCreate([
                    'product_id' => $product->id,
                    'description' => $item,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Contraindications
            |--------------------------------------------------------------------------
            */

            foreach ($data['contraindications'] ?? [] as $item) {

                Contraindication::updateOrCreate([
                    'product_id' => $product->id,
                    'description' => $item,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Precautions
            |--------------------------------------------------------------------------
            */

            foreach ($data['precautions'] ?? [] as $item) {

                Precaution::updateOrCreate([
                    'product_id' => $product->id,
                    'description' => $item,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Side Effects
            |--------------------------------------------------------------------------
            */

            foreach ($data['side_effects'] ?? [] as $item) {

                SideEffect::updateOrCreate([
                    'product_id' => $product->id,
                    'description' => $item,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Diseases
            |--------------------------------------------------------------------------
            */

            foreach ($data['diseases'] ?? [] as $name) {

                $disease = Disease::firstOrCreate(
                    [
                        'name' => $name,
                    ],
                    [
                        'slug' => Str::slug($name),
                        'is_active' => true,
                    ]
                );

                $product->diseases()->syncWithoutDetaching([
                    $disease->id,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Dosages
            |--------------------------------------------------------------------------
            */

            foreach ($data['dosages'] ?? [] as $item) {

                $species = Species::where(
                    'name',
                    $item['species']
                )->first();

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

            /*
            |--------------------------------------------------------------------------
            | Withdrawal Periods
            |--------------------------------------------------------------------------
            */

            foreach ($data['withdrawal_periods'] ?? [] as $item) {

                $species = Species::where(
                    'name',
                    $item['species']
                )->first();

                if (! $species) {
                    continue;
                }

                WithdrawalPeriod::updateOrCreate([
                    'product_id' => $product->id,
                    'species_id' => $species->id,
                    'meat_days' => $item['meat_days'] ?? null,
                    'milk_days' => $item['milk_days'] ?? null,
                    'egg_days' => $item['egg_days'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */

            foreach ($data['images'] ?? [] as $image) {

                $product->images()->firstOrCreate([
                    'image_url' => $image,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Documents
            |--------------------------------------------------------------------------
            */

            foreach ($data['documents'] ?? [] as $doc) {

                $product->documents()->firstOrCreate([
                    'title' => $doc['title'],
                    'document_url' => $doc['url'],
                ]);
            }

            return $product;
        });
    }
}
