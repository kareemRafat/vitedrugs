<?php

namespace Database\Factories;

use App\Models\Drugs\BodySystem;
use Illuminate\Database\Eloquent\Factories\Factory;

class BodySystemFactory extends Factory
{
    protected $model = BodySystem::class;

    public function definition(): array
    {
        $displayName = fake()->unique()->randomElement([
            'Respiratory System',
            'Digestive System',
            'Nervous System',
            'Reproductive System',
            'Musculoskeletal System',
            'Urinary System',
            'Integumentary System',
            'Cardiovascular System',
            'Endocrine System',
            'Lymphatic System',
        ]);

        return [
            'canonical_name' => strtolower(str_replace(' ', '_', $displayName)),
            'display_name' => $displayName,
        ];
    }
}
