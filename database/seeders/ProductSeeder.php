<?php

namespace Database\Seeders;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\DosageForm;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $dosageForms = DosageForm::all();
        $companies = Company::all();
        $ingredients = ActiveIngredient::all();
        $diseases = Disease::all();

        $products = Product::factory()
            ->count(30)
            ->sequence(fn ($sequence) => [
                'dosage_form_id' => $dosageForms->random()->id,
            ])
            ->create();

        foreach ($products as $product) {
            $product->companies()->attach(
                $companies->random(rand(1, 2))->pluck('id')->mapWithKeys(fn ($id) => [
                    $id => ['role' => fake()->randomElement(['manufacturer', 'agent', 'distributor'])],
                ])
            );

            $product->activeIngredients()->attach(
                $ingredients->random(rand(1, 4))->pluck('id')->mapWithKeys(fn ($id) => [
                    $id => [
                        'strength' => fake()->randomElement(['50', '100', '250', '500', '1000']),
                        'unit' => fake()->randomElement(['mg', 'g', 'ml', 'IU', '%']),
                        'sort_order' => fake()->numberBetween(1, 5),
                    ],
                ])
            );

            $product->diseases()->attach(
                $diseases->random(rand(2, 5))->pluck('id')->mapWithKeys(fn ($id) => [
                    $id => ['sort_order' => fake()->numberBetween(1, 10)],
                ])
            );
        }
    }
}
