<?php

namespace Database\Seeders;

use App\Models\Alternative;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AlternativeSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $alternatives = $products->where('id', '!=', $product->id)->random(rand(1, 3));

            foreach ($alternatives as $alt) {
                Alternative::factory()->create([
                    'product_id' => $product->id,
                    'alternative_product_id' => $alt->id,
                    'type' => fake()->randomElement(['commercial', 'therapeutic', 'economic']),
                ]);
            }
        }
    }
}
