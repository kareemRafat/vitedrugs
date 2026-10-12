<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $species = [

            'Chicken',
            'Broiler',
            'Layer',

            'Turkey',

            'Duck',
            'Goose',

            'Pigeon',

            'Quail',

            'Ostrich'
        ];

        foreach ($species as $item) {

            Species::firstOrCreate([
                'name' => $item
            ]);
        }
    }
}