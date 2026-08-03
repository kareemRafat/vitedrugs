<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\Drugs\DiseaseClassification;
use Illuminate\Database\Seeder;

class DiseaseKnowledgePayloadSeeder extends Seeder
{
    public function run(): void
    {
        $diseases = Disease::query()
            ->with(['clinicalSigns', 'diseaseClassification'])
            ->get();

        foreach ($diseases as $disease) {
            if (! empty($disease->knowledge_payload)) {
                continue;
            }

            $classification = $disease->diseaseClassification;

            $payload = [
                'clinical_signs' => $disease->clinicalSigns
                    ->map(fn ($sign) => [
                        'canonical_name' => $sign->canonical_name,
                        'display_name' => $sign->display_name,
                        'weight' => $sign->pivot->weight,
                        'is_specific' => (bool) $sign->pivot->is_specific,
                        'is_required' => (bool) $sign->pivot->is_required,
                        'is_pathognomonic' => (bool) $sign->pivot->is_pathognomonic,
                    ])
                    ->values()
                    ->toArray(),
                'postmortem_findings' => $this->postmortemFindings($disease),
                'diagnosis' => $this->diagnosis($disease, $classification),
                'treatment' => $this->treatment($disease, $classification),
                'prevention_control' => $this->prevention($disease, $classification),
                'references' => [
                    ['title' => 'OIE Terrestrial Animal Health Code', 'url' => 'https://www.woah.org/en/what-we-do/standards/codes-and-manuals/terrestrial-code-online-access/'],
                    ['title' => 'MSD Veterinary Manual', 'url' => 'https://www.msdvetmanual.com/'],
                    ['title' => 'VetPedia Knowledge Base', 'url' => '/drugs/search'],
                ],
            ];

            $disease->update(['knowledge_payload' => $payload]);
        }
    }

    private function postmortemFindings(Disease $disease): array
    {
        $category = $disease->diseaseClassification?->etiology_type ?? '';

        $base = [
            ['canonical_name' => 'carcass_dehydration', 'display_name' => 'Carcass dehydration'],
            ['canonical_name' => 'congestion', 'display_name' => 'Generalized congestion of visceral organs'],
        ];

        if (str_contains($category, 'Viral')) {
            $base[] = ['canonical_name' => 'vesicular_lesions', 'display_name' => 'Vesicular lesions on mucosa and skin'];
        }

        if (str_contains($category, 'Bacterial') || str_contains($category, 'bacterial')) {
            $base[] = ['canonical_name' => 'fibrinous_exudate', 'display_name' => 'Fibrinous exudate on serosal surfaces'];
        }

        if (str_contains($category, 'Protozoal') || str_contains($category, 'Parasitic')) {
            $base[] = ['canonical_name' => 'enteric_hemorrhage', 'display_name' => 'Hemorrhagic enteritis and mucosal thickening'];
        }

        if ($disease->name === 'Mastitis') {
            $base[] = ['canonical_name' => 'mammary_inflammation', 'display_name' => 'Inflamed mammary gland with purulent secretion'];
        }

        return $base;
    }

    private function diagnosis(Disease $disease, ?DiseaseClassification $classification): array
    {
        $methods = [
            ['method' => 'Clinical examination', 'description' => 'Careful history and physical examination focusing on the presenting syndrome.'],
            ['method' => 'Laboratory testing', 'description' => 'Hematology, biochemistry, and serology to support the clinical suspicion.'],
            ['method' => 'Microbiological culture', 'description' => 'Isolation and identification of the causative agent where applicable.'],
            ['method' => 'Molecular diagnostics (PCR)', 'description' => 'Nucleic-acid based detection for rapid and specific confirmation.'],
        ];

        if (str_contains($disease->name, 'Pneumonia') || $disease->name === 'CRD') {
            array_unshift($methods, [
                'method' => 'Thoracic auscultation and imaging',
                'description' => 'Auscultation of lung fields complemented by thoracic radiography.',
            ]);
        }

        if ($disease->name === 'Mastitis') {
            array_unshift($methods, [
                'method' => 'California Mastitis Test',
                'description' => 'Rapid screening of milk somatic cells for subclinical mastitis.',
            ]);
        }

        return $methods;
    }

    private function treatment(Disease $disease, ?DiseaseClassification $classification): array
    {
        $category = $classification?->etiology_type ?? '';

        $treatment = [
            ['intervention' => 'Supportive care', 'description' => 'Fluid therapy, nutritional support, and rest.'],
            ['intervention' => 'Antipyretic therapy', 'description' => 'Non-steroidal anti-inflammatory drugs to control pyrexia and inflammation.'],
        ];

        if (str_contains($category, 'Bacterial') || str_contains($category, 'bacterial')) {
            $treatment[] = [
                'intervention' => 'Antimicrobial therapy',
                'description' => 'Targeted antibiotics based on culture and sensitivity, respecting withdrawal periods.',
            ];
        }

        if (str_contains($category, 'Protozoal') || str_contains($category, 'Parasitic')) {
            $treatment[] = [
                'intervention' => 'Antiprotozoal therapy',
                'description' => 'Specific anticoccidial or antiparasitic agents according to the causative agent.',
            ];
        }

        if (str_contains($category, 'Viral')) {
            $treatment[] = [
                'intervention' => 'Symptomatic and immune support',
                'description' => 'Supportive therapy since specific antiviral drugs are limited in veterinary practice.',
            ];
        }

        return $treatment;
    }

    private function prevention(Disease $disease, ?DiseaseClassification $classification): array
    {
        $category = $classification?->etiology_type ?? '';

        $prevention = [
            ['measure' => 'Biosecurity', 'description' => 'Strict disinfection, quarantine of new arrivals, and controlled access.'],
            ['measure' => 'Herd health monitoring', 'description' => 'Early detection and isolation of clinically affected animals.'],
        ];

        if (str_contains($category, 'Viral')) {
            $prevention[] = [
                'measure' => 'Vaccination',
                'description' => 'Routine vaccination of susceptible populations to prevent outbreaks.',
            ];
        }

        if ($disease->name === 'Mastitis') {
            $prevention[] = [
                'measure' => 'Milking hygiene',
                'description' => 'Proper teat disinfection and dry-cow therapy to reduce new infections.',
            ];
        }

        return $prevention;
    }
}
