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

            // Findings that ClinicalSignSeeder needs but that were previously
            // only named inside the clinical sign itself. Without these the
            // clinical_signs NOT NULL finding_id column cannot be satisfied.
            'anemia' => ['Anemia', 'فقر الدم'],
            'lacrimation' => ['Lacrimation', 'الدمعان'],
            'weakness' => ['Weakness', 'الضعف العام'],
            'tachycardia' => ['Tachycardia', 'تسرّع القلب'],
            'lymphadenitis' => ['Lymphadenitis', 'التهاب العقد اللمفاوية'],
            'submandibular lymphadenitis' => ['Submandibular lymphadenitis', 'التهاب العقد تحت الفك'],
            'infertility' => ['Infertility', 'العقم'],
            'temporary infertility' => ['Temporary infertility', 'عقم مؤقت'],

            // Qualifier findings. These separate clinical signs that would
            // otherwise share one finding and one anatomical structure, which
            // the unique composition index rejects as a duplicate.
            'hoof sloughing' => ['Hoof sloughing', 'انفصال الحافر'],
            'mastitis' => ['Mastitis', 'التهاب الضرع'],
            'mammary gland heat' => ['Mammary gland heat', 'سخونة الغدة الثديية'],
            'hemorrhagic diarrhea' => ['Hemorrhagic diarrhea', 'إسهال نزفي'],
            'profuse diarrhea' => ['Profuse diarrhea', 'إسهال غزير'],
            'respiratory distress' => ['Respiratory distress', 'اضطراب تنفسي'],
            'tachypnea' => ['Tachypnea', 'تسرّع التنفس'],
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
