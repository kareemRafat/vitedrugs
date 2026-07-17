<?php

namespace Database\Seeders;

use App\Models\Precaution;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PrecautionSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            Precaution::factory()
                ->count(rand(1, 3))
                ->create(['product_id' => $product->id]);
        });
    }
}
