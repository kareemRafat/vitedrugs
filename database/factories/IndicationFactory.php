<?php

namespace Database\Factories;

use App\Models\Indication;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndicationFactory extends Factory
{
    protected $model = Indication::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'description' => fake()->sentence(),
            'description_ar' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
