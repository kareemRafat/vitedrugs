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
                'slug' => 'mastitis-in-dairy-cattle-a-practical-approach',
                'disease' => 'Mastitis',
                'species' => 'Cattle',
                'article_type' => 'disease_reference',
                'summary' => 'A practical review of mastitis diagnosis, treatment, and herd-level control.',
                'content' => '<h2>Overview</h2><p>Mastitis is one of the most economically important diseases of dairy cattle, characterized by inflammation of the mammary gland.</p><h2>Clinical Signs</h2><p>Swelling and heat of the udder, reduced milk production, and abnormal milk consistency.</p><h2>Diagnosis</h2><p>Based on clinical examination, California Mastitis Test, and bacterial culture.</p><h2>Treatment</h2><p>Antibiotic therapy based on culture and sensitivity, combined with frequent milking out and anti-inflammatory support.</p><h2>Prevention</h2><p>Herd hygiene, proper milking technique, and dry-cow therapy.</p>',
            ],
            [
                'title' => 'Coccidiosis in Poultry: Recognition and Control',
                'slug' => 'coccidiosis-in-poultry-recognition-and-control',
                'disease' => 'Coccidiosis',
                'species' => 'Poultry',
                'article_type' => 'disease_reference',
                'summary' => 'Recognition of coccidiosis in broilers and layers, plus control strategies.',
                'content' => '<h2>Overview</h2><p>Coccidiosis is an enteric disease of poultry caused by Eimeria species.</p><h2>Clinical Signs</h2><p>Diarrhea, dehydration, weight loss, and reduced feed conversion.</p><h2>Diagnosis</h2><p>Fecal flotation and lesion scoring at post-mortem.</p><h2>Treatment</h2><p>Anticoccidial drugs such as amprolium and toltrazuril.</p><h2>Prevention</h2><p>Litter management and vaccination.</p>',
            ],
            [
                'title' => 'Salmonellosis: A Zoonotic Threat',
                'slug' => 'salmonellosis-a-zoonotic-threat',
                'disease' => 'Salmonellosis',
                'species' => 'Cattle, Poultry, Pigs',
                'article_type' => 'disease_reference',
                'summary' => 'Understanding salmonellosis across species and its public health implications.',
                'content' => '<h2>Overview</h2><p>Salmonellosis is a major zoonotic bacterial disease affecting many animal species.</p><h2>Clinical Signs</h2><p>Diarrhea, pyrexia, dehydration, and occasionally abortion.</p><h2>Diagnosis</h2><p>Bacterial culture and serotyping.</p><h2>Treatment</h2><p>Fluid therapy and targeted antimicrobials.</p><h2>Public Health</h2><p>Hygiene and biosecurity are critical to prevent foodborne transmission.</p>',
            ],
        ];

        foreach ($articles as $article) {
            $disease = Disease::where('name', $article['disease'])->first();

            $slug = $article['slug'];
            unset($article['slug'], $article['disease']);

            MedicalArticle::firstOrCreate(
                ['slug' => $slug],
                array_merge($article, [
                    'disease_id' => $disease?->id,
                    'is_published' => true,
                ])
            );
        }
    }
}
