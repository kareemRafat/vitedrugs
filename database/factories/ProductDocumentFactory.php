<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductDocumentFactory extends Factory
{
    protected $model = ProductDocument::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'title' => fake()->randomElement(['Product Leaflet', 'Safety Data Sheet', 'Technical Brochure', 'Certificate of Analysis']),
            'file_path' => 'documents/'.fake()->uuid().'.pdf',
            'type' => fake()->randomElement(['leaflet', 'datasheet', 'brochure', 'certificate']),
        ];
    }
}
