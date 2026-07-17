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
