<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Ontology (gap-fill only if empty)
            BodySystemSeeder::class,
            AnatomicalStructureSeeder::class,
            FindingSeeder::class,
            ModifierSeeder::class,
            ClinicalSignSeeder::class,

            // Species taxonomy (always runs; adds Fish/Turkey + taxonomy_group)
            HostSpeciesSeeder::class,

            // Disease-linked data (depends on diseases + ontology above)
            DiseaseClassificationSeeder::class,
            DiseaseHostSpeciesSeeder::class,
            DiseaseClinicalSignSeeder::class,

            // Microorganisms + their links
            MicroorganismSeeder::class,
            DiseaseMicroorganismSeeder::class,
            MicroorganismActiveIngredientSeeder::class,

            // Content entities
            MedicalArticleSeeder::class,
            SynonymSeeder::class,
            AbbreviationSeeder::class,

            // F8 projects + disease encyclopedia payloads
            VeterinaryProjectSeeder::class,
            DiseaseKnowledgePayloadSeeder::class,
        ]);
    }
}
