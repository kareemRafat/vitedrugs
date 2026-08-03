<?php

namespace Database\Factories;

use App\Models\Drugs\Modifier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModifierFactory extends Factory
{
    protected $model = Modifier::class;

    public function definition(): array
    {
        $displayName = fake()->unique()->randomElement([
            'Bilateral',
            'Unilateral',
            'Acute',
            'Chronic',
            'Progressive',
            'Intermittent',
            'Mild',
            'Severe',
            'Generalized',
            'Localized',
            'Diffuse',
            'Profuse',
            'Copious',
            'Scanty',
            'Episodic',
            'Continuous',
            'Transient',
            'Recurrent',
            'Severe diffuse',
            'Mild intermittent',
        ]);

        return [
            'canonical_name' => strtolower(str_replace(' ', '_', $displayName)),
            'display_name' => $displayName,
            'type' => fake()->randomElement(['laterality', 'course', 'severity', 'temporal']),
            'modifier_group' => fake()->randomElement(['laterality', 'course', 'severity', 'temporal']),
            'is_noisy' => false,
        ];
    }
}
