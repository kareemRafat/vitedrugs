<?php

namespace Database\Seeders;

use App\Models\ActiveIngredient;
use App\Models\DrugClass;
use Illuminate\Database\Seeder;

class ActiveIngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = ActiveIngredient::factory()
            ->count(25)
            ->create();

        $drugClasses = DrugClass::all();

        foreach ($ingredients as $ingredient) {
            $ingredient->drugClasses()->attach(
                $drugClasses->random(rand(1, 3))->pluck('id')
            );
        }
    }
}
