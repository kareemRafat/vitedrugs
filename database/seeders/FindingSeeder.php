<?php

namespace Database\Seeders;

use App\Models\LargeAnimals\Finding;
use Illuminate\Database\Seeder;

class FindingSeeder extends Seeder
{
    public function run(): void
    {
        $findings = [
            'pyrexia' => ['Pyrexia', 'الحُمّى'],
            'depression' => ['Depression', 'الخمول والاكتئاب'],
            'inappetence' => ['Inappetence', 'فقدان الشهية'],
            'diarrhea' => ['Diarrhea', 'الإسهال'],
            'dehydration' => ['Dehydration', 'الجفاف'],
            'dyspnea' => ['Dyspnea', 'ضيق التنفس'],
            'lameness' => ['Lameness', 'العرج'],
            'vesicle' => ['Vesicle', 'الحويصلة'],
            'erosion' => ['Erosion', 'التآكل'],
            'ulcer' => ['Ulcer', 'القرحة'],
            'recumbency' => ['Recumbency', 'الاستلقاء'],
            'weight loss' => ['Weight loss', 'فقدان الوزن'],
            'reduced milk production' => ['Reduced milk production', 'انخفاض إنتاج الحليب'],
            'abortion' => ['Abortion', 'الإجهاض'],
            'sudden death' => ['Sudden death', 'الموت المفاجئ'],
            'cough' => ['Cough', 'السعال'],
            'nasal discharge' => ['Nasal discharge', 'الإفرازات الأنفية'],
            'hypersalivation' => ['Hypersalivation', 'فرط اللعاب'],
            'stomatitis' => ['Stomatitis', 'التهاب الفم'],
            'bloat' => ['Bloat', 'الانتفاخ'],
        ];

        foreach ($findings as $canonical => [$display, $displayAr]) {
            $finding = Finding::firstOrCreate(
                ['canonical_name' => $canonical],
                [
                    'display_name' => $display,
                    'is_general_sign' => true,
                    'slug' => str()->slug($canonical),
                ]
            );

            $finding->update([
                'display_name_ar' => $displayAr,
            ]);
        }
    }
}
