<?php

namespace Database\Factories;

use App\Models\LargeAnimals\Finding;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FindingFactory extends Factory
{
    protected $model = Finding::class;

    public function definition(): array
    {
        $displayName = fake()->unique()->words(2, true);

        return [
            'canonical_name' => strtolower(str_replace(' ', '_', $displayName)),
            'display_name' => $displayName,
            'category' => fake()->randomElement(['clinical', 'postmortem', 'laboratory', 'imaging']),
            'ontology_type' => fake()->randomElement(['sign', 'symptom', 'finding', 'lesion']),
            'parent_id' => null,
            'is_noisy' => false,
            'is_general_sign' => false,
            'slug' => Str::slug($displayName),
        ];
    }
}
