<?php

namespace Database\Seeders;

use App\Models\Drugs\Abbreviation;
use Illuminate\Database\Seeder;

class AbbreviationSeeder extends Seeder
{
    public function run(): void
    {
        if (Abbreviation::exists()) {
            return;
        }

        $abbreviations = [
            ['FMD', 'Foot-and-Mouth Disease', 'Highly contagious viral disease of cloven-hoofed animals', 'disease'],
            ['CRD', 'Chronic Respiratory Disease', 'Chronic respiratory disease complex of poultry', 'disease'],
            ['BWD', 'Bovine Viral Diarrhea', 'Viral infection affecting cattle', 'disease'],
            ['AI', 'Avian Influenza', 'Influenza virus infection of birds', 'disease'],
            ['ND', 'Newcastle Disease', 'Viral disease of poultry', 'disease'],
            ['IM', 'Intramuscular', 'Route of drug administration', 'pharmacy'],
            ['IV', 'Intravenous', 'Route of drug administration', 'pharmacy'],
            ['SC', 'Subcutaneous', 'Route of drug administration', 'pharmacy'],
            ['PO', 'Per Os (by mouth)', 'Route of drug administration', 'pharmacy'],
            ['IMT', 'Intramammary Treatment', 'Route of drug administration for mastitis', 'pharmacy'],
        ];

        foreach ($abbreviations as [$abbr, $full, $description, $category]) {
            Abbreviation::create([
                'abbreviation' => $abbr,
                'full_term' => $full,
                'description' => $description,
                'category' => $category,
            ]);
        }
    }
}
