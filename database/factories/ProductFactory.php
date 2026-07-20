<?php

namespace Database\Factories;

use App\Models\DosageForm;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $tradeName = fake()->unique()->randomElement([
            'Amoxivet', 'Baytril', 'Ivomec', 'Terramycin', 'Tylan',
            'Finadyne', 'Metacam', 'Dectomax', 'Nuflor', 'Closamectin',
            'Ripercol', 'Sulfivet', 'Tribrissen', 'Excenel', 'Doxypen',
            'Colivet', 'Linco-Spectin', 'Panacur', 'Droncit', 'Fasinex',
            'Trodax', 'Butox', 'Ectodex', 'Ketofen', 'Resflor',
            'Draxxin', 'Pulmotil', 'Advocin', 'Vetrimoxin', 'Norodine',
        ]);

        return [
            'trade_name' => $tradeName,
            'trade_name_ar' => $tradeName,
            'slug' => Str::slug($tradeName),
            'dosage_form_id' => DosageForm::factory(),
            'product_type' => fake()->randomElement(['pharmaceutical', 'vaccine', 'supplement', 'feed_additive', 'disinfectant', 'biological']),
            'description' => fake()->paragraphs(2, true),
            'description_ar' => fake()->paragraphs(2, true),
            'package_size' => fake()->randomElement(['10 ml', '50 ml', '100 ml', '250 ml', '500 ml', '1 L', '100 tabs', '500 tabs', '1 kg', '5 kg']),
            'storage_conditions' => fake()->randomElement(['Store below 25°C', 'Store at 2-8°C', 'Store at room temperature', 'Store in a cool dry place', 'Protect from light']),
            'is_active' => true,
        ];
    }
}
