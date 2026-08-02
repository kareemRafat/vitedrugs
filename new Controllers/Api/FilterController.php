<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DiseaseSummaryResource;
use App\Http\Resources\FilterSuggestionResource;
use App\Models\ClinicalSign;
use App\Models\Disease;
use Illuminate\Http\Request;

class FilterController extends Controller
{
   /*
    |--------------------------------------------------------------------------
    | Suggestions
    |--------------------------------------------------------------------------
    */

    public function suggestions(Request $request)
    {
        $query = trim($request->q);

        if (!$query || strlen($query) < 2) {

            return response()->json([
                'data' => []
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Clinical Signs Only
        |--------------------------------------------------------------------------
        */

        $clinicalSigns = ClinicalSign::query()

            ->whereRaw(
                'LOWER(display_name) LIKE ?',
                ['%' . strtolower($query) . '%']
            )

            ->limit(10)

            ->get()

            ->map(function ($item) {

                return [

                    'id' => 'clinical_sign_' . $item->id,

                    'title' => $item->display_name,

                    'type' => 'Clinical Sign',

                    'entity_type' => 'clinical_sign',

                    'real_id' => $item->id,
                ];
            });

        return response()->json([

            'data' => FilterSuggestionResource::collection(
                $clinicalSigns->values()
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Disease Filtering
    |--------------------------------------------------------------------------
    */

    public function filter(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Request Data
        |--------------------------------------------------------------------------
        */

        $tokens = $request->tokens ?? [];

        $species = $request->species;

        $etiology = $request->etiologies;

        $bodySystem = $request->body_systems;

        $zoonotic = $request->zoonotic;

        /*
        |--------------------------------------------------------------------------
        | Clinical Signs
        |--------------------------------------------------------------------------
        */

        $clinicalSigns = [];

        foreach ($tokens as $token) {

            if (
                str_contains(
                    $token,
                    'clinical_sign_'
                )
            ) {

                $clinicalSigns[] = str_replace(
                    'clinical_sign_',
                    '',
                    $token
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = Disease::query()

            ->with([
                'classification',
                'hostSpecies',
                // 'bodySystems'
            ]);

        /*
        |--------------------------------------------------------------------------
        | Filter Clinical Signs
        |--------------------------------------------------------------------------
        */

        if (!empty($clinicalSigns)) {

            $query->whereHas(

                'clinicalSigns',

                function ($q) use ($clinicalSigns) {

                    $q->whereIn(
                        'clinical_signs.id',
                        $clinicalSigns
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Species
        |--------------------------------------------------------------------------
        */

        if (!empty($species)) {

            $speciesId = str_replace(
                'species_',
                '',
                $species
            );

            $query->whereHas(

                'hostSpecies',

                function ($q) use ($speciesId) {

                    $q->where(
                        'host_species.id',
                        $speciesId
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter Etiology
        |--------------------------------------------------------------------------
        */

        if (!empty($etiology)) {

            $query->whereHas(

                'classification',

                function ($q) use ($etiology) {

                    $q->where(
                        'etiology_type',
                        $etiology
                    );
                }
            );
        }

        // /*
        // |--------------------------------------------------------------------------
        // | Filter Body Systems
        // |--------------------------------------------------------------------------
        // */

        // if (!empty($bodySystem)) {

        //     $bodySystemId = str_replace(
        //         'body_system_',
        //         '',
        //         $bodySystem
        //     );

        //     $query->whereHas(

        //         'bodySystems',

        //         function ($q) use ($bodySystemId) {

        //             $q->where(
        //                 'body_systems.id',
        //                 $bodySystemId
        //             );
        //         }
        //     );
        // }

        /*
        |--------------------------------------------------------------------------
        | Filter Zoonotic
        |--------------------------------------------------------------------------
        */

        if ($zoonotic) {

            $query->whereHas(

                'classification',

                function ($q) {

                    $q->where(
                        'zoonotic',
                        true
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        */

        $results = $query

            ->orderBy('name_en')

            ->limit(50)

            ->get();

        return response()->json([
            'results' => DiseaseSummaryResource::collection($results)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Filter Page
    |--------------------------------------------------------------------------
    */
}
