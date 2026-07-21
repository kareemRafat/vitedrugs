<?php

namespace App\Console\Commands;

use App\Enums\ProductStatus;
use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportDrugVetProducts extends Command
{
    protected $signature =
        'drugvet:import {file}';

    protected $description =
        'Import DrugVet products';

    public function handle()
    {
        $file = $this->argument('file');

        $products = json_decode(
            File::get($file),
            true
        );

        $imported = 0;

        foreach ($products as $item) {

            /*
            |--------------------------------------------------------------------------
            | Ignore duplicate products
            |--------------------------------------------------------------------------
            */

            $tradeName = trim(
                $item['trade_name']
            );

            $slug = Str::slug($tradeName);

            if (
                Product::withoutGlobalScope('approved')
                    ->where('slug', $slug)
                    ->exists()
            ) {

                $this->warn(
                    "Skipped: {$tradeName}"
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Company
            |--------------------------------------------------------------------------
            */

            $companyName = trim(
                $item['company']
            );

            $company = Company::firstOrCreate(
                [
                    'slug' => Str::slug(
                        $companyName
                    ),
                ],
                [
                    'name' => $companyName,
                    'company_type' => 'manufacturer',
                    'is_active' => true,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            $product = Product::withoutGlobalScope('approved')->create([
                'trade_name' => $tradeName,
                'slug' => $slug,
                'product_type' => 'pharmaceutical',
                'is_active' => true,
                'status' => ProductStatus::Approved,
            ]);

            $product->companies()->syncWithoutDetaching([
                $company->id => ['role' => 'manufacturer'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Ingredients
            |--------------------------------------------------------------------------
            */

            $ingredients = [];

            if (! isset($item['active_ingredient'])) {

                $this->warn(
                    "No active ingredient for {$tradeName}"
                );

                continue;
            }

            if (is_string($item['active_ingredient'])) {

                if (! empty(trim($item['active_ingredient']))) {

                    $ingredients[] = [
                        'name' => $item['active_ingredient'],
                        'strength' => $item['strength'] ?? null,
                    ];
                }
            } elseif (is_array($item['active_ingredient'])) {

                $ingredients = array_filter(
                    $item['active_ingredient'],
                    fn ($ingredient) => ! empty($ingredient['name'] ?? null)
                );
            }

            if (empty($ingredients)) {

                $this->warn(
                    "Empty active ingredient for {$tradeName}"
                );

                continue;
            }

            foreach (
                $ingredients as $order => $ingredientData
            ) {

                $ingredientName =
                    trim(
                        $ingredientData['name']
                    );

                $ingredient =
                    ActiveIngredient::firstOrCreate(
                        [
                            'slug' => Str::slug(
                                $ingredientName
                            ),
                        ],
                        [
                            'name' => ucfirst(
                                strtolower(
                                    $ingredientName
                                )
                            ),

                            'is_active' => true,
                        ]
                    );

                $strength = null;

                $unit = null;

                if (
                    ! empty($ingredientData['strength'])
                ) {

                    preg_match(
                        '/([\d\.]+)/',
                        $ingredientData['strength'],
                        $number
                    );

                    $strength =
                        $number[1]
                        ?? null;

                    $unit =
                        trim(
                            preg_replace(
                                '/^[\d\.]+\s*/',
                                '',
                                $ingredientData['strength']
                            )
                        );
                }

                $product
                    ->activeIngredients()
                    ->syncWithoutDetaching([
                        $ingredient->id => [

                            'strength' => $strength,

                            'unit' => $unit,

                            'sort_order' => $order,
                        ],
                    ]);
            }

            $imported++;

            $this->info(
                "Imported: {$tradeName}"
            );
        }

        $this->info(
            "Done. Imported {$imported} products"
        );
    }
}
