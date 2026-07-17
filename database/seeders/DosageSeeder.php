<?php

namespace Database\Seeders;

use App\Models\Dosage;
use App\Models\Product;
use App\Models\Species;
use Illuminate\Database\Seeder;

class DosageSeeder extends Seeder
{
    public function run(): void
    {
        $species = Species::all();

        Product::all()->each(function (Product $product) use ($species) {
            Dosage::factory()
                ->count(rand(1, 4))
                ->create([
                    'product_id' => $product->id,
                    'species_id' => $species->random()->id,
                ]);
        });
    }
}
