<?php

namespace Database\Seeders;

use App\Models\Contraindication;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ContraindicationSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            Contraindication::factory()
                ->count(rand(1, 3))
                ->create(['product_id' => $product->id]);
        });
    }
}
