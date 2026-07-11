<?php

namespace Database\Seeders;

use App\Models\DosageForm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DosageFormSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Injection', 'حقن'],
            ['Oral Solution', 'محلول فموي'],
            ['Powder', 'مسحوق'],
            ['Premix', 'بريمكس'],
            ['Tablet', 'أقراص'],
            ['Bolus', 'بولس'],
            ['Capsule', 'كبسولات'],
            ['Suspension', 'معلق'],
            ['Ointment', 'مرهم'],
            ['Cream', 'كريم'],
            ['Gel', 'جل'],
            ['Spray', 'بخاخ'],
            ['Drops', 'قطرات'],
        ];

        foreach ($items as $item) {
            DosageForm::updateOrCreate(
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
