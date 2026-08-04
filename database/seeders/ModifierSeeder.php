<?php

namespace Database\Seeders;

use App\Models\Drugs\Modifier;
use Illuminate\Database\Seeder;

class ModifierSeeder extends Seeder
{
    public function run(): void
    {
        $modifiers = [
            ['Bilateral', 'laterality', 'ثنائي الجانب'],
            ['Unilateral', 'laterality', 'أحادي الجانب'],
            ['Acute', 'course', 'حاد'],
            ['Chronic', 'course', 'مزمن'],
            ['Progressive', 'course', 'تقدمي'],
            ['Intermittent', 'temporal', 'متقطع'],
            ['Mild', 'severity', 'خفيف'],
            ['Severe', 'severity', 'شديد'],
        ];

        foreach ($modifiers as [$display, $group, $displayAr]) {
            $modifier = Modifier::firstOrCreate(
                ['canonical_name' => str()->slug($display)],
                [
                    'display_name' => $display,
                    'modifier_group' => $group,
                    'type' => $group,
                    'is_noisy' => false,
                ]
            );

            $modifier->update([
                'display_name_ar' => $displayAr,
            ]);
        }
    }
}
