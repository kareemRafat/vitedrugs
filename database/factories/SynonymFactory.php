<?php

namespace Database\Factories;

use App\Models\Drugs\Finding;
use App\Models\Drugs\Synonym;
use Illuminate\Database\Eloquent\Factories\Factory;

class SynonymFactory extends Factory
{
    protected $model = Synonym::class;

    public function definition(): array
    {
        $term = fake()->unique()->word();

        return [
            'term' => $term,
            'normalized_term' => strtolower($term),
            'source_type' => 'manual',
            'synonymable_type' => Finding::class,
            'synonymable_id' => Finding::factory(),
        ];
    }
}
