<?php

namespace Database\Factories;

use App\Models\Drugs\Abbreviation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbbreviationFactory extends Factory
{
    protected $model = Abbreviation::class;

    public function definition(): array
    {
        return [
            'abbreviation' => strtoupper(fake()->unique()->lexify('????')),
            'full_term' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['diagnostic', 'clinical', 'laboratory', 'drug']),
        ];
    }
}
