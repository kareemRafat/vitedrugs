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
        ];
    }
}
