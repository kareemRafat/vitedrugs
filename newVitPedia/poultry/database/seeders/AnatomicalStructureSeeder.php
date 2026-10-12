<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BodySystem;
use App\Models\AnatomicalStructure;
use Illuminate\Support\Str;

class AnatomicalStructureSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            'Respiratory System' => [

                'Trachea',
                'Lungs',
                'Air Sacs',
                'Nasal Cavity',
                'Sinuses',
                'Syrinx',

            ],

            'Digestive System' => [

                'Beak',
                'Oral Cavity',
                'Esophagus',
                'Crop',
                'Proventriculus',
                'Gizzard',
                'Duodenum',
                'Jejunum',
                'Ileum',
                'Ceca',
                'Large Intestine',
                'Cloaca',
                'Liver',
                'Pancreas',

            ],

            'Nervous System' => [

                'Brain',
                'Cerebellum',
                'Spinal Cord',
                'Peripheral Nerves',

            ],

            'Cardiovascular System' => [

                'Heart',
                'Blood Vessels',

            ],

            'Urinary System' => [

                'Kidneys',
                'Ureters',

            ],

            'Reproductive System' => [

                'Ovary',
                'Oviduct',
                'Testes',

            ],

            'Immune System' => [

                'Bursa of Fabricius',
                'Thymus',
                'Spleen',
                'Bone Marrow',

            ],

            'Musculoskeletal System' => [

                'Bones',
                'Joints',
                'Muscles',
                'Tendons',

            ],

            'Integumentary System' => [

                'Skin',
                'Feathers',
                'Comb',
                'Wattles',

            ],
        ];

        foreach ($data as $systemName => $structures) {

            $bodySystem = BodySystem::where(
                'name',
                $systemName
            )->orWhere(
                'display_name',
                $systemName
            )->first();

            if (! $bodySystem) {
                continue;
            }

            foreach ($structures as $index => $structure) {

                AnatomicalStructure::updateOrCreate(

                    [
                        'canonical_name' =>
                            Str::slug($structure, '_')
                    ],

                    [
                        'body_system_id' =>
                            $bodySystem->id,

                        'name' => $structure,

                        'display_name' => $structure,

                        'canonical_name' =>
                            Str::slug($structure, '_'),

                        'slug' =>
                            Str::slug($structure),

                        'description' => null,

                        'anatomical_region' =>
                            $systemName,

                        'hierarchy_level' => 0,

                        'sort_order' => $index + 1,

                        'is_vital' => in_array(
                            $structure,
                            [
                                'Brain',
                                'Heart',
                                'Lungs',
                                'Liver',
                                'Kidneys'
                            ]
                        ),

                        'is_active' => true,
                    ]
                );
            }
        }
    }
}