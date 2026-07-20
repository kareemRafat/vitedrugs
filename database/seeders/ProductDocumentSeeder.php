<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDocument;
use Illuminate\Database\Seeder;

class ProductDocumentSeeder extends Seeder
{
    public function run(): void
    {
        Product::all()->each(function (Product $product) {
            ProductDocument::factory()
                ->count(rand(0, 2))
                ->create(['product_id' => $product->id]);
        });
    }
}
