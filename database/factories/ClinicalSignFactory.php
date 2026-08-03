<?php

namespace Database\Factories;

use App\Models\Drugs\AnatomicalStructure;
use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\Finding;
use App\Models\Drugs\Modifier;
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
            'modifier_id' => Modifier::factory(),
            'canonical_name' => strtolower(str_replace(' ', '_', $displayName)),
            'display_name' => $displayName,
            'stage' => fake()->randomElement(['clinical', 'subclinical', 'peracute', 'acute', 'chronic']),
            'severity_level_id' => null,
            'semantic_slug' => strtolower(str_replace(' ', '-', $displayName)),
        ];
    }
}
