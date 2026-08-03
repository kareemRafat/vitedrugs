<?php

namespace Database\Seeders;

use App\Models\Drugs\BodySystem;
use Illuminate\Database\Seeder;

class BodySystemSeeder extends Seeder
{
    public function run(): void
    {
        if (BodySystem::exists()) {
            return;
        }

        $systems = [
            'Respiratory System' => 'respiratory_system',
            'Digestive System' => 'digestive_system',
            'Nervous System' => 'nervous_system',
            'Reproductive System' => 'reproductive_system',
            'Musculoskeletal System' => 'musculoskeletal_system',
            'Urinary System' => 'urinary_system',
            'Integumentary System' => 'integumentary_system',
            'Cardiovascular System' => 'cardiovascular_system',
            'Endocrine System' => 'endocrine_system',
            'Lymphatic System' => 'lymphatic_system',
        ];

        foreach ($systems as $display => $canonical) {
            BodySystem::create([
                'canonical_name' => $canonical,
                'display_name' => $display,
            ]);
        }
    }
}
