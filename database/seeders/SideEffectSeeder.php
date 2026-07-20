<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SideEffect;
use Illuminate\Database\Seeder;

class SideEffectSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            SideEffect::factory()
                ->count(rand(1, 4))
                ->create(['product_id' => $product->id]);
        });
    }
}
