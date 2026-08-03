<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\Drugs\Microorganism;
use Illuminate\Database\Seeder;

class DiseaseMicroorganismSeeder extends Seeder
{
    public function run(): void
    {
        if (Disease::count() === 0) {
            return;
        }

        $byName = [
            'Foot and Mouth Disease' => ['cause' => ['Foot-and-Mouth Disease Virus']],
            'Coccidiosis' => ['cause' => ['Eimeria', 'Eimeria tenella', 'Eimeria necatrix']],
            'Mastitis' => ['cause' => ['Staphylococcus aureus', 'Escherichia coli', 'Streptococcus equi']],
            'Bacterial pneumonia' => ['cause' => ['Pasteurella multocida', 'Escherichia coli']],
            'Bronchopneumonia' => ['cause' => ['Pasteurella multocida', 'Mycobacterium']],
            'Pneumonia' => ['cause' => ['Pasteurella multocida', 'Escherichia coli', 'Aspergillus fumigatus']],
            'CRD' => ['cause' => ['Mycobacterium', 'Escherichia coli']],
            'Colibacillosis' => ['cause' => ['Escherichia coli']],
            'Colisepticaemia' => ['cause' => ['Escherichia coli', 'Salmonella']],
            'Salmonellosis' => ['cause' => ['Salmonella']],
            'Mycoplasmosis' => ['cause' => ['Mycobacterium']],
            'Septicaemia' => ['cause' => ['Escherichia coli', 'Pasteurella', 'Salmonella']],
            'Septicemia' => ['cause' => ['Escherichia coli', 'Pasteurella', 'Salmonella']],
            'Diarrhea' => ['cause' => ['Escherichia coli', 'Clostridium', 'Salmonella']],
            'Enteritis' => ['cause' => ['Escherichia coli', 'Clostridium', 'Salmonella']],
            'Gastroenteritis' => ['cause' => ['Escherichia coli', 'Clostridium', 'Salmonella']],
            'Strangles' => ['cause' => ['Streptococcus equi', 'Streptococcus']],
            'Metritis' => ['cause' => ['Escherichia coli', 'Streptococcus']],
            'Cervicitis' => ['cause' => ['Escherichia coli']],
            'Foot rot' => ['cause' => ['Fusobacterium', 'Spirochaetes']],
        ];

        foreach ($byName as $diseaseName => $roles) {
            $disease = Disease::where('name', $diseaseName)->first();

            if (! $disease) {
                continue;
            }

            foreach ($roles as $role => $microNames) {
                foreach ($microNames as $microName) {
                    $micro = Microorganism::where('name', $microName)->first();

                    if (! $micro) {
                        continue;
                    }

                    $disease->microorganisms()->syncWithoutDetaching([
                        $micro->id => ['role' => $role],
                    ]);
                }
            }
        }
    }
}
