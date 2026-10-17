<?php

namespace Database\Factories;

use App\Enums\OntologyType;
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
            // Was `['sign', 'symptom', 'finding', 'lesion']`. Two of those four
            // are not values the application recognises: `symptom` and `finding`
            // matched neither the atlas filter (`= 'lesion'`) nor the body map's
            // test (`!== 'sign'` counted them as lesions), so a fixture built on
            // them was quietly testing a row no production page would ever see.
            'ontology_type' => fake()->randomElement(OntologyType::cases()),
            'parent_id' => null,
            'is_noisy' => false,
            'is_general_sign' => false,
            'slug' => Str::slug($displayName),
        ];
    }
}
