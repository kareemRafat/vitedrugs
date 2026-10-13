<?php

namespace Database\Factories;

use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HostSpecies>
 */
class HostSpeciesFactory extends Factory
{
    protected $model = HostSpecies::class;

    /**
     * Display name to taxonomy group.
     *
     * Kept explicit rather than randomised because `taxonomy_group` decides which
     * site part a species belongs to — a factory that rolled a poultry species
     * into a large-animal test would fail intermittently rather than clearly.
     *
     * @var array<string, string>
     */
    private const BY_GROUP = [
        'ruminant' => ['Cattle', 'Buffalo', 'Sheep', 'Goat', 'Camel', 'Calf'],
        'equine' => ['Equine'],
        'porcine' => ['Pig'],
        'canine' => ['Dogs'],
        'feline' => ['Cats'],
        'fish' => ['Fish'],
        'poultry' => ['Chicken', 'Turkey', 'Duck', 'Quail'],
    ];

    public function definition(): array
    {
        return $this->attributesFor('ruminant');
    }

    public function largeAnimals(): static
    {
        return $this->taxonomyGroup('ruminant');
    }

    public function poultry(): static
    {
        return $this->taxonomyGroup('poultry');
    }

    public function taxonomyGroup(string $group): static
    {
        return $this->state(fn (): array => $this->attributesFor($group));
    }

    /**
     * @return array<string, mixed>
     */
    private function attributesFor(string $group): array
    {
        $names = self::BY_GROUP[$group] ?? self::BY_GROUP['ruminant'];
        $displayName = fake()->unique()->randomElement($names);

        return [
            'canonical_name' => strtolower($displayName),
            'display_name' => $displayName,
            'taxonomy_group' => $group,
            'is_domestic' => true,
            'is_wildlife' => false,
            'is_human' => false,
        ];
    }
}
