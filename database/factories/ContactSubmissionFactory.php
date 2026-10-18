<?php

namespace Database\Factories;

use App\Enums\ContactCategory;
use App\Models\ContactSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactSubmissionFactory extends Factory
{
    protected $model = ContactSubmission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'role' => fake()->randomElement(['Veterinarian', 'Pharmacist', 'Farm Manager', 'Researcher', 'Student']),
            'subject' => fake()->sentence(4),
            'category' => fake()->randomElement(ContactCategory::cases()),
            'locale' => fake()->randomElement(['en', 'ar']),
            'message' => fake()->paragraph(3),
            'is_read' => false,
            'read_at' => null,
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function category(ContactCategory $category): static
    {
        return $this->state(fn (array $attributes): array => [
            'category' => $category,
            'subject' => $category->getLabel(),
        ]);
    }
}
