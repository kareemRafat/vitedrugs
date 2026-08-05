<?php

namespace Database\Seeders;

use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Synonym;
use Illuminate\Database\Seeder;

class SynonymSeeder extends Seeder
{
    public function run(): void
    {
        if (Synonym::exists()) {
            return;
        }

        $terms = [
            'pyrexia' => ['fever', 'elevated temperature', 'hyperthermia'],
            'lameness' => ['limp', 'claudication'],
            'diarrhea' => ['loose stools', 'enteritis'],
            'dyspnea' => ['difficulty breathing', 'shortness of breath'],
        ];

        foreach ($terms as $canonical => $synonyms) {
            $sign = ClinicalSign::where('canonical_name', $canonical)->first();

            if (! $sign) {
                continue;
            }

            foreach ($synonyms as $term) {
                Synonym::create([
                    'term' => $term,
                    'normalized_term' => mb_strtolower($term),
                    'source_type' => 'manual',
                    'synonymable_type' => $sign::class,
                    'synonymable_id' => $sign->id,
                ]);
            }
        }
    }
}
