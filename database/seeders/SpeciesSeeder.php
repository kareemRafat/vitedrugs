<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Poultry', 'دواجن'],
            ['Broiler', 'بداري تسمين'],
            ['Layer', 'دجاج بياض'],
            ['Breeder', 'أمهات'],
            ['Cattle', 'أبقار'],
            ['Buffalo', 'جاموس'],
            ['Sheep', 'أغنام'],
            ['Goat', 'ماعز'],
            ['Horse', 'خيول'],
            ['Dog', 'كلاب'],
            ['Cat', 'قطط'],
            ['Rabbit', 'أرانب'],
            ['Camel', 'جمال'],
            ['Fish', 'أسماك'],
            ['Turkey', 'رومي'],
            ['Duck', 'بط'],
            ['Quail', 'سمان'],
            ['Pigeon', 'حمام'],
        ];

        foreach ($items as $item) {
            Species::updateOrCreate(
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