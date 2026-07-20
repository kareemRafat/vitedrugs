<?php

namespace Database\Seeders;

use App\Models\ActiveIngredient;
use App\Models\DrugInteraction;
use Illuminate\Database\Seeder;

class DrugInteractionSeeder extends Seeder
{
    public function run(): void
    {
        ActiveIngredient::all()->each(function (ActiveIngredient $ingredient) {
            DrugInteraction::factory()
                ->count(rand(1, 3))
                ->create(['active_ingredient_id' => $ingredient->id]);
        });
    }
}
