<?php

namespace Database\Seeders;

use App\Models\BodySystem;
use Illuminate\Database\Seeder;

class BodySystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [

            'respiratory',
            'digestive',
            'nervous',
            'musculoskeletal',
            'immune',
            'urinary',
            'reproductive',
            'integumentary',
            'cardiovascular',
            'ocular',
            'general_systemic'

        ];

        foreach ($systems as $system) {

            BodySystem::firstOrCreate([
                'name' => $system
            ]);
        }
    }
}