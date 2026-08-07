<?php

namespace Database\Factories;

use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\Modifier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicalSignFactory extends Factory
{
    protected $model = ClinicalSign::class;

    public function definition(): array
    {
        $displayName = fake()->unique()->words(3, true);

        return [
            'anatomical_structure_id' => AnatomicalStructure::factory(),
            'finding_id' => Finding::factory(),
            'modifier_id' => fn () => Modifier::query()->inRandomOrder()->first()?->id ?? Modifier::factory(),
            'canonical_name' => strtolower(str_replace(' ', '_', $displayName)),
            'display_name' => $displayName,
            'stage' => fake()->randomElement(['clinical', 'subclinical', 'peracute', 'acute', 'chronic']),
            'severity_level_id' => null,
        ];
    }
}
