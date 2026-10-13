<?php

namespace Database\Factories;

use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpeciesFactory extends Factory
{
    protected $model = Species::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Cattle', 'Sheep', 'Goats', 'Pigs', 'Horses', 'Chickens',
            'Turkeys', 'Ducks', 'Dogs', 'Cats', 'Rabbits', 'Fish',
        ]);

        return [
            'name' => $name,
            'name_ar' => $this->arabicSpecies($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'description_ar' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function largeAnimals(): static
    {
        return $this->forDomain(Species::DOMAIN_LARGE_ANIMAL);
    }

    public function poultry(): static
    {
        return $this->forDomain(Species::DOMAIN_POULTRY);
    }

    public function forDomain(string $domain): static
    {
        return $this->state(fn (): array => ['domain' => $domain]);
    }

    /**
     * Pin the name instead of taking the randomised one.
     *
     * `species.name` and `species.slug` are both unique, and every name in the
     * pool above is a real animal, so an unnamed `Species::factory()` can collide
     * with a named one elsewhere in the same test.
     */
    public function named(string $name): static
    {
        return $this->state(fn (): array => [
            'name' => $name,
            'name_ar' => $this->arabicSpecies($name),
            'slug' => Str::slug($name),
        ]);
    }

    private function arabicSpecies(string $name): string
    {
        return match ($name) {
            'Cattle' => 'أبقار',
            'Sheep' => 'أغنام',
            'Goats' => 'ماعز',
            'Pigs' => 'خنازير',
            'Horses' => 'خيول',
            'Chickens' => 'دجاج',
            'Turkeys' => 'ديك رومي',
            'Ducks' => 'بط',
            'Dogs' => 'كلاب',
            'Cats' => 'قطط',
            'Rabbits' => 'أرانب',
            'Fish' => 'أسماك',
            default => $name,
        };
    }
}
