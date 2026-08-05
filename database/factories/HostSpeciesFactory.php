<?php

namespace Database\Factories;

use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Database\Eloquent\Factories\Factory;

class HostSpeciesFactory extends Factory
{
    protected $model = HostSpecies::class;

    public function definition(): array
    {
        $displayName = fake()->unique()->randomElement([
            'Cattle',
            'Buffalo',
            'Sheep',
            'Goat',
            'Camel',
            'Equine',
            'Dogs',
            'Cats',
            'Poultry',
            'Turkey',
            'Calf',
            'Fish',
        ]);

        $taxonomyGroup = match ($displayName) {
            'Cattle', 'Buffalo', 'Sheep', 'Goat', 'Camel', 'Calf' => 'ruminant',
            'Poultry', 'Turkey' => 'poultry',
            'Fish' => 'fish',
            default => null,
        };

        return [
            'canonical_name' => strtolower($displayName),
            'display_name' => $displayName,
            'taxonomy_group' => $taxonomyGroup,
            'is_domestic' => true,
            'is_wildlife' => false,
            'is_human' => false,
        ];
    }
}
