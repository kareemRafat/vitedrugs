<?php

namespace Database\Seeders;

use App\Models\Drugs\Microorganism;
use Illuminate\Database\Seeder;

class MicroorganismSeeder extends Seeder
{
    public function run(): void
    {
        $microorganisms = [
            // Virus (missing from DB — only bacteria + fungus exist)
            ['Foot-and-Mouth Disease Virus', 'virus'],
            ['Infectious Bronchitis Virus', 'virus'],
            ['Newcastle Disease Virus', 'virus'],
            ['Avian Influenza Virus', 'virus'],
            ['Bovine Viral Diarrhea Virus', 'virus'],
            // Parasite (missing from DB)
            ['Eimeria', 'parasite'],
            ['Eimeria tenella', 'parasite'],
            ['Eimeria necatrix', 'parasite'],
            ['Haemonchus contortus', 'parasite'],
            ['Fasciola hepatica', 'parasite'],
            // Bacteria (present, but fill common ones for links)
            ['Escherichia coli', 'bacteria'],
            ['Salmonella', 'bacteria'],
            ['Pasteurella multocida', 'bacteria'],
            ['Streptococcus equi', 'bacteria'],
            ['Staphylococcus aureus', 'bacteria'],
            ['Clostridium perfringens', 'bacteria'],
            // Fungus
            ['Candida albicans', 'fungus'],
            ['Aspergillus fumigatus', 'fungus'],
        ];

        foreach ($microorganisms as [$name, $type]) {
            Microorganism::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => str()->slug($name),
                    'normalized_name' => mb_strtolower($name),
                    'microorganism_type' => $type,
                    'is_pathogenic' => true,
                    'json_data' => [
                        'common_name' => $name,
                        'organism_type' => $type,
                    ],
                ]
            );
        }
    }
}
