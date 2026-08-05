<?php

namespace Database\Factories;

use App\Models\Disease;
use App\Models\LargeAnimals\DiseaseClassification;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiseaseClassificationFactory extends Factory
{
    protected $model = DiseaseClassification::class;

    public function definition(): array
    {
        return [
            'disease_id' => Disease::factory(),
            'infectiousness' => fake()->randomElement(['infectious', 'non_infectious']),
            'transmissibility' => fake()->randomElement(['contagious', 'non_contagious']),
            'etiology_type' => fake()->randomElement(['bacterial', 'viral', 'fungal', 'parasitic', 'multifactorial']),
            'occurrence_patterns' => fake()->randomElements(['sporadic', 'endemic', 'epizootic', 'pandemic', 'exotic'], 2),
            'disease_courses' => fake()->randomElements(['peracute', 'acute', 'subacute', 'chronic'], 2),
            'notifiable' => fake()->boolean(),
            'zoonotic' => fake()->boolean(),
            'oie_category' => fake()->randomElement(['listed', 'notifiable', 'surveillance', null]),
        ];
    }
}
