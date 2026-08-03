<?php

namespace Database\Seeders;

use App\Models\Drugs\HostSpecies;
use Illuminate\Database\Seeder;

class HostSpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $species = [
            ['canonical_name' => 'cattle', 'display_name' => 'Cattle', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'buffalo', 'display_name' => 'Buffalo', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'sheep', 'display_name' => 'Sheep', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'goat', 'display_name' => 'Goat', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'camel', 'display_name' => 'Camel', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'equine', 'display_name' => 'Equine', 'taxonomy_group' => 'equine', 'is_domestic' => true],
            ['canonical_name' => 'horse', 'display_name' => 'Horse', 'taxonomy_group' => 'equine', 'is_domestic' => true],
            ['canonical_name' => 'pig', 'display_name' => 'Pig', 'taxonomy_group' => 'porcine', 'is_domestic' => true],
            ['canonical_name' => 'dog', 'display_name' => 'Dogs', 'taxonomy_group' => 'canine', 'is_domestic' => true],
            ['canonical_name' => 'cat', 'display_name' => 'Cats', 'taxonomy_group' => 'feline', 'is_domestic' => true],
            ['canonical_name' => 'poultry', 'display_name' => 'Poultry', 'taxonomy_group' => 'poultry', 'is_domestic' => true],
            ['canonical_name' => 'turkey', 'display_name' => 'Turkey', 'taxonomy_group' => 'poultry', 'is_domestic' => true],
            ['canonical_name' => 'calf', 'display_name' => 'Calf', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'lamb', 'display_name' => 'Lamb', 'taxonomy_group' => 'ruminant', 'is_domestic' => true],
            ['canonical_name' => 'fish', 'display_name' => 'Fish', 'taxonomy_group' => 'fish', 'is_domestic' => true],
        ];

        foreach ($species as $data) {
            HostSpecies::updateOrCreate(
                ['canonical_name' => $data['canonical_name']],
                [
                    'display_name' => $data['display_name'],
                    'taxonomy_group' => $data['taxonomy_group'],
                    'is_domestic' => $data['is_domestic'],
                    'is_wildlife' => false,
                    'is_human' => false,
                ]
            );
        }
    }
}
