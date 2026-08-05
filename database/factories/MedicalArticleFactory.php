<?php

namespace Database\Factories;

use App\Models\Disease;
use App\Models\LargeAnimals\MedicalArticle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MedicalArticleFactory extends Factory
{
    protected $model = MedicalArticle::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(6, true),
            'disease_id' => Disease::factory(),
            'species' => fake()->randomElement(['Cattle', 'Sheep', 'Goat', 'Poultry', 'Fish', 'Equine']),
            'article_type' => fake()->randomElement(['disease_reference', 'overview', 'guideline']),
            'is_published' => true,
            'summary' => fake()->sentence(),
        ];
    }
}
