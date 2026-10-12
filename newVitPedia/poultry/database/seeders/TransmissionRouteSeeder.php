<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransmissionRoute;

class TransmissionRouteSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [

            'Horizontal',

            'Vertical',

            'Airborne',

            'Fecal-Oral',

            'Direct Contact',

            'Indirect Contact',

            'Mechanical Vector',

            'Biological Vector',

            'Egg Transmission',

            'Waterborne',

            'Feedborne',

            'Wild Birds',

            'Fomite',

            'Sexual',

            'Unknown',
        ];

        foreach ($routes as $route) {

            TransmissionRoute::firstOrCreate([

                'name' => $route

            ], [

                'slug' => str($route)->slug(),

                'display_name' => $route
            ]);
        }
    }
}