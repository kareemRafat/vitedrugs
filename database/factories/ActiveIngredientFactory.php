<?php

namespace Database\Factories;

use App\Models\ActiveIngredient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActiveIngredientFactory extends Factory
{
    protected $model = ActiveIngredient::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Amoxicillin', 'Enrofloxacin', 'Ivermectin', 'Oxytetracycline',
                'Tylosin', 'Flunixin Meglumine', 'Meloxicam', 'Doramectin',
                'Florfenicol', 'Closantel', 'Levamisole', 'Sulfadimidine',
                'Trimethoprim', 'Ceftiofur', 'Doxycycline', 'Colistin',
                'Lincomycin', 'Spectinomycin', 'Fenbendazole', 'Praziquantel',
                'Clorsulon', 'Nitroxynil', 'Deltamethrin', 'Cypermethrin',
                'Ketoprofen',
            ]),
            'name_ar' => fake()->unique()->randomElement([
                'أموكسيسيلين', 'إنروفلوكساسين', 'إيفرمكتين', 'أوكسي تتراسيكلين',
                'تايلوسين', 'فلونيكسين ميغلومين', 'ميلوكسيكام', 'دورامكتين',
                'فلورفينيكول', 'كلوسانتيل', 'ليفاميزول', 'سلفاديميدين',
                'تريميثوبريم', 'سيفتيوفور', 'دوكسيسيكلين', 'كوليستين',
                'لينكومايسين', 'سبيكتينومايسين', 'فينبيندازول', 'برازيكوانتيل',
                'كلورسلون', 'نيتروكسينيل', 'دلتاميثرين', 'سايبرمثرين',
                'كيتوبروفين',
            ]),
            'slug' => fn (array $attrs) => Str::slug($attrs['name']),
            'description' => 'A veterinary pharmaceutical compound. ' . fake()->paragraph(),
            'description_ar' => 'مركب صيدلاني بيطري. ' . fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
