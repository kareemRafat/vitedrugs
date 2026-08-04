<?php

namespace Database\Seeders;

use App\Models\Drugs\HostSpecies;
use Illuminate\Database\Seeder;

class HostSpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $species = [
            ['canonical_name' => 'cattle', 'display_name' => 'Cattle', 'display_name_ar' => 'الأبقار', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'buffalo', 'display_name' => 'Buffalo', 'display_name_ar' => 'الجاموس', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'sheep', 'display_name' => 'Sheep', 'display_name_ar' => 'الأغنام', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'goat', 'display_name' => 'Goat', 'display_name_ar' => 'الماعز', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'camel', 'display_name' => 'Camel', 'display_name_ar' => 'الجِمال', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'equine', 'display_name' => 'Equine', 'display_name_ar' => 'الخيول', 'taxonomy_group' => 'equine', 'is_domestic' => true],
            ['canonical_name' => 'horse', 'display_name' => 'Horse', 'display_name_ar' => 'الحصان', 'taxonomy_group' => 'equine', 'is_domestic' => true],
            ['canonical_name' => 'pig', 'display_name' => 'Pig', 'display_name_ar' => 'الخنازير', 'taxonomy_group' => 'porcine', 'is_domestic' => true],
            ['canonical_name' => 'dog', 'display_name' => 'Dogs', 'display_name_ar' => 'الكلاب', 'taxonomy_group' => 'canine', 'is_domestic' => true],
            ['canonical_name' => 'cat', 'display_name' => 'Cats', 'display_name_ar' => 'القطط', 'taxonomy_group' => 'feline', 'is_domestic' => true],
            ['canonical_name' => 'poultry', 'display_name' => 'Poultry', 'display_name_ar' => 'الدواجن', 'taxonomy_group' => 'poultry', 'is_domestic' => true],
            ['canonical_name' => 'turkey', 'display_name' => 'Turkey', 'display_name_ar' => 'الديوك الرومية', 'taxonomy_group' => 'poultry', 'is_domestic' => true],
            ['canonical_name' => 'calf', 'display_name' => 'Calf', 'display_name_ar' => 'العجول', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'lamb', 'display_name' => 'Lamb', 'display_name_ar' => 'الحملان', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'fish', 'display_name' => 'Fish', 'display_name_ar' => 'الأسماك', 'taxonomy_group' => 'fish', 'is_domestic' => true],
        ];

        foreach ($species as $data) {
            $model = HostSpecies::firstOrCreate(
                ['canonical_name' => $data['canonical_name']],
                [
                    'display_name' => $data['display_name'],
                    'taxonomy_group' => $data['taxonomy_group'],
                    'is_domestic' => $data['is_domestic'],
                    'is_wildlife' => false,
                    'is_human' => false,
                ]
            );

            $model->update([
                'display_name_ar' => $data['display_name_ar'],
            ]);
        }
    }
}
