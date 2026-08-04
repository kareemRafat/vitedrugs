<?php

namespace Database\Seeders;

use App\Models\Disease;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DiseaseSeeder extends Seeder
{
    public function run(): void
    {
        $diseases = [
            ['Foot and Mouth Disease', 'الحمى القلاعية', 'Highly contagious viral disease of cloven-hoofed animals characterized by vesicles and erosions in the mouth, muzzle and feet.', 'مرض فيروسي شديد العدوى يصيب الحيوانات مشقوقة الظلف ويتميز بحويصلات وتآكلات في الفم والمنخر والأقدام.'],
            ['Coccidiosis', 'الكوكسيديا', 'Protozoal intestinal infection affecting mainly young animals, causing diarrhea, dehydration and weight loss.', 'عدوى معوية أوّليّة تصيب الحيوانات الصغيرة بشكل رئيسي وتسبب الإسهال والجفاف وفقدان الوزن.'],
            ['Mastitis', 'التهاب الضرع', 'Inflammation of the mammary gland, mostly of bacterial origin, reducing milk yield and quality.', 'التهاب في الغدة الثديية، مصدره بكتيري غالبًا، ويؤدي إلى انخفاض كمية وجودة الحليب.'],
            ['Bacterial pneumonia', 'الالتهاب الرئوي الجرثومي', 'Bacterial infection of the lungs leading to respiratory distress, fever and cough.', 'عدوى بكتيرية في الرئتين تؤدي إلى اضطراب تنفسي وحمى وسعال.'],
            ['Bronchopneumonia', 'الالتهاب الشُّعبي الرئوي', 'Inflammation of the bronchi and lung tissue, frequently secondary to stress or viral infection.', 'التهاب في القصبات والنسيج الرئوي، وغالبًا ما يكون ثانويًا للإجهاد أو العدوى الفيروسية.'],
            ['Pneumonia', 'الالتهاب الرئوي', 'Inflammation of the lung parenchyma with impaired gas exchange and systemic signs.', 'التهاب في نسيج الرئة مع ضعف في تبادل الغازات وعلامات جهازية.'],
            ['CRD', 'المرض التنفسي المزمن', 'Chronic respiratory disease of poultry caused mainly by Mycoplasma, with coughing and nasal discharge.', 'مرض تنفسي مزمن يصيب الدواجن يسببه الميكوبلازما بشكل رئيسي، مع سعال وإفرازات أنفية.'],
            ['Colibacillosis', 'العصيات القولونية', 'Disease caused by Escherichia coli, common in young animals causing diarrhea and septicemia.', 'مرض تسببه الإشريكية القولونية، شائع في الحيوانات الصغيرة ويسبب الإسهال وتسمم الدم.'],
            ['Colisepticaemia', 'تسمم الدم القولوني', 'Systemic Escherichia coli infection with fever, depression and sudden death.', 'عدوى جهازية بالإشريكية القولونية مع حمى وخمول وموت مفاجئ.'],
            ['Salmonellosis', 'السلمونيلات', 'Bacterial disease caused by Salmonella causing severe diarrhea, fever and septicemia.', 'مرض بكتيري تسببه السلمونيلا ويسبب إسهالًا شديدًا وحمى وتسمم دم.'],
            ['Mycoplasmosis', 'الميكوبلازما', 'Infection with Mycoplasma species affecting the respiratory and reproductive systems.', 'عدوى بأنواع الميكوبلازما تؤثر على الجهازين التنفسي والتناسلي.'],
            ['Septicaemia', 'تسمم الدم', 'Systemic spread of bacteria in the bloodstream with severe general signs.', 'انتشار جهازي للبكتيريا في مجرى الدم مع علامات عامة شديدة.'],
            ['Septicemia', 'تسمم الدم', 'Systemic spread of bacteria in the bloodstream with severe general signs.', 'انتشار جهازي للبكتيريا في مجرى الدم مع علامات عامة شديدة.'],
            ['Diarrhea', 'الإسهال', 'Frequent passage of loose or watery stools, common in calves and young animals.', 'خروج متكرر لبراز رخو أو مائي، شائع في العجول والحيوانات الصغيرة.'],
            ['Enteritis', 'التهاب الأمعاء', 'Inflammation of the intestinal mucosa with diarrhea and dehydration.', 'التهاب في الغشاء المخاطي للأمعاء مع إسهال وجفاف.'],
            ['Gastroenteritis', 'التهاب المعدة والأمعاء', 'Inflammation of the stomach and intestines with vomiting and diarrhea.', 'التهاب في المعدة والأمعاء مع قيء وإسهال.'],
            ['Strangles', 'الخناق', 'Contagious bacterial infection of horses caused by Streptococcus equi with lymph node abscesses.', 'عدوى بكتيرية معدية تصيب الخيول يسببها المكورات العقدية الفروسية مع خراجات في العقد اللمفاوية.'],
            ['Metritis', 'التهاب الرحم', 'Inflammation of the uterine wall after calving, usually of bacterial origin.', 'التهاب في جدار الرحم بعد الولادة، وعادة ما يكون مصدره بكتيريًا.'],
            ['Cervicitis', 'التهاب عنق الرحم', 'Inflammation of the cervix causing temporary infertility.', 'التهاب في عنق الرحم يسبب عقمًا مؤقتًا.'],
            ['Foot rot', 'عفن القدم', 'Bacterial infection of the hoof causing lameness and hoof separation.', 'عدوى بكتيرية في الحافر تسبب العرج وانفصال الحافر.'],
        ];

        foreach ($diseases as [$name, $nameAr, $description, $descriptionAr]) {
            $disease = Disease::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'is_active' => true,
                ]
            );

            $disease->update([
                'name_ar' => $nameAr,
                'description' => $description,
                'description_ar' => $descriptionAr,
            ]);
        }
    }
}
