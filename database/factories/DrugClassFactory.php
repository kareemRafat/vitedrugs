<?php

namespace Database\Factories;

use App\Models\DrugClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DrugClassFactory extends Factory
{
    protected $model = DrugClass::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Antibiotics', 'Anthelmintics', 'Anti-inflammatory', 'Antiparasitics',
            'Antifungals', 'Antivirals', 'Vaccines', 'Hormones',
            'Vitamins & Minerals', 'Fluid Therapy', 'Anaesthetics',
        ]);

        return [
            'name' => $name,
            'name_ar' => $this->arabicClass($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'description_ar' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    private function arabicClass(string $name): string
    {
        return match ($name) {
            'Antibiotics' => 'مضادات حيوية',
            'Anthelmintics' => 'مضادات الديدان',
            'Anti-inflammatory' => 'مضادات الالتهاب',
            'Antiparasitics' => 'مضادات الطفيليات',
            'Antifungals' => 'مضادات الفطريات',
            'Antivirals' => 'مضادات الفيروسات',
            'Vaccines' => 'لقاحات',
            'Hormones' => 'هرمونات',
            'Vitamins & Minerals' => 'فيتامينات ومعادن',
            'Fluid Therapy' => 'العلاج بالسوائل',
            'Anaesthetics' => 'مخدرات',
            default => $name,
        };
    }
}
