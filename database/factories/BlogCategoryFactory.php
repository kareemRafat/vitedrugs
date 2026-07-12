<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogCategoryFactory extends Factory
{
    private static array $arNames = [
        'أخبار بيطرية',
        'أدوية حديثة',
        'دراسات سريرية',
        'إرشادات علاجية',
        'صحة الحيوان',
        'تقارير علمية',
        'استشارات بيطرية',
        'بحوث ودراسات',
        'نصائح وقائية',
        'تحليلات دوائية',
    ];

    private static array $arDescriptions = [
        'آخر الأخبار والمستجدات في مجال الطب البيطري والأدوية.',
        'تعرف على أحدث الأدوية البيطرية والعلاجات المبتكرة.',
        'دراسات حالات سريرية من عيادات بيطرية متخصصة.',
        'إرشادات علاجية شاملة للأمراض والحالات البيطرية.',
        'مقالات متخصصة في صحة ورعاية الحيوانات المختلفة.',
        'تقارير علمية محكمة حول آخر الأبحاث البيطرية.',
        'استشارات وإجابات على أسئلة المربين والأطباء البيطريين.',
        'أحدث البحوث والدراسات في مجال العلوم البيطرية.',
        'نصائح وإرشادات للوقاية من الأمراض البيطرية.',
        'تحليلات متعمقة للأدوية والمكونات النشطة.',
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $idx = static::$index++ % count(static::$arNames);

        return [
            'name' => fake()->unique()->words(2, true),
            'name_ar' => static::$arNames[$idx],
            'slug' => Str::slug(fake()->words(2, true)),
            'description' => fake()->sentence(),
            'description_ar' => static::$arDescriptions[$idx],
            'is_active' => true,
        ];
    }
}
