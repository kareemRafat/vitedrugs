<?php

namespace Database\Factories;

use App\Models\Drugs\DifferentialSyndrome;
use Illuminate\Database\Eloquent\Factories\Factory;

class DifferentialSyndromeFactory extends Factory
{
    protected $model = DifferentialSyndrome::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Respiratory Syndrome',
                'Enteric Syndrome',
                'Nervous Syndrome',
                'Reproductive Syndrome',
                'Systemic Febrile Syndrome',
                'Dermatologic Syndrome',
                'Musculoskeletal Syndrome',
            ]),
            'description' => fake()->sentence(),
        ];
    }
}
