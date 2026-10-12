<?php

namespace Database\Seeders;

use App\Models\RiskFactor;
use Illuminate\Database\Seeder;

class RiskFactorSeeder extends Seeder
{
    public function run(): void
    {
        $factors = [

            'new_birds_introduced',
            'mixed_species_rearing',
            'sudden_feed_change',
            'wet_litter',
            'poor_ventilation',
            'overcrowding',
            'lack_of_vaccination',
            'transport_stress',
            'high_environmental_temperature',
            'cold_stress',
            'cannibalism',
            'poor_biosecurity',
            'earthworm_exposure',
            'insect_vector_exposure'

        ];

        foreach ($factors as $factor) {

            RiskFactor::firstOrCreate([
                'name' => $factor
            ]);
        }
    }
}