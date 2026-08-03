<?php

namespace Database\Seeders;

use App\Models\Drugs\AnatomicalStructure;
use App\Models\Drugs\ClinicalSign;
use App\Models\Drugs\Finding;
use App\Models\Drugs\Modifier;
use Illuminate\Database\Seeder;

class ClinicalSignSeeder extends Seeder
{
    public function run(): void
    {
        if (ClinicalSign::exists()) {
            return;
        }

        $combos = [
            ['fever', 'pyrexia', 'Respiratory System'],
            ['cough', 'cough', 'Respiratory System'],
            ['nasal discharge', 'nasal discharge', 'Respiratory System'],
            ['diarrhea', 'diarrhea', 'Digestive System'],
            ['lameness', 'lameness', 'Musculoskeletal System'],
            ['vesicle', 'vesicle', 'Integumentary System'],
            ['bloat', 'bloat', 'Digestive System'],
        ];

        foreach ($combos as [$display, $findingCanonical, $anatomy]) {
            $finding = Finding::where('canonical_name', $findingCanonical)->first();
            $structure = AnatomicalStructure::where('display_name', $anatomy)->first();
            $modifier = Modifier::where('display_name', 'Acute')->first();

            if (! $finding) {
                continue;
            }

            ClinicalSign::create([
                'canonical_name' => str()->slug($display),
                'display_name' => ucfirst($display),
                'finding_id' => $finding->id,
                'anatomical_structure_id' => $structure?->id,
                'modifier_id' => $modifier?->id,
            ]);
        }
    }
}
