<?php

namespace Database\Factories;

use App\Models\Drugs\VeterinaryProject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VeterinaryProjectFactory extends Factory
{
    protected $model = VeterinaryProject::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(),
            'content' => fake()->paragraphs(5, true),
            'project_type' => fake()->randomElement(['feasibility_study', 'investment_guide', 'guideline']),
            'sector' => fake()->randomElement(['poultry', 'ruminants', 'fish', 'mixed']),
            'featured' => false,
            'is_published' => true,
        ];
    }
}
