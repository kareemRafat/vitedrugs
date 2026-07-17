<?php

namespace Database\Seeders;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            ProductImage::factory()
                ->count(rand(0, 3))
                ->create(['product_id' => $product->id]);
        });
    }
}
