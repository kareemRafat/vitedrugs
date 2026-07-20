<?php

namespace Database\Factories;

use App\Models\ImportJob;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImportJobFactory extends Factory
{
    protected $model = ImportJob::class;

    public function definition(): array
    {
        return [
            'source_file' => 'imports/'.fake()->uuid().'.pdf',
            'source_type' => fake()->randomElement(['pdf', 'docx', 'html', 'json', 'txt']),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'failed']),
            'is_approved' => fake()->boolean(20),
            'total_products' => fake()->numberBetween(0, 100),
            'imported_products' => fn (array $attrs) => fake()->numberBetween(0, $attrs['total_products']),
            'failed_products' => fn (array $attrs) => $attrs['total_products'] - $attrs['imported_products'],
            'extracted_json' => null,
            'error_message' => null,
            'started_at' => fake()->optional()->dateTime(),
            'completed_at' => fake()->optional()->dateTime(),
        ];
    }
}
