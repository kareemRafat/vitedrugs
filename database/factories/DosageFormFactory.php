<?php

namespace Database\Factories;

use App\Models\DosageForm;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DosageFormFactory extends Factory
{
    protected $model = DosageForm::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Tablet', 'Injectable Solution', 'Oral Suspension', 'Powder',
            'Capsule', 'Cream', 'Ointment', 'Drench', 'Bolus',
            'Solution for Injection', 'Spot-On', 'Collar', 'Premix',
        ]);

        return [
            'name' => $name,
            'name_ar' => $this->arabicForm($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'description_ar' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    private function arabicForm(string $name): string
    {
        return match ($name) {
            'Tablet' => 'أقراص',
            'Injectable Solution' => 'محلول حقن',
            'Oral Suspension' => 'معلق فموي',
            'Powder' => 'مسحوق',
            'Capsule' => 'كبسولات',
            'Cream' => 'كريم',
            'Ointment' => 'مرهم',
            'Drench' => 'مجرعة فموية',
            'Bolus' => 'بلعة',
            'Solution for Injection' => 'محلول للحقن',
            'Spot-On' => 'موضعي',
            'Collar' => 'طوق',
            'Premix' => 'خليط علفي',
            default => $name,
        };
    }
}
