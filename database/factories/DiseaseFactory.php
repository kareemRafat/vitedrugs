<?php

namespace Database\Factories;

use App\Models\Disease;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DiseaseFactory extends Factory
{
    protected $model = Disease::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Bovine Respiratory Disease',
            'Mastitis',
            'Foot and Mouth Disease',
            'Brucellosis',
            'Anthrax',
            'Blackleg',
            'Coccidiosis',
            'Avian Influenza',
            'Newcastle Disease',
            'Swine Fever',
            'Canine Parvovirus',
            'Feline Leukemia',
            'Equine Influenza',
            'Tetanus',
            'Rabies',
            'Salmonellosis',
            'Colibacillosis',
            'Mycoplasmosis',
            'Pasteurellosis',
            'Leptospirosis',
            'Tuberculosis',
            'Johne\'s Disease',
            'Caseous Lymphadenitis',
            'Sheep Pox',
            'Goat Pox',
            ' Infectious Bursal Disease',
            'Marek\'s Disease',
            'Fowl Cholera',
            'Infectious Bronchitis',
            'Egg Drop Syndrome',
        ]);

        return [
            'name' => $name,
            'name_ar' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(3, true),
            'description_ar' => fake()->paragraphs(3, true),
            'is_active' => true,
            'is_zoonotic' => false,
            'is_internal' => false,
        ];
    }

    public function largeAnimals(): static
    {
        return $this->forDomain(Disease::DOMAIN_LARGE_ANIMAL);
    }

    public function poultry(): static
    {
        return $this->forDomain(Disease::DOMAIN_POULTRY);
    }

    public function forDomain(string $domain): static
    {
        return $this->state(fn (): array => ['domain' => $domain]);
    }

    /**
     * Pin the name instead of taking the randomised one.
     *
     * `diseases.name` and `diseases.slug` are both unique, so a test that names
     * one disease explicitly can still collide with another that rolled the same
     * name — and every name in the pool above is a real veterinary disease, i.e.
     * exactly the name a test would reach for. The collision shows up as an
     * intermittent UNIQUE violation roughly one run in thirty, not as a clear
     * failure.
     *
     * So: if a test names one disease, name every disease it creates.
     */
    public function named(string $name): static
    {
        return $this->state(fn (): array => [
            'name' => $name,
            'name_ar' => $name,
            'slug' => Str::slug($name),
        ]);
    }
}
