<?php

namespace Database\Seeders;

use App\Models\ActiveIngredient;
use App\Models\LargeAnimals\Microorganism;
use Illuminate\Database\Seeder;

class MicroorganismActiveIngredientSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'Amoxicillin' => [
                'Pasteurella multocida' => 'sensitive',
                'Streptococcus equi' => 'sensitive',
                'Clostridium perfringens' => 'sensitive',
                'Staphylococcus aureus' => 'resistant',
                'Escherichia coli' => 'resistant',
            ],
            'Ceftiofur' => [
                'Escherichia coli' => 'sensitive',
                'Salmonella' => 'sensitive',
                'Pasteurella multocida' => 'sensitive',
                'Streptococcus equi' => 'sensitive',
            ],
            'Enrofloxacin' => [
                'Escherichia coli' => 'sensitive',
                'Salmonella' => 'sensitive',
                'Pasteurella multocida' => 'sensitive',
                'Staphylococcus aureus' => 'sensitive',
            ],
            'Oxytetracycline' => [
                'Escherichia coli' => 'moderate',
                'Salmonella' => 'moderate',
                'Pasteurella multocida' => 'sensitive',
                'Staphylococcus aureus' => 'moderate',
                'Streptococcus equi' => 'moderate',
                'Clostridium perfringens' => 'sensitive',
            ],
            'Doxycycline' => [
                'Escherichia coli' => 'sensitive',
                'Staphylococcus aureus' => 'sensitive',
            ],
            'Tylosin' => [
                'Streptococcus equi' => 'sensitive',
                'Clostridium perfringens' => 'sensitive',
                'Pasteurella multocida' => 'moderate',
            ],
            'Florfenicol' => [
                'Escherichia coli' => 'moderate',
                'Salmonella' => 'sensitive',
                'Pasteurella multocida' => 'sensitive',
            ],
        ];

        foreach ($links as $ingredientName => $pathogens) {
            $ingredient = ActiveIngredient::where('name', $ingredientName)->first();

            if (! $ingredient) {
                continue;
            }

            foreach ($pathogens as $pathogenName => $sensitivity) {
                $microorganism = Microorganism::where('name', $pathogenName)->first();

                if (! $microorganism) {
                    continue;
                }

                $microorganism->activeIngredients()->syncWithoutDetaching([
                    $ingredient->id => [
                        'sensitivity' => $sensitivity,
                        'notes' => null,
                    ],
                ]);
            }
        }
    }
}
