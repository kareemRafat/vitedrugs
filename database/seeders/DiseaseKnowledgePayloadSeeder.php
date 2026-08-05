<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\LargeAnimals\DiseaseClassification;
use Illuminate\Database\Seeder;

class DiseaseKnowledgePayloadSeeder extends Seeder
{
    public function run(): void
    {
        $diseases = Disease::query()
            ->with(['clinicalSigns', 'diseaseClassification'])
            ->get();

        foreach ($diseases as $disease) {
            if (! empty($disease->knowledge_payload)) {
                continue;
            }

            $classification = $disease->diseaseClassification;

            $payload = [
                'clinical_signs' => $disease->clinicalSigns
                    ->map(fn ($sign) => [
                        'canonical_name' => $sign->canonical_name,
                        'display_name' => $sign->display_name,
                        'display_name_ar' => $sign->display_name_ar ?: $sign->display_name,
                        'weight' => $sign->pivot->weight,
                        'is_specific' => (bool) $sign->pivot->is_specific,
                        'is_required' => (bool) $sign->pivot->is_required,
                        'is_pathognomonic' => (bool) $sign->pivot->is_pathognomonic,
                    ])
                    ->values()
                    ->toArray(),
                'postmortem_findings' => $this->postmortemFindings($disease),
                'diagnosis' => $this->diagnosis($disease, $classification),
                'treatment' => $this->treatment($disease, $classification),
                'prevention_control' => $this->prevention($disease, $classification),
                'references' => [
                    ['title' => 'OIE Terrestrial Animal Health Code', 'url' => 'https://www.woah.org/en/what-we-do/standards/codes-and-manuals/terrestrial-code-online-access/'],
                    ['title' => 'MSD Veterinary Manual', 'url' => 'https://www.msdvetmanual.com/'],
                    ['title' => 'VetPedia Knowledge Base', 'url' => '/drugs/search'],
                ],
            ];

            $disease->update(['knowledge_payload' => $payload]);
        }
    }

    private function postmortemFindings(Disease $disease): array
    {
        $category = $disease->diseaseClassification?->etiology_type ?? '';

        $base = [
            [
                'canonical_name' => 'carcass_dehydration',
                'display_name' => 'Carcass dehydration',
                'display_name_ar' => 'جفاف الذبيحة',
            ],
            [
                'canonical_name' => 'congestion',
                'display_name' => 'Generalized congestion of visceral organs',
                'display_name_ar' => 'احتقان عام في الأعضاء الحشوية',
            ],
        ];

        if (str_contains($category, 'Viral')) {
            $base[] = [
                'canonical_name' => 'vesicular_lesions',
                'display_name' => 'Vesicular lesions on mucosa and skin',
                'display_name_ar' => 'آفات حويصلية في الأغشية المخاطية والجلد',
            ];
        }

        if (str_contains($category, 'Bacterial') || str_contains($category, 'bacterial')) {
            $base[] = [
                'canonical_name' => 'fibrinous_exudate',
                'display_name' => 'Fibrinous exudate on serosal surfaces',
                'display_name_ar' => 'إفراز ليفي على الأسطح المصلية',
            ];
        }

        if (str_contains($category, 'Protozoal') || str_contains($category, 'Parasitic')) {
            $base[] = [
                'canonical_name' => 'enteric_hemorrhage',
                'display_name' => 'Hemorrhagic enteritis and mucosal thickening',
                'display_name_ar' => 'التهاب معوي نزفي وسماكة في الغشاء المخاطي',
            ];
        }

        if ($disease->name === 'Mastitis') {
            $base[] = [
                'canonical_name' => 'mammary_inflammation',
                'display_name' => 'Inflamed mammary gland with purulent secretion',
                'display_name_ar' => 'التهاب الغدة الثديية مع إفراز صديدي',
            ];
        }

        return $base;
    }

