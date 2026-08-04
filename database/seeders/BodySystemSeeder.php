<?php

namespace Database\Seeders;

use App\Models\Drugs\BodySystem;
use Illuminate\Database\Seeder;

class BodySystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [
            // Live schema body systems
            'cardiovascular' => ['display_name' => 'Cardiovascular', 'display_name_ar' => 'الجهاز القلبي الوعائي'],
            'developmental' => ['display_name' => 'Developmental', 'display_name_ar' => 'الجهاز النمائي'],
            'digestive' => ['display_name' => 'Digestive', 'display_name_ar' => 'الجهاز الهضمي'],
            'endocrine' => ['display_name' => 'Endocrine', 'display_name_ar' => 'الغدد الصماء'],
            'hemolymphatic' => ['display_name' => 'HemoLymphatic', 'display_name_ar' => 'الجهاز الدموي اللمفاوي'],
            'hepatobiliary' => ['display_name' => 'HepatoBiliary', 'display_name_ar' => 'الجهاز الكبدي الصفراوي'],
            'immune' => ['display_name' => 'Immune', 'display_name_ar' => 'الجهاز المناعي'],
            'integumentary' => ['display_name' => 'Integumentary', 'display_name_ar' => 'الجهاز الجلدي'],
            'mammary' => ['display_name' => 'Mammary', 'display_name_ar' => 'الضَّرع'],
            'metabolic' => ['display_name' => 'Metabolic', 'display_name_ar' => 'الجهاز الاستقلابي'],
            'musculoskeletal' => ['display_name' => 'Musculoskeletal', 'display_name_ar' => 'الجهاز العضلي الهيكلي'],
            'neurologic' => ['display_name' => 'Neurologic', 'display_name_ar' => 'الجهاز العصبي'],
            'obstetric' => ['display_name' => 'Obstetric', 'display_name_ar' => 'الجهاز التوليدي'],
            'ophthalmic' => ['display_name' => 'Ophthalmic', 'display_name_ar' => 'الجهاز العيني'],
            'otic' => ['display_name' => 'Otic', 'display_name_ar' => 'الجهاز السمعي'],
            'reproductive' => ['display_name' => 'Reproductive', 'display_name_ar' => 'الجهاز التناسلي'],
            'respiratory' => ['display_name' => 'Respiratory', 'display_name_ar' => 'الجهاز التنفسي'],
            'systemic' => ['display_name' => 'Systemic', 'display_name_ar' => 'علامات عامة'],
            'urinary' => ['display_name' => 'Urinary', 'display_name_ar' => 'الجهاز البولي'],

            // Legacy full-name aliases used by earlier seed data
            'respiratory_system' => ['display_name' => 'Respiratory System', 'display_name_ar' => 'الجهاز التنفسي'],
            'digestive_system' => ['display_name' => 'Digestive System', 'display_name_ar' => 'الجهاز الهضمي'],
            'nervous_system' => ['display_name' => 'Nervous System', 'display_name_ar' => 'الجهاز العصبي'],
            'reproductive_system' => ['display_name' => 'Reproductive System', 'display_name_ar' => 'الجهاز التناسلي'],
            'musculoskeletal_system' => ['display_name' => 'Musculoskeletal System', 'display_name_ar' => 'الجهاز العضلي الهيكلي'],
            'urinary_system' => ['display_name' => 'Urinary System', 'display_name_ar' => 'الجهاز البولي'],
            'integumentary_system' => ['display_name' => 'Integumentary System', 'display_name_ar' => 'الجهاز الجلدي'],
            'cardiovascular_system' => ['display_name' => 'Cardiovascular System', 'display_name_ar' => 'الجهاز القلبي الوعائي'],
            'endocrine_system' => ['display_name' => 'Endocrine System', 'display_name_ar' => 'الغدد الصماء'],
            'lymphatic_system' => ['display_name' => 'Lymphatic System', 'display_name_ar' => 'الجهاز اللمفاوي'],
        ];

        foreach ($systems as $canonical => $data) {
            $system = BodySystem::firstOrCreate(
                ['canonical_name' => $canonical],
                [
                    'display_name' => $data['display_name'],
                ]
            );

            $system->update([
                'display_name_ar' => $data['display_name_ar'],
            ]);
        }
    }
}
