<?php

namespace Database\Seeders;

use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use Illuminate\Database\Seeder;

class AnatomicalStructureSeeder extends Seeder
{
    public function run(): void
    {
        $structures = [
            'respiratory_system' => [
                ['lungs', 'Lungs', 'الرئتان'],
                ['trachea', 'Trachea', 'القصبة الهوائية'],
                ['nasal_cavity', 'Nasal cavity', 'التجويف الأنفي'],
                ['pharynx', 'Pharynx', 'البلعوم'],
            ],
            'digestive_system' => [
                ['rumen', 'Rumen', 'الكرش'],
                ['abomasum', 'Abomasum', 'المنفحة'],
                ['intestines', 'Intestines', 'الأمعاء'],
                ['oral_cavity', 'Oral cavity', 'تجويف الفم'],
                // Separate from "oral cavity" so a tongue vesicle and an oral
                // vesicle are distinguishable signs.
                ['tongue', 'Tongue', 'اللسان'],
            ],
            'nervous_system' => [
                ['brain', 'Brain', 'الدماغ'],
                ['spinal_cord', 'Spinal cord', 'النخاع الشوكي'],
            ],
            'reproductive_system' => [
                ['uterus', 'Uterus', 'الرحم'],
                ['testes', 'Testes', 'الخصيتان'],
                ['mammary_gland', 'Mammary gland', 'الغدة الثديية'],
            ],
            'musculoskeletal_system' => [
                ['joints', 'Joints', 'المفاصل'],
                ['hooves', 'Hooves', 'الأظلاف'],
                ['muscles', 'Muscles', 'العضلات'],
                // Separate from "hooves" so a vesicle at the interdigital space
                // and one at the coronary band are distinguishable signs.
                ['interdigital_space', 'Interdigital space', 'الفراغ بين الأصابع'],
                ['coronary_band', 'Coronary band', 'حزام التاج'],
            ],
            'urinary_system' => [
                ['kidneys', 'Kidneys', 'الكليتان'],
                ['bladder', 'Bladder', 'المثانة'],
            ],
            'integumentary_system' => [
                ['skin', 'Skin', 'الجلد'],
                ['muzzle', 'Muzzle', 'المنخر'],
                ['teats', 'Teats', 'الحلمات'],
            ],
            'cardiovascular_system' => [
                ['heart', 'Heart', 'القلب'],
                ['blood_vessels', 'Blood vessels', 'الأوعية الدموية'],
            ],
            'endocrine_system' => [
                ['thyroid_gland', 'Thyroid gland', 'الغدة الدرقية'],
            ],
            'lymphatic_system' => [
                ['lymph_nodes', 'Lymph nodes', 'العقد اللمفاوية'],
            ],
        ];

        foreach ($structures as $systemCanonical => $items) {
            $system = BodySystem::where('canonical_name', $systemCanonical)->first();

            if (! $system) {
                continue;
            }

            foreach ($items as [$canonical, $display, $displayAr]) {
                $structure = AnatomicalStructure::firstOrCreate(
                    ['canonical_name' => $canonical],
                    [
                        'body_system_id' => $system->id,
                        'display_name' => $display,
                        'type' => 'organ',
                    ]
                );

                $structure->update([
                    'display_name_ar' => $displayAr,
                ]);
            }
        }
    }
}
