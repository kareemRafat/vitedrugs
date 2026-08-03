<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\Drugs\HostSpecies;
use Illuminate\Database\Seeder;

class DiseaseHostSpeciesSeeder extends Seeder
{
    public function run(): void
    {
        if (Disease::count() === 0) {
            return;
        }

        $byName = [
            'Foot and Mouth Disease' => ['cattle', 'buffalo', 'sheep', 'goat', 'pig'],
            'Coccidiosis' => ['poultry', 'cattle', 'sheep', 'goat', 'calf'],
            'Mastitis' => ['cattle', 'buffalo', 'sheep', 'goat', 'calf'],
            'Bacterial pneumonia' => ['cattle', 'calf', 'sheep', 'goat', 'pig'],
            'Bronchopneumonia' => ['cattle', 'calf', 'sheep', 'goat', 'pig'],
            'Pneumonia' => ['cattle', 'calf', 'sheep', 'goat', 'pig'],
            'CRD' => ['poultry'],
            'Colibacillosis' => ['calf', 'poultry', 'pig'],
            'Colisepticaemia' => ['calf', 'poultry'],
            'Salmonellosis' => ['calf', 'cattle', 'poultry', 'pig', 'sheep', 'goat'],
            'Mycoplasmosis' => ['cattle', 'poultry', 'goat', 'sheep'],
            'Septicaemia' => ['cattle', 'calf', 'sheep', 'goat', 'pig'],
            'Septicemia' => ['cattle', 'calf', 'sheep', 'goat', 'pig'],
            'Diarrhea' => ['calf', 'cattle', 'pig', 'poultry'],
            'Enteritis' => ['calf', 'cattle', 'pig', 'poultry'],
            'Gastroenteritis' => ['calf', 'cattle', 'pig', 'poultry'],
            'Strangles' => ['equine', 'horse'],
            'Metritis' => ['cattle', 'buffalo', 'mare', 'sheep', 'goat'],
            'Cervicitis' => ['cattle', 'buffalo', 'sheep', 'goat'],
            'Foot rot' => ['cattle', 'sheep', 'goat', 'buffalo'],
        ];

        foreach ($byName as $diseaseName => $speciesCanonicals) {
            $disease = Disease::where('name', $diseaseName)->first();

            if (! $disease) {
                continue;
            }

            foreach ($speciesCanonicals as $canonical) {
                $species = HostSpecies::where('canonical_name', $canonical)->first();

                if (! $species) {
                    continue;
                }

                $disease->hostSpecies()->syncWithoutDetaching([
                    $species->id => [
                        'role' => 'primary_host',
                        'susceptibility' => 'susceptible',
                        'is_primary_host' => true,
                    ],
                ]);
            }
        }
    }
}
