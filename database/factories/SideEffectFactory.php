<?php

namespace Database\Factories;

use App\Models\SideEffect;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SideEffectFactory extends Factory
{
    protected $model = SideEffect::class;

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
