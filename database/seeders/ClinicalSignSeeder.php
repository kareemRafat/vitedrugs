<?php

namespace Database\Seeders;

use App\Models\Drugs\AnatomicalStructure;
use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\Finding;
use App\Models\Drugs\Modifier;
use Illuminate\Database\Seeder;

class ClinicalSignSeeder extends Seeder
{
    /**
     * [canonical_name, display_name, display_name_ar, finding_canonical, anatomy_display]
     */
    public function run(): void
    {
        $combos = [
            ['pyrexia', 'Pyrexia', 'الحُمّى', 'pyrexia', null],
            ['oral vesicle', 'Oral vesicle', 'حويصلة فموية', 'vesicle', 'Oral cavity'],
            ['tongue vesicle', 'Tongue vesicle', 'حويصلة اللسان', 'vesicle', 'Oral cavity'],
            ['muzzle vesicle', 'Muzzle vesicle', 'حويصلة المنخر', 'vesicle', 'Muzzle'],
            ['interdigital vesicle', 'Interdigital vesicle', 'حويصلة بين الأصابع', 'vesicle', 'Hooves'],
            ['coronary band vesicle', 'Coronary band vesicle', 'حويصلة التاج', 'vesicle', 'Hooves'],
            ['hypersalivation', 'Hypersalivation', 'فرط اللعاب', 'hypersalivation', 'Oral cavity'],
            ['lameness', 'Lameness', 'العرج', 'lameness', 'Hooves'],
            ['hoof sloughing', 'Hoof sloughing', 'انفصال الحافر', 'vesicle', 'Hooves'],
            ['depression', 'Depression', 'الخمول والاكتئاب', 'depression', null],
            ['inappetence', 'Inappetence', 'فقدان الشهية', 'inappetence', null],
            ['oral erosion', 'Oral erosion', 'تآكل فموي', 'erosion', 'Oral cavity'],
            ['diarrhea', 'Diarrhea', 'الإسهال', 'diarrhea', 'Intestines'],
            ['hemorrhagic diarrhea', 'Hemorrhagic diarrhea', 'إسهال نزفي', 'diarrhea', 'Intestines'],
            ['dehydration', 'Dehydration', 'الجفاف', 'dehydration', null],
            ['weight loss', 'Weight loss', 'فقدان الوزن', 'weight loss', null],
            ['anemia', 'Anemia', 'فقر الدم', null, null],
            ['mastitis', 'Mastitis', 'التهاب الضرع', 'reduced milk production', 'Mammary gland'],
            ['reduced milk production', 'Reduced milk production', 'انخفاض إنتاج الحليب', 'reduced milk production', 'Mammary gland'],
            ['mammary gland heat', 'Mammary gland heat', 'سخونة الضرع', 'reduced milk production', 'Mammary gland'],
            ['teat vesicle', 'Teat vesicle', 'حويصلة الحلمة', 'vesicle', 'Teats'],
            ['dyspnea', 'Dyspnea', 'ضيق التنفس', 'dyspnea', 'Lungs'],
            ['respiratory distress', 'Respiratory distress', 'اضطراب تنفسي', 'dyspnea', 'Lungs'],
            ['tachypnea', 'Tachypnea', 'تسرّع التنفس', 'dyspnea', 'Lungs'],
            ['nasal discharge', 'Nasal discharge', 'إفرازات أنفية', 'nasal discharge', 'Nasal cavity'],
            ['cough', 'Cough', 'السعال', 'cough', 'Trachea'],
            ['lacrimation', 'Lacrimation', 'الدمعان', null, null],
            ['weakness', 'Weakness', 'الضعف العام', null, null],
            ['sudden death', 'Sudden death', 'الموت المفاجئ', 'sudden death', null],
            ['profuse diarrhea', 'Profuse diarrhea', 'إسهال غزير', 'diarrhea', 'Intestines'],
            ['abortion', 'Abortion', 'الإجهاض', 'abortion', 'Uterus'],
            ['tachycardia', 'Tachycardia', 'تسرّع القلب', null, 'Heart'],
            ['recumbency', 'Recumbency', 'الاستلقاء', 'recumbency', 'Muscles'],
            ['lymphadenitis', 'Lymphadenitis', 'التهاب العقد اللمفاوية', null, 'Lymph nodes'],
            ['submandibular lymphadenitis', 'Submandibular lymphadenitis', 'التهاب العقد تحت الفك', null, 'Lymph nodes'],
            ['infertility', 'Infertility', 'العقم', null, 'Uterus'],
            ['temporary infertility', 'Temporary infertility', 'عقم مؤقت', null, 'Uterus'],
        ];

        $modifier = Modifier::where('display_name', 'Acute')->first();

        foreach ($combos as [$canonical, $display, $displayAr, $findingCanonical, $anatomy]) {
            $finding = Finding::where('canonical_name', $findingCanonical)->first();
            $structure = $anatomy ? AnatomicalStructure::where('display_name', $anatomy)->first() : null;

            $sign = ClinicalSign::firstOrCreate(
                ['canonical_name' => $canonical],
                [
                    'display_name' => $display,
                    'finding_id' => $finding?->id,
                    'anatomical_structure_id' => $structure?->id,
                    'modifier_id' => $modifier?->id,
                ]
            );

            $sign->update([
                'display_name_ar' => $displayAr,
            ]);
        }
    }
}
