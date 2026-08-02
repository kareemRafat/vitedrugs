<?php

namespace App\Http\Controllers;


use App\Models\BodySystem;

use App\Models\DiseaseClassification;
use App\Models\HostSpecies;

class FilterController extends Controller
{
    

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Preferred Species Order
        |--------------------------------------------------------------------------
        */

        $preferredSpeciesOrder = [

            'Cattle',
            'Buffalo',
            'Sheep',
            'Goat',
            'Camel',
            'Equine',
            'Dogs',
            'Cats',
            'Poultry',
            'Turkey',
            'Calf'
        ];

        /*
        |--------------------------------------------------------------------------
        | Species
        |--------------------------------------------------------------------------
        */

        $species = HostSpecies::query()

            ->whereIn(
                'display_name',
                $preferredSpeciesOrder
            )

            ->get()

            ->sortBy(function ($species)
            use ($preferredSpeciesOrder) {

                return array_search(
                    $species->display_name,
                    $preferredSpeciesOrder
                );
            })

            ->values();

        /*
        |--------------------------------------------------------------------------
        | Etiologies
        |--------------------------------------------------------------------------
        */

        $etiologies = DiseaseClassification::query()

            ->select('etiology_type')

            ->whereNotNull('etiology_type')

            ->groupBy('etiology_type')

            ->orderBy('etiology_type')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Body Systems
        |--------------------------------------------------------------------------
        */

        $bodySystems = BodySystem::query()

            ->orderBy('display_name')

            ->get();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(

            'filteration.filter',

            compact(
                'species',
                'etiologies',
                'bodySystems'
            )
        );
    }
}
