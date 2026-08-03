<?php

namespace Database\Factories;

use App\Models\Drugs\Microorganism;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MicroorganismFactory extends Factory
{
    protected $model = Microorganism::class;

    public function definition(): array
    {
        $genus = fake()->unique()->word();
        $species = fake()->unique()->word();
        $name = $genus.' '.$species;

        return [
            'name' => $name,
            'normalized_name' => strtolower($name),
            'slug' => Str::slug($name),
            'kingdom' => fake()->randomElement(['Bacteria', 'Fungi', 'Viruses', 'Protozoa']),
            'phylum' => null,
            'class' => null,
            'order' => null,
            'family' => null,
            'genus' => $genus,
            'species' => $species,
            'microorganism_type' => fake()->randomElement(['bacteria', 'fungus', 'virus', 'parasite', 'other']),
            'is_pathogenic' => true,
            'searchable_text' => null,
            'json_data' => [
                'morphology' => fake()->sentence(),
                'staining' => fake()->randomElement(['gram-positive', 'gram-negative', 'acid-fast', 'n/a']),
            ],
            'source_json_file' => null,
            'tags' => fake()->randomElements(['gram-positive', 'gram-negative', 'zoonotic', 'intracellular'], 2),
        ];
    }
}
