<?php

namespace Database\Factories;

use App\Models\Alternative;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlternativeFactory extends Factory
{
    protected $model = Alternative::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'alternative_product_id' => Product::factory(),
            'type' => fake()->randomElement(['commercial', 'therapeutic', 'economic']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
