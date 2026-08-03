<?php

namespace Database\Seeders;

use App\Models\Drugs\Modifier;
use Illuminate\Database\Seeder;

class ModifierSeeder extends Seeder
{
    public function run(): void
    {
        if (Modifier::exists()) {
            return;
        }

        $modifiers = [
            ['Bilateral', 'laterality'],
            ['Unilateral', 'laterality'],
            ['Acute', 'course'],
            ['Chronic', 'course'],
            ['Progressive', 'course'],
            ['Intermittent', 'temporal'],
            ['Mild', 'severity'],
            ['Severe', 'severity'],
        ];

        foreach ($modifiers as [$display, $group]) {
            Modifier::create([
                'canonical_name' => str()->slug($display),
                'display_name' => $display,
                'modifier_group' => $group,
                'type' => $group,
                'is_noisy' => false,
            ]);
        }
    }
}
