<?php

namespace Database\Factories;

use App\Models\WithdrawalPeriod;
use App\Models\Product;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawalPeriodFactory extends Factory
{
    protected $model = WithdrawalPeriod::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'species_id' => Species::factory(),
            'meat_days' => fake()->randomElement([0, 3, 5, 7, 10, 14, 21, 28]),
            'milk_days' => fake()->randomElement([0, 1, 3, 5, 7, 10, 14]),
            'egg_days' => fake()->randomElement([0, 1, 3, 5, 7, 10]),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
