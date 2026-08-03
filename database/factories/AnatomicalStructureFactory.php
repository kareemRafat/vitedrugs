<?php

namespace Database\Factories;

use App\Models\Drugs\AnatomicalStructure;
use App\Models\Drugs\BodySystem;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnatomicalStructureFactory extends Factory
{
    protected $model = AnatomicalStructure::class;

    public function definition(): array
    {
        $displayName = fake()->unique()->words(2, true);

        return [
            'body_system_id' => BodySystem::factory(),
            'parent_id' => null,
            'canonical_name' => strtolower(str_replace(' ', '_', $displayName)),
            'display_name' => $displayName,
            'type' => fake()->randomElement(['organ', 'tissue', 'region', 'cavity']),
        ];
    }
}
