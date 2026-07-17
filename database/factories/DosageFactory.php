<?php

namespace Database\Factories;

use App\Models\Dosage;
use App\Models\Product;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class DosageFactory extends Factory
{
    protected $model = Dosage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'species_id' => Species::factory(),
            'dosage' => fake()->randomElement(['1 ml/50 kg', '2 mg/kg', '10 mg/kg', '5 ml/animal', '1 tablet/20 kg', '0.5 ml/kg', '15 mg/kg', '3 ml/100 kg']),
            'route' => fake()->randomElement(['IM', 'SC', 'IV', 'Oral', 'Topical', 'Intramammary', 'SC/IM']),
            'duration' => fake()->randomElement(['3-5 days', '5-7 days', 'Single dose', '10 days', '3 days', '7-10 days']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
