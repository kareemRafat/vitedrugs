<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Species;
use App\Models\WithdrawalPeriod;
use Illuminate\Database\Seeder;

class WithdrawalPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $species = Species::all();

        Product::all()->each(function (Product $product) use ($species) {
            WithdrawalPeriod::factory()
                ->count(rand(1, 3))
                ->create([
                    'product_id' => $product->id,
                    'species_id' => $species->random()->id,
                ]);
        });
    }
}
