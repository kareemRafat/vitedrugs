<?php

namespace Database\Factories;

use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\FindingImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class FindingImageFactory extends Factory
{
    protected $model = FindingImage::class;

    public function definition(): array
    {
        return [
            'finding_id' => Finding::factory(),
            'image' => 'findings/'.fake()->uuid().'.jpg',
            'caption' => fake()->sentence(4),
            'caption_ar' => null,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * A photograph with no caption on either side.
     *
     * The atlas only ever renders `localized_caption`, and an English fallback
     * would quietly stand in on the Arabic page — which is a worse outcome than
     * an empty caption, because the row looks finished. Tests that care about
     * Arabic rendering need this state to exist.
     */
    public function uncaptioned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'caption' => null,
            'caption_ar' => null,
        ]);
    }

    /**
     * A bilingual photograph.
     */
    public function bilingual(): static
    {
        return $this->state(fn (array $attributes): array => [
            'caption' => 'Pale, mottled liver',
            'caption_ar' => 'كبد شاحب مرقّط',
        ]);
    }
}
