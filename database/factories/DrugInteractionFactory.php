<?php

namespace Database\Factories;

use App\Models\ActiveIngredient;
use App\Models\DrugInteraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrugInteractionFactory extends Factory
{
    protected $model = DrugInteraction::class;

    public function definition(): array
    {
        return [
            'active_ingredient_id' => ActiveIngredient::factory(),
            'interacting_drug' => fake()->randomElement([
                'Tetracyclines', 'Macrolides', 'Fluoroquinolones',
                'Sulfonamides', 'Beta-lactams', 'Aminoglycosides',
                'NSAIDs', 'Corticosteroids', 'Diuretics',
            ]),
            'severity' => fake()->randomElement(['minor', 'moderate', 'major']),
            'effect' => fake()->sentence(),
            'recommendation' => fake()->sentence(),
        ];
    }
}
