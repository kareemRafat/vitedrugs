<?php

namespace Database\Seeders;

use App\Models\DrugClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DrugClassSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Antibiotics', 'مضادات حيوية'],
            ['Anthelmintics', 'مضادات الطفيليات'],
            ['Coccidiostats', 'مضادات الكوكسيديا'],
            ['NSAIDs', 'مضادات الالتهاب غير الستيرويدية'],
            ['Vaccines', 'لقاحات'],
            ['Vitamins', 'فيتامينات'],
            ['Minerals', 'معادن'],
            ['Hormones', 'هرمونات'],
            ['Antifungals', 'مضادات الفطريات'],
            ['Antivirals', 'مضادات الفيروسات'],
            ['Disinfectants', 'مطهرات'],
        ];

        foreach ($items as $item) {
            DrugClass::updateOrCreate(
                ['slug' => Str::slug($item[0])],
                [
                    'name' => $item[0],
                    'name_ar' => $item[1],
                    'is_active' => true,
                ]
            );
        }
    }
}