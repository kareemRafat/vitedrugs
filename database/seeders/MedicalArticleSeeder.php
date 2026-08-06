<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\LargeAnimals\MedicalArticle;
use Illuminate\Database\Seeder;

class MedicalArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Mastitis in Dairy Cattle: A Practical Approach',
                'title_ar' => 'التهاب الضرع في أبقار الحليب: منهج عملي',
                'slug' => 'mastitis-in-dairy-cattle-a-practical-approach',
                'disease' => 'Mastitis',
                'species' => 'Cattle',
                'species_ar' => 'الأبقار',
                'article_type' => 'disease_reference',
                'summary' => 'A practical review of mastitis diagnosis, treatment, and herd-level control.',
                'summary_ar' => 'مراجعة عملية لتشخيص التهاب الضرع وعلاجه ومكافحته على مستوى القطيع.',
                'content' => '<h2>Overview</h2><p>Mastitis is one of the most economically important diseases of dairy cattle, characterized by inflammation of the mammary gland.</p><h2>Clinical Signs</h2><p>Swelling and heat of the udder, reduced milk production, and abnormal milk consistency.</p><h2>Diagnosis</h2><p>Based on clinical examination, California Mastitis Test, and bacterial culture.</p><h2>Treatment</h2><p>Antibiotic therapy based on culture and sensitivity, combined with frequent milking out and anti-inflammatory support.</p><h2>Prevention</h2><p>Herd hygiene, proper milking technique, and dry-cow therapy.</p>',
                'content_ar' => '<h2>نظرة عامة</h2><p>التهاب الضرع من أهم الأمراض الاقتصادية في أبقار الحليب، ويتميز بالتهاب الغدة اللبنية.</p><h2>العلامات السريرية</h2><p>تورم وسخونة الضرع، انخفاض إنتاج الحليب، وتغير قوام الحليب.</p><h2>التشخيص</h2><p>يعتمد على الفحص السريري واختبار كاليفورنيا ومزرعة بكتيرية.</p><h2>العلاج</h2><p>المضادات الحيوية تبعاً للزراعة والحساسية، إلى جانب الحلب المتكرر والدعم المضاد للالتهابات.</p><h2>الوقاية</h2><p>نظافة التعبئة وأسلوب الحلب الصحيح وعلاج الأبقار الجافة.</p>',
            ],
            [
                'title' => 'Coccidiosis in Poultry: Recognition and Control',
                'title_ar' => 'داء المكورات في الدواجن: التشخيص والمكافحة',
                'slug' => 'coccidiosis-in-poultry-recognition-and-control',
                'disease' => 'Coccidiosis',
                'species' => 'Poultry',
                'species_ar' => 'الدواجن',
                'article_type' => 'disease_reference',
                'summary' => 'Recognition of coccidiosis in broilers and layers, plus control strategies.',
                'summary_ar' => 'التعرف على داء المكورات في دجاج اللحم والبيض مع استراتيجيات المكافحة.',
                'content' => '<h2>Overview</h2><p>Coccidiosis is an enteric disease of poultry caused by Eimeria species.</p><h2>Clinical Signs</h2><p>Diarrhea, dehydration, weight loss, and reduced feed conversion.</p><h2>Diagnosis</h2><p>Fecal flotation and lesion scoring at post-mortem.</p><h2>Treatment</h2><p>Anticoccidial drugs such as amprolium and toltrazuril.</p><h2>Prevention</h2><p>Litter management and vaccination.</p>',
                'content_ar' => '<h2>نظرة عامة</h2><p>مرض معوي يصيب الدواجن بسببه فاضل الإميريا (Eimeria).</p><h2>العلامات السريرية</h2><p>إسهال وجفاف وضعف وخفض التربة وتحويل العلف.</p><h2>التشخيص</h2><p>بفحص البراز وقياس الآفات بعد الذبح.</p><h2>العلاج</h2><p>أدوية مضادة للمشرك مثل الأمبروليم والتولترازوريل.</p><h2>الوقاية</h2><p>إدارة الفراش والمكافحة باللقاحات.</p>',
            ],
            [
                'title' => 'Salmonellosis: A Zoonotic Threat',
                'title_ar' => 'داء السالمونيلا: خطر حيواني المصدر',
                'slug' => 'salmonellosis-a-zoonotic-threat',
                'disease' => 'Salmonellosis',
                'species' => 'Cattle, Poultry, Pigs',
                'species_ar' => 'الأبقار، الدواجن، الخنازير',
                'article_type' => 'disease_reference',
                'summary' => 'Understanding salmonellosis across species and its public health implications.',
                'summary_ar' => 'فهم داء السالمونيلا عبر الأنواع وآثاره على الصحة العامة.',
                'content' => '<h2>Overview</h2><p>Salmonellosis is a major zoonotic bacterial disease affecting many animal species.</p><h2>Clinical Signs</h2><p>Diarrhea, pyrexia, dehydration, and occasionally abortion.</p><h2>Diagnosis</h2><p>Bacterial culture and serotyping.</p><h2>Treatment</h2><p>Fluid therapy and targeted antimicrobials.</p><h2>Public Health</h2><p>Hygiene and biosecurity are critical to prevent foodborne transmission.</p>',
                'content_ar' => '<h2>نظرة عامة</h2><p>داء السالمونيلا مرض بكتيري حيواني المصدر مهم يؤثر في عدة أنواع حيوانية.</p><h2>العلامات السريرية</h2><p>إسهال وحمى وجفاف وقد يحدث إجهاض أحياناً.</p><h2>التشخيص</h2><p>المزارع البكتيرية والتحديد المصلّي.</p><h2>العلاج</h2><p>العلاج بالسوائل والمضادات الحيوية الموجهة.</p><h2>الصحة العامة</h2><p>النظافة والأمن الحيوي أساسيان لمنع انتقال الأمراض الغذائية.</p>',
            ],
            [
                'title' => 'Foot-and-Mouth Disease in Large Animals',
                'title_ar' => 'مرض الحمى القلاعية في الحيوانات الكبيرة',
                'slug' => 'foot-and-mouth-disease',
                'disease' => 'Foot and Mouth Disease',
                'species' => 'Cattle, Buffaloes, Sheep, Goats, Pigs',
                'species_ar' => 'الأبقار، الجاموس، الأغنام، الماعز، الخنازير',
                'article_type' => 'disease_reference',
                'summary' => 'Highly contagious viral disease of cloven-hoofed animals with major economic impact.',
                'summary_ar' => 'مرض فيروسي شديد العدوى يصيب الحيوانات ذات الأظلف المقشرة وله أثر اقتصادي كبير.',
                'content' => '<h2>Overview</h2><p>Foot-and-mouth disease (FMD) is a highly contagious viral disease affecting cloven-hoofed animals.</p><h2>Etiology</h2><p>Caused by Foot-and-Mouth Disease Virus (FMDV), a member of the genus Aphthovirus.</p><h2>Clinical Signs</h2><p>Pyrexia, hypersalivation, oral and interdigital vesicles, lameness, and reduced milk production.</p><h2>Diagnosis</h2><p>PCR and ELISA are the primary confirmatory laboratory tests.</p><h2>Prevention</h2><p>Strict biosecurity, movement restriction, surveillance, and vaccination programs.</p>',
                'content_ar' => '<h2>نظرة عامة</h2><p>مرض الحمى القلاعية مرض فيروسي شديد العدوى يصيب الحيوانات مشقوقة الظلف.</p><h2>المسبب المرضي</h2><p>سببه فيروس الحمى القلاعية (FMDV) من جنس Aphthovirus.</p><h2>العلامات السريرية</h2><p>حمى وسيلان اللعاب وحويصلات فموية وبين الأظلاف وعرج وانخفاض إنتاج الحليب.</p><h2>التشخيص</h2><p>يعتمد التشخيص المؤكد على الفحوصات المخبرية مثل PCR وELISA.</p><h2>الوقاية والمكافحة</h2><p>الأمن الحيوي الصارم وتقييد الحركة والترصد وبرامج التطعيم.</p>',
            ],
        ];

        foreach ($articles as $article) {
            $disease = Disease::where('name', $article['disease'])->first();

            $slug = $article['slug'];
            unset($article['slug'], $article['disease']);

            MedicalArticle::updateOrCreate(
                ['slug' => $slug],
                array_merge($article, [
                    'disease_id' => $disease?->id,
                    'is_published' => true,
                ])
            );
        }
    }
}
