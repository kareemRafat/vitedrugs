<?php

namespace Database\Seeders;

use App\Models\Drugs\Finding;
use Illuminate\Database\Seeder;

class FindingSeeder extends Seeder
{
    public function run(): void
    {
        if (Finding::exists()) {
            return;
        }

        $findings = [
            'pyrexia' => 'Pyrexia',
            'depression' => 'Depression',
            'inappetence' => 'Inappetence',
            'diarrhea' => 'Diarrhea',
            'dehydration' => 'Dehydration',
            'dyspnea' => 'Dyspnea',
            'lameness' => 'Lameness',
            'vesicle' => 'Vesicle',
            'erosion' => 'Erosion',
            'ulcer' => 'Ulcer',
            'recumbency' => 'Recumbency',
            'weight loss' => 'Weight loss',
            'reduced milk production' => 'Reduced milk production',
            'abortion' => 'Abortion',
            'sudden death' => 'Sudden death',
            'cough' => 'Cough',
            'nasal discharge' => 'Nasal discharge',
            'hypersalivation' => 'Hypersalivation',
            'stomatitis' => 'Stomatitis',
            'bloat' => 'Bloat',
        ];

        foreach ($findings as $canonical => $display) {
            Finding::create([
                'canonical_name' => $canonical,
                'display_name' => $display,
                'is_general_sign' => true,
                'slug' => str()->slug($canonical),
            ]);
        }
    }
}
