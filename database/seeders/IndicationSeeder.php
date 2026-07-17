<?php

namespace Database\Seeders;

use App\Models\Indication;
use App\Models\Product;
use Illuminate\Database\Seeder;

class IndicationSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            Indication::factory()
                ->count(rand(2, 5))
                ->create(['product_id' => $product->id]);
        });
    }
}
