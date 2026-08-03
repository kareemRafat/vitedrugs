<?php

namespace Database\Seeders;

use App\Models\Drugs\AnatomicalStructure;
use App\Models\Drugs\BodySystem;
use Illuminate\Database\Seeder;

class AnatomicalStructureSeeder extends Seeder
{
    public function run(): void
    {
        if (AnatomicalStructure::exists()) {
            return;
        }

        $structures = [
            'Respiratory System' => [
                ['lungs', 'Lungs'],
                ['trachea', 'Trachea'],
                ['nasal_cavity', 'Nasal cavity'],
                ['pharynx', 'Pharynx'],
            ],
            'Digestive System' => [
                ['rumen', 'Rumen'],
                ['abomasum', 'Abomasum'],
                ['intestines', 'Intestines'],
                ['oral_cavity', 'Oral cavity'],
            ],
            'Nervous System' => [
                ['brain', 'Brain'],
                ['spinal_cord', 'Spinal cord'],
            ],
            'Reproductive System' => [
                ['uterus', 'Uterus'],
                ['testes', 'Testes'],
                ['mammary_gland', 'Mammary gland'],
            ],
            'Musculoskeletal System' => [
                ['joints', 'Joints'],
                ['hooves', 'Hooves'],
                ['muscles', 'Muscles'],
            ],
            'Urinary System' => [
                ['kidneys', 'Kidneys'],
                ['bladder', 'Bladder'],
            ],
            'Integumentary System' => [
                ['skin', 'Skin'],
                ['muzzle', 'Muzzle'],
                ['teats', 'Teats'],
            ],
            'Cardiovascular System' => [
                ['heart', 'Heart'],
                ['blood_vessels', 'Blood vessels'],
            ],
            'Endocrine System' => [
                ['thyroid_gland', 'Thyroid gland'],
            ],
            'Lymphatic System' => [
                ['lymph_nodes', 'Lymph nodes'],
            ],
        ];

        foreach ($structures as $systemName => $items) {
            $system = BodySystem::where('display_name', $systemName)->first();

            if (! $system) {
                continue;
            }

            foreach ($items as [$canonical, $display]) {
                AnatomicalStructure::create([
                    'body_system_id' => $system->id,
                    'canonical_name' => $canonical,
                    'display_name' => $display,
                    'type' => 'organ',
                ]);
            }
        }
    }
}
