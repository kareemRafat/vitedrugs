<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // No-dependency entities
            SpeciesSeeder::class,
            DosageFormSeeder::class,
            DrugClassSeeder::class,
            DiseaseSeeder::class,
            CompanySeeder::class,
            ActiveIngredientSeeder::class,

            // Product and all its relationships
            ProductSeeder::class,

            // Product child entities
            IndicationSeeder::class,
            ContraindicationSeeder::class,
            PrecautionSeeder::class,
            SideEffectSeeder::class,
            DosageSeeder::class,
            WithdrawalPeriodSeeder::class,
            AlternativeSeeder::class,
            ProductImageSeeder::class,
            ProductDocumentSeeder::class,

            // ActiveIngredient child entities
            DrugInteractionSeeder::class,

            // Blog (depends on User + BlogCategory)
            BlogSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'password' => Hash::make(12345678),
        ]);
    }
}
