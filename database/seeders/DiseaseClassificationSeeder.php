<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\LargeAnimals\DiseaseClassification;
use Illuminate\Database\Seeder;

class DiseaseClassificationSeeder extends Seeder
{
    public function run(): void
    {
        $classifications = [
            'Foot and Mouth Disease' => [
                'etiology_type' => 'Viral disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['epizootic', 'endemic'],
                'disease_courses' => ['acute'],
                'notifiable' => true,
                'zoonotic' => false,
                'oie_category' => 'notifiable',
            ],
            'Coccidiosis' => [
                'etiology_type' => 'Protozoal disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['acute', 'subacute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Mastitis' => [
                'etiology_type' => 'Multifactorial bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic', 'sporadic'],
                'disease_courses' => ['acute', 'chronic'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Bacterial pneumonia' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['sporadic', 'endemic'],
                'disease_courses' => ['acute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Bronchopneumonia' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['sporadic', 'endemic'],
                'disease_courses' => ['acute', 'subacute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Pneumonia' => [
                'etiology_type' => 'Multifactorial infectious disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['acute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'CRD' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['chronic'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Colibacillosis' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic', 'sporadic'],
                'disease_courses' => ['acute'],
                'notifiable' => false,
                'zoonotic' => true,
            ],
            'Colisepticaemia' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic', 'sporadic'],
                'disease_courses' => ['acute', 'peracute'],
                'notifiable' => false,
                'zoonotic' => true,
            ],
            'Salmonellosis' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic', 'sporadic'],
                'disease_courses' => ['acute', 'chronic'],
                'notifiable' => true,
                'zoonotic' => true,
                'oie_category' => 'notifiable',
            ],
            'Mycoplasmosis' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['chronic'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Septicaemia' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'non_contagious',
                'occurrence_patterns' => ['sporadic'],
                'disease_courses' => ['peracute', 'acute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Septicemia' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'non_contagious',
                'occurrence_patterns' => ['sporadic'],
                'disease_courses' => ['peracute', 'acute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Diarrhea' => [
                'etiology_type' => 'Multifactorial infectious disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['acute', 'chronic'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Enteritis' => [
                'etiology_type' => 'Multifactorial infectious disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['acute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Gastroenteritis' => [
                'etiology_type' => 'Multifactorial infectious disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['acute'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Strangles' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['acute'],
                'notifiable' => true,
                'zoonotic' => false,
            ],
            'Metritis' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'non_contagious',
                'occurrence_patterns' => ['sporadic'],
                'disease_courses' => ['acute', 'chronic'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Cervicitis' => [
                'etiology_type' => 'Bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'non_contagious',
                'occurrence_patterns' => ['sporadic'],
                'disease_courses' => ['chronic'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
            'Foot rot' => [
                'etiology_type' => 'Multifactorial bacterial disease',
                'infectiousness' => 'infectious',
                'transmissibility' => 'contagious',
                'occurrence_patterns' => ['endemic'],
                'disease_courses' => ['chronic'],
                'notifiable' => false,
                'zoonotic' => false,
            ],
        ];

        $diseases = Disease::all();

        foreach ($diseases as $disease) {
            $config = $classifications[$disease->name] ?? null;

            if (! $config) {
                continue;
            }

            DiseaseClassification::firstOrCreate(
                ['disease_id' => $disease->id],
                $config
            );
        }
    }
}
