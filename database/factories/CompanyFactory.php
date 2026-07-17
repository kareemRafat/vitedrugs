<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'name_ar' => $name,
            'slug' => Str::slug($name),
            'parent_company_id' => null,
            'company_type' => fake()->randomElement(['manufacturer', 'agent', 'distributor', 'marketing']),
            'logo' => null,
            'description' => fake()->paragraph(),
            'description_ar' => fake()->paragraph(),
            'country' => fake()->country(),
            'address' => fake()->address(),
            'address_ar' => fake()->address(),
            'governorate' => fake()->city(),
            'google_maps_url' => null,
            'coverage_area' => fake()->randomElement(['Local', 'Regional', 'National', 'International']),
            'registration_number' => fake()->bothify('REG-####-??'),
            'website' => 'https://' . fake()->domainName(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'whatsapp' => fake()->phoneNumber(),
            'telegram' => null,
            'facebook' => null,
            'linkedin' => null,
            'youtube' => null,
            'instagram' => null,
            'contact_person' => fake()->name(),
            'is_active' => true,
        ];
    }
}