    private function diagnosis(Disease $disease, ?DiseaseClassification $classification): array
    {
        $methods = [
            [
                'method' => 'Clinical examination',
                'method_ar' => 'الفحص السريري',
                'description' => 'Careful history and physical examination focusing on the presenting syndrome.',
                'description_ar' => 'أخذ التاريخ المرضي وإجراء الفحص السريري مع التركيز على المتلازمة الظاهرة.',
            ],
            [
                'method' => 'Laboratory testing',
                'method_ar' => 'الفحوصات المخبرية',
                'description' => 'Hematology, biochemistry, and serology to support the clinical suspicion.',
                'description_ar' => 'فحوصات الدم والكيمياء الحيوية والمصلية لدعم الشك السريري.',
            ],
            [
                'method' => 'Microbiological culture',
                'method_ar' => 'الزراعة الميكروبيولوجية',
                'description' => 'Isolation and identification of the causative agent where applicable.',
                'description_ar' => 'عزل وتحديد العامل المسبب عند الاقتضاء.',
            ],
            [
                'method' => 'Molecular diagnostics (PCR)',
                'method_ar' => 'التشخيص الجزيئي (PCR)',
                'description' => 'Nucleic-acid based detection for rapid and specific confirmation.',
                'description_ar' => 'الكشف القائم على الأحماض النووية للتأكيد السريع والدقيق.',
            ],
        ];

        if (str_contains($disease->name, 'Pneumonia') || $disease->name === 'CRD') {
            array_unshift($methods, [
                'method' => 'Thoracic auscultation and imaging',
                'method_ar' => 'التسمع الصدري والتصوير',
                'description' => 'Auscultation of lung fields complemented by thoracic radiography.',
                'description_ar' => 'تسمع الحقول الرئوية مع التصوير الشعاعي للصدر.',
            ]);
        }

        if ($disease->name === 'Mastitis') {
            array_unshift($methods, [
                'method' => 'California Mastitis Test',
                'method_ar' => 'اختبار كاليفورنيا لالتهاب الضرع',
                'description' => 'Rapid screening of milk somatic cells for subclinical mastitis.',
                'description_ar' => 'فحص سريع للخلايا الجسدية في الحليب للكشف عن التهاب الضرع تحت السريري.',
            ]);
        }

        return $methods;
    }

    private function treatment(Disease $disease, ?DiseaseClassification $classification): array
    {
        $category = $classification?->etiology_type ?? '';

        $treatment = [
            [
                'intervention' => 'Supportive care',
                'intervention_ar' => 'الرعاية الداعمة',
                'description' => 'Fluid therapy, nutritional support, and rest.',
                'description_ar' => 'العلاج بالسوائل والدعم الغذائي والراحة.',
            ],
            [
                'intervention' => 'Antipyretic therapy',
                'intervention_ar' => 'العلاج الخافض للحرارة',
                'description' => 'Non-steroidal anti-inflammatory drugs to control pyrexia and inflammation.',
                'description_ar' => 'مضادات الالتهاب غير الستيرويدية للسيطرة على الحمى والالتهاب.',
            ],
        ];

        if (str_contains($category, 'Bacterial') || str_contains($category, 'bacterial')) {
            $treatment[] = [
                'intervention' => 'Antimicrobial therapy',
                'intervention_ar' => 'العلاج المضاد للميكروبات',
                'description' => 'Targeted antibiotics based on culture and sensitivity, respecting withdrawal periods.',
                'description_ar' => 'مضادات حيوية موجهة بناءً على الزراعة والحساسية مع مراعاة فترات الانسحاب.',
            ];
        }

        if (str_contains($category, 'Protozoal') || str_contains($category, 'Parasitic')) {
            $treatment[] = [
                'intervention' => 'Antiprotozoal therapy',
                'intervention_ar' => 'العلاج المضاد للأوالي',
                'description' => 'Specific anticoccidial or antiparasitic agents according to the causative agent.',
                'description_ar' => 'عوامل مضادة للكوكسيديا أو الطفيليات وفقًا للعامل المسبب.',
            ];
        }

        if (str_contains($category, 'Viral')) {
            $treatment[] = [
                'intervention' => 'Symptomatic and immune support',
                'intervention_ar' => 'العلاج العرضي ودعم المناعة',
                'description' => 'Supportive therapy since specific antiviral drugs are limited in veterinary practice.',
                'description_ar' => 'علاج داعم نظرًا لمحدودية مضادات الفيروسات المحددة في الطب البيطري.',
            ];
        }

        return $treatment;
    }

    private function prevention(Disease $disease, ?DiseaseClassification $classification): array
    {
        $category = $classification?->etiology_type ?? '';

        $prevention = [
            [
                'measure' => 'Biosecurity',
                'measure_ar' => 'الأمن الحيوي',
                'description' => 'Strict disinfection, quarantine of new arrivals, and controlled access.',
                'description_ar' => 'تطهير صارم وعزل الحيوانات الجديدة والتحكم في الدخول.',
            ],
            [
                'measure' => 'Herd health monitoring',
                'measure_ar' => 'مراقبة صحة القطيع',
                'description' => 'Early detection and isolation of clinically affected animals.',
                'description_ar' => 'الكشف المبكر وعزل الحيوانات المصابة سريريًا.',
            ],
        ];

        if (str_contains($category, 'Viral')) {
            $prevention[] = [
                'measure' => 'Vaccination',
                'measure_ar' => 'التطعيم',
                'description' => 'Routine vaccination of susceptible populations to prevent outbreaks.',
                'description_ar' => 'تطعيم منتظم للحيوانات القابلة للإصابة للوقاية من تفشي المرض.',
            ];
        }

        if ($disease->name === 'Mastitis') {
            $prevention[] = [
                'measure' => 'Milking hygiene',
                'measure_ar' => 'نظافة الحلب',
                'description' => 'Proper teat disinfection and dry-cow therapy to reduce new infections.',
                'description_ar' => 'تطهير الحلمات وعلاج فترة الجفاف لتقليل العدوى الجديدة.',
            ];
        }

        return $prevention;
    }
}
