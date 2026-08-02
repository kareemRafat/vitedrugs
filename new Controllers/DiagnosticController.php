<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ClinicalSign;
use App\Models\Disease;

class DiagnosticController extends Controller
{
    public function index()
    {





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
| Visible / Supported Species Only
|--------------------------------------------------------------------------
*/

        $hostSpecies =

            \App\Models\HostSpecies::query()

            ->whereIn(

                'display_name',

                $preferredSpeciesOrder
            )

            ->get()

            ->sortBy(function ($species) use ($preferredSpeciesOrder) {

                return array_search(

                    $species->display_name,

                    $preferredSpeciesOrder
                );
            })

            ->values();
        $allSigns =

            \App\Models\ClinicalSign::query()

            ->with([

                'anatomicalStructure.bodySystem',

                'finding',

            ])

            ->orderBy(
                'display_name'
            )

            ->get()

            ->map(function ($sign) {

                return [

                    'id' => $sign->id,

                    'display_name' =>
                    $sign->display_name,

                    'canonical_name' =>
                    $sign->canonical_name,

                    'body_system' =>

                    $sign
                        ->anatomicalStructure
                        ?->bodySystem
                        ?->display_name

                        ??

                        'General Systemic Signs',

                    'clinical_category' =>

                    $sign
                        ->finding
                        ?->category

                        ??

                        'General',
                ];
            });

        return view(

            'diagnostic.index',

            compact(
                'hostSpecies',
                'allSigns',
            )
        );
    }


    public function diagnose(
        Request $request
    ) {

        $selectedSigns =



            $request->get(
                'clinical_signs',
                []
            );

        $hostSpeciesId =
            $request->get(
                'host_species_id'
            );

        /*
|--------------------------------------------------------------------------
| Require At Least One Clinical Sign
|--------------------------------------------------------------------------
*/

        if (

            empty($selectedSigns)

        ) {

            return redirect()

                ->back()

                ->with(

                    'warning',

                    'Please select at least one abnormal clinical finding before running differential diagnosis.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | Selected Clinical Signs
    |--------------------------------------------------------------------------
    */

        $selectedClinicalSigns =

            ClinicalSign::query()

            ->with(
                'anatomicalStructure.bodySystem'
            )

            ->whereIn(
                'id',
                $selectedSigns
            )

            ->get();


        /*
    |--------------------------------------------------------------------------
    | Body System Distribution
    |--------------------------------------------------------------------------
    */

        $selectedSystemCounts = [];


        foreach (
            $selectedClinicalSigns
            as $selectedSign
        ) {

            $system =

                $selectedSign
                ->anatomicalStructure
                ?->bodySystem
                ?->display_name;


            if ($system) {

                if (
                    ! isset(
                        $selectedSystemCounts[$system]
                    )
                ) {

                    $selectedSystemCounts[$system] = 0;
                }

                $selectedSystemCounts[$system]++;
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Disease Retrieval
    |--------------------------------------------------------------------------
    */

        $diseases = Disease::query()

            ->with([

                'clinicalSigns.anatomicalStructure.bodySystem',

                'hostSpecies',

            ]);


        if ($hostSpeciesId) {

            $diseases->whereHas(

                'hostSpecies',

                function ($query) use ($hostSpeciesId) {

                    $query->where(
                        'host_species.id',
                        $hostSpeciesId
                    )

                        ->where(function ($q) {

                            $q->where(
                                'is_primary_host',
                                true
                            )

                                ->orWhere(
                                    'susceptibility',
                                    'high'
                                )

                                ->orWhere(
                                    'susceptibility',
                                    'moderate'
                                );
                        });
                }
            );
        }


        $diseases = $diseases->get();


        $results = [];

        /*
|--------------------------------------------------------------------------
| Differential Syndromes
|--------------------------------------------------------------------------
*/

        $differentialSyndromes =

            \App\Models\DifferentialSyndrome::query()

            ->with([

                'clinicalSigns',

                'diseases',

            ])

            ->get();


        /*
    |--------------------------------------------------------------------------
    | General Non-Specific Signs
    |--------------------------------------------------------------------------
    */

        $generalSigns = [

            'anorexia',
            'pyrexia',
            'fever',
            'depression',
            'weakness',
            'weight loss',
            'dehydration',
            'lethargy',
            'inappetence',
            'emaciation',
        ];


        foreach ($diseases as $disease) {

            $score = 0;

            /*
|--------------------------------------------------------------------------
| Species Diagnostic Boost
|--------------------------------------------------------------------------
*/

            if ($hostSpeciesId) {

                $hostRelation =

                    $disease->hostSpecies

                    ->firstWhere(
                        'id',
                        $hostSpeciesId
                    );

                if ($hostRelation) {

                    /*
        |--------------------------------------------------------------------------
        | Primary Host Boost
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_primary_host

                    ) {

                        $score += 40;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Reservoir Boost
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_reservoir

                    ) {

                        $score += 10;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Carrier Boost
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_carrier

                    ) {

                        $score += 10;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Incidental Host Penalty
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_incidental_host

                    ) {

                        $score -= 20;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Susceptibility Weighting
        |--------------------------------------------------------------------------
        */

                    $susceptibility = strtolower(

                        $hostRelation
                            ->pivot
                            ->susceptibility

                            ?? ''
                    );

                    if (

                        str_contains(
                            $susceptibility,
                            'high'
                        )

                    ) {

                        $score += 20;
                    }

                    if (

                        str_contains(
                            $susceptibility,
                            'low'
                        )

                    ) {

                        $score -= 10;
                    }
                }
            }

            $matchedSigns = [];

            $reasons = [];


            /*
        |--------------------------------------------------------------------------
        | Diagnostic Priority Variables
        |--------------------------------------------------------------------------
        */

            $matchedSpecificSigns = 0;

            $matchedRequiredSigns = 0;

            $matchedHighWeightSigns = 0;

            $matchedPathognomonicSigns = 0;

            $priorityTier = 4;
            /*
|--------------------------------------------------------------------------
| Syndrome Boost Tracking
|--------------------------------------------------------------------------
*/

            $matchedSyndromes = [];

            $confidence = 'Low';

            $matchType = 'Weak General Match';


            /*
        |--------------------------------------------------------------------------
        | Diagnostic Matching
        |--------------------------------------------------------------------------
        */
            $boostedSystems = [];
            foreach (
                $disease->clinicalSigns
                as $sign
            ) {

                if (

                    ! in_array(
                        $sign->id,
                        $selectedSigns
                    )

                ) {

                    continue;
                }


                $pivot =
                    $sign->pivot;


                $weight =
                    $pivot->weight ?? 1;


                $canonicalName =

                    strtolower(
                        trim(
                            $sign->canonical_name
                        )
                    );


                /*
|--------------------------------------------------------------------------
| Pathognomonic Override Engine
|--------------------------------------------------------------------------
*/

                if (

                    $pivot->is_pathognomonic

                ) {

                    /*
    |--------------------------------------------------------------------------
    | Massive Diagnostic Priority Boost
    |--------------------------------------------------------------------------
    */

                    $score += 50;

                    /*
    |--------------------------------------------------------------------------
    | Force Highest Clinical Tier
    |--------------------------------------------------------------------------
    */

                    $priorityTier = 1;

                    /*
    |--------------------------------------------------------------------------
    | Diagnostic Explanation
    |--------------------------------------------------------------------------
    */

                    $reasons[] =

                        'Pathognomonic clinical sign strongly associated with this disease';

                    /*
    |--------------------------------------------------------------------------
    | Track Pathognomonic Match
    |--------------------------------------------------------------------------
    */

                    $matchedPathognomonicSigns++;
                }



                /*
            |--------------------------------------------------------------------------
            | Base Weight
            |--------------------------------------------------------------------------
            */

                /*
|--------------------------------------------------------------------------
| Base Weight Logic
|--------------------------------------------------------------------------
*/
                /*
|--------------------------------------------------------------------------
| General Sign Suppression
|--------------------------------------------------------------------------
*/

                $isGeneralSign =

                    $sign
                    ->finding
                    ?->is_general_sign

                    ||

                    in_array(
                        $canonicalName,
                        $generalSigns
                    );


                if (

                    $isGeneralSign

                    &&

                    ! $pivot->is_pathognomonic

                ) {

                    /*
    |--------------------------------------------------------------------------
    | Weak General Sign Contribution
    |--------------------------------------------------------------------------
    */

                    $score += min($weight, 1);

                    /*
    |--------------------------------------------------------------------------
    | Mild Penalty To Reduce Noise
    |--------------------------------------------------------------------------
    */

                    $score -= 2;

                    /*
    |--------------------------------------------------------------------------
    | Prevent Negative Collapse
    |--------------------------------------------------------------------------
    */

                    $score = max($score, 0);

                    $reasons[] =

                        'General systemic sign with low diagnostic specificity';
                } else {

                    /*
    |--------------------------------------------------------------------------
    | Full Specific Sign Weight
    |--------------------------------------------------------------------------
    */

                    $score += $weight;
                }


                $matchedSigns[strtolower(
                    trim(
                        $sign->canonical_name
                    )
                )] = $sign->display_name;




                /*
            |--------------------------------------------------------------------------
            | High Weight Signs
            |--------------------------------------------------------------------------
            */

                /*
|--------------------------------------------------------------------------
| High Diagnostic Weight Signs
|--------------------------------------------------------------------------
*/

                if (

                    $weight >= 7

                    &&

                    ! $isGeneralSign

                    &&

                    (
                        $pivot->is_specific
                        ||
                        $pivot->is_required
                    )

                ) {

                    $matchedHighWeightSigns++;

                    $score += 10;

                    $reasons[] =

                        'High-priority diagnostic sign: ' .

                        $sign->display_name;
                }

                /*
            |--------------------------------------------------------------------------
            | Highly Specific Signs
            |--------------------------------------------------------------------------
            */

                if (

                    $pivot->is_specific

                    &&

                    ! in_array(
                        $canonicalName,
                        $generalSigns
                    )

                ) {

                    $matchedSpecificSigns++;

                    $score += 15;

                    $reasons[] =

                        'Specific clinical sign: ' .

                        $sign->display_name;
                }


                /*
            |--------------------------------------------------------------------------
            | Required Signs
            |--------------------------------------------------------------------------
            */

                if (

                    $pivot->is_required

                    &&

                    ! in_array(
                        $canonicalName,
                        $generalSigns
                    )

                ) {

                    $matchedRequiredSigns++;

                    $score += 12;

                    $reasons[] =

                        'Required clinical sign: ' .

                        $sign->display_name;
                }


                /*
            |--------------------------------------------------------------------------
            | Body-System Context
            |--------------------------------------------------------------------------
            */

                $system =

                    $sign
                    ->anatomicalStructure
                    ?->bodySystem
                    ?->display_name;


                if (

                    $system

                    &&

                    isset(
                        $selectedSystemCounts[$system]
                    )

                    &&

                    ! in_array(
                        $system,
                        $boostedSystems
                    )

                ) {

                    /*
|--------------------------------------------------------------------------
| Clinical Syndrome Cluster Boost
|--------------------------------------------------------------------------
*/

                    if (

                        $selectedSystemCounts[$system] >= 3

                    ) {

                        $score += 8;

                        $boostedSystems[] = $system;
                    } elseif (

                        $selectedSystemCounts[$system] == 2

                    ) {

                        $score += 6;
                    } else {

                        $score += 2;
                    }
                }
            }
            /*
|--------------------------------------------------------------------------
| Differential Syndrome Matching
|--------------------------------------------------------------------------
*/

            foreach (

                $differentialSyndromes

                as $syndrome

            ) {

                $syndromeSignIds =

                    $syndrome

                    ->clinicalSigns

                    ->pluck('id')

                    ->toArray();


                /*
    |--------------------------------------------------------------------------
    | Matched Syndrome Signs
    |--------------------------------------------------------------------------
    */

                $matchedSyndromeSigns =

                    array_intersect(

                        $selectedSigns,

                        $syndromeSignIds
                    );


                /*
    |--------------------------------------------------------------------------
    | Syndrome Threshold
    |--------------------------------------------------------------------------
    */

                $requiredMatchCount =

                    max(

                        2,

                        ceil(
                            count($syndromeSignIds) * 0.6
                        )
                    );


                /*
    |--------------------------------------------------------------------------
    | Syndrome Activated
    |--------------------------------------------------------------------------
    */

                if (

                    count($matchedSyndromeSigns)

                    >=

                    $requiredMatchCount

                ) {

                    /*
        |--------------------------------------------------------------------------
        | Disease Belongs To Syndrome
        |--------------------------------------------------------------------------
        */

                    $syndromeDisease =

                        $syndrome

                        ->diseases

                        ->firstWhere(
                            'id',
                            $disease->id
                        );


                    if ($syndromeDisease) {

                        $boostScore =

                            $syndromeDisease
                            ->pivot
                            ->boost_score

                            ?? 15;


                        $score += $boostScore;


                        $matchedSyndromes[] =

                            $syndrome->name;


                        $reasons[] =

                            'Matched differential syndrome: '

                            .

                            $syndrome->name;


                        /*
            |--------------------------------------------------------------------------
            | Syndrome Priority Boost
            |--------------------------------------------------------------------------
            */

                        $priorityTier = min(
                            $priorityTier,
                            2
                        );
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Missing Required Signs Penalty
        |--------------------------------------------------------------------------
        */

            $requiredSigns =

                $disease->clinicalSigns

                ->filter(function ($sign) {

                    return
                        $sign->pivot
                        ->is_required;
                });


            foreach (
                $requiredSigns
                as $requiredSign
            ) {

                if (

                    ! in_array(
                        $requiredSign->id,
                        $selectedSigns
                    )

                ) {

                    $score -= 4;
                }
            }


            /*
        |--------------------------------------------------------------------------
        | Non-General Matches
        |--------------------------------------------------------------------------
        */

            $matchedCanonicalSigns =

                collect($disease->clinicalSigns)

                ->filter(function ($sign) use ($selectedSigns) {

                    return in_array(
                        $sign->id,
                        $selectedSigns
                    );
                })

                ->pluck('canonical_name')

                ->map(function ($name) {

                    return strtolower(
                        trim($name)
                    );
                })

                ->unique()

                ->values();


            $nonGeneralMatches =

                $matchedCanonicalSigns

                ->reject(function ($sign) use ($generalSigns) {

                    return in_array(
                        $sign,
                        $generalSigns
                    );
                });






            if ($matchedPathognomonicSigns === 0) {

                /*
        |--------------------------------------------------------------------------
        | Tier 1
        | Specific / Required / High-Weight Signs
        |--------------------------------------------------------------------------
        */

                if (

                    (

                        $matchedSpecificSigns >= 1

                        ||

                        $matchedRequiredSigns >= 1

                        ||

                        $matchedHighWeightSigns >= 1

                    )

                    &&

                    count($nonGeneralMatches) >= 2

                ) {

                    $priorityTier = 1;

                    $score += 20;
                }


                /*
        |--------------------------------------------------------------------------
        | Tier 2
        | Three or More Non-General Signs
        |--------------------------------------------------------------------------
        */

                if (

                    count($nonGeneralMatches) >= 3

                ) {

                    $priorityTier = min(
                        $priorityTier,
                        2
                    );

                    $score += 20;

                    $reasons[] =

                        'Three or more associated clinical signs matched.';
                }


                /*
        |--------------------------------------------------------------------------
        | Tier 3
        | Two Non-General Signs
        |--------------------------------------------------------------------------
        */ elseif (

                    count($nonGeneralMatches) >= 2

                ) {

                    $priorityTier = min(
                        $priorityTier,
                        3
                    );

                    $score += 10;

                    $reasons[] =

                        'Two associated clinical signs matched.';
                }


                /*
        |--------------------------------------------------------------------------
        | Tier 4
        | Single Non-General Sign
        |--------------------------------------------------------------------------
        */ elseif (

                    count($nonGeneralMatches) === 1

                ) {

                    $priorityTier = 4;

                    $score += 2;
                }
            }


            /*
|--------------------------------------------------------------------------
| Matched Non-General Sign Count
|--------------------------------------------------------------------------
*/

            $matchedCount =

                count(
                    $nonGeneralMatches
                );




            /*
|--------------------------------------------------------------------------
| Strong Match
|--------------------------------------------------------------------------
*/

            if (

                $matchedSpecificSigns >= 1

                &&

                $matchedCount >= 2

            ) {

                $matchType =

                    'Strong Match';
            }


            /*
|--------------------------------------------------------------------------
| Moderate Match
|--------------------------------------------------------------------------
*/ elseif (

                $matchedCount >= 2

            ) {

                $matchType =

                    'Moderate Match';
            }

            /*
|--------------------------------------------------------------------------
| Final Pathognomonic Reinforcement
|--------------------------------------------------------------------------
*/

            if ($matchedPathognomonicSigns > 0) {

                $score += 25;

                $confidence = 'High';

                $matchType = 'Pathognomonic Match';

                $priorityTier = 1;
            }

            if ($matchedPathognomonicSigns === 0) {
                /*
|--------------------------------------------------------------------------
| Confidence Classification
|--------------------------------------------------------------------------
*/




                if (

                    $matchedCount >= 3

                    &&

                    $matchedSpecificSigns >= 1

                ) {

                    $confidence =

                        'High';
                } elseif (

                    $matchedCount >= 2

                ) {

                    $confidence =

                        'Moderate';
                } else {

                    $confidence =

                        'Low';
                }
            }

            /*
|--------------------------------------------------------------------------
| Weak / General Match Detection
|--------------------------------------------------------------------------
*/

            $isWeakGeneralMatch = false;


            if (

                $matchedCount <= 1

                &&

                $matchedSpecificSigns === 0

                &&

                $matchedRequiredSigns === 0

                &&

                $matchedPathognomonicSigns === 0

            ) {

                $isWeakGeneralMatch = true;
            }

            /*
|--------------------------------------------------------------------------
| Prevent Negative Scores
|--------------------------------------------------------------------------
*/

            $score = max($score, 0);

            /*
|--------------------------------------------------------------------------
| Ignore Diseases With No Actual Match
|--------------------------------------------------------------------------
*/

            if (

                count($matchedSigns) === 0

            ) {

                continue;
            }
            /*
        |--------------------------------------------------------------------------
        | Final Result
        |--------------------------------------------------------------------------
        */
            /*
|--------------------------------------------------------------------------
| Missing Key Diagnostic Findings
|--------------------------------------------------------------------------
*/

            $missingKeyFindings = [];

            foreach (

                $disease->clinicalSigns

                as $diseaseSign

            ) {

                /*
    |--------------------------------------------------------------------------
    | Skip Already Selected
    |--------------------------------------------------------------------------
    */

                if (

                    in_array(
                        $diseaseSign->id,
                        $selectedSigns
                    )
                ) {

                    continue;
                }

                $pivot =
                    $diseaseSign->pivot;

                /*
    |--------------------------------------------------------------------------
    | Pathognomonic
    |--------------------------------------------------------------------------
    */

                if (

                    $pivot->is_pathognomonic

                ) {

                    $missingKeyFindings[] =

                        $diseaseSign->display_name;

                    continue;
                }

                /*
    |--------------------------------------------------------------------------
    | Required
    |--------------------------------------------------------------------------
    */

                if (

                    $pivot->is_required

                ) {

                    $missingKeyFindings[] =

                        $diseaseSign->display_name;

                    continue;
                }

                /*
    |--------------------------------------------------------------------------
    | Highly Specific
    |--------------------------------------------------------------------------
    */

                if (

                    $pivot->is_specific

                    &&

                    $pivot->weight >= 7

                ) {

                    $missingKeyFindings[] =

                        $diseaseSign->display_name;
                }
            }


            $results[] = [

                'is_weak_general_match' =>

                $isWeakGeneralMatch,

                'matched_specific_signs' =>

                $matchedSpecificSigns,

                'matched_required_signs' =>

                $matchedRequiredSigns,

                'matched_non_general_count' =>

                $matchedCount,

                'disease' =>
                $disease,

                'score' =>
                $score,

                'priority_tier' =>
                $priorityTier,

                'confidence' =>
                $confidence,

                'match_type' =>

                $matchType,

                'matched_signs' =>

                array_values(
                    $matchedSigns
                ),

                'reasons' =>

                collect(

                    collect($reasons)

                        ->unique()

                        ->values()

                )

                    ->filter()

                    ->values()

                    ->toArray(),


                'missing_key_findings' =>

                collect($missingKeyFindings)

                    ->unique()

                    ->take(5)

                    ->values()

                    ->toArray(),
            ];
        }


        /*
|--------------------------------------------------------------------------
| Relative Match Strength Calculation
|--------------------------------------------------------------------------
*/

        $maxScore =

            collect($results)

            ->max('score');


        foreach ($results as &$result) {

            $relativeMatchStrength =

                $maxScore > 0

                ? round(
                    (
                        $result['score']
                        / $maxScore
                    ) * 100
                )

                : 0;


            /*
    |--------------------------------------------------------------------------
    | Safer Clinical Scaling
    |--------------------------------------------------------------------------
    */

            /*
|--------------------------------------------------------------------------
| Weak General Match Suppression
|--------------------------------------------------------------------------
*/

            if (

                $result['is_weak_general_match']

            ) {

                $relativeMatchStrength = min(

                    $relativeMatchStrength,

                    35
                );
            }

            if (

                count(
                    $result['matched_signs']
                ) >= 3

            ) {

                $relativeMatchStrength = min(
                    $relativeMatchStrength,
                    80
                );
            } elseif (

                count(
                    $result['matched_signs']
                ) == 2

            ) {

                $relativeMatchStrength = min(
                    $relativeMatchStrength,
                    65
                );
            } else {

                $relativeMatchStrength = min(
                    $relativeMatchStrength,
                    40
                );
            }


            /*
    |--------------------------------------------------------------------------
    | Weak Single-Sign Protection
    |--------------------------------------------------------------------------
    */
            if (

                $result['match_type']

                ===

                'Pathognomonic Match'

            ) {

                $relativeMatchStrength = max(
                    $relativeMatchStrength,
                    95
                );
            }

            if (

                count(
                    $result['matched_signs']
                ) === 1

                &&

                $result['match_type']

                !==

                'Pathognomonic Match'

            ) {

                $relativeMatchStrength = min(
                    $relativeMatchStrength,
                    45
                );
            }


            $result['relative_match_strength'] =

                max(
                    $relativeMatchStrength,
                    1
                );
        }


        /*
|--------------------------------------------------------------------------
| Final Clinical Ranking
|--------------------------------------------------------------------------
*/

        usort($results, function ($a, $b) {

            /*
    |--------------------------------------------------------------------------
    | Priority Tier First
    |--------------------------------------------------------------------------
    */

            if (

                $a['priority_tier']
                !==
                $b['priority_tier']

            ) {

                return

                    $a['priority_tier']
                    <=>
                    $b['priority_tier'];
            }

            /*
    |--------------------------------------------------------------------------
    | Then Relative Match Strength
    |--------------------------------------------------------------------------
    */

            if (

                $a['relative_match_strength']
                !==
                $b['relative_match_strength']

            ) {

                return

                    $b['relative_match_strength']
                    <=>
                    $a['relative_match_strength'];
            }

            /*
    |--------------------------------------------------------------------------
    | Then Raw Score
    |--------------------------------------------------------------------------
    */

            return

                $b['score']
                <=>
                $a['score'];
        });

        $topDiseases = collect($results)

            ->take(5);

        /*
|--------------------------------------------------------------------------
| Unified Refinement Signs Pool
|--------------------------------------------------------------------------
*/

        $refinementSignsPool = collect();
        /*
|--------------------------------------------------------------------------
| Diagnostic Refinement Questions
|--------------------------------------------------------------------------
*/

        foreach ($topDiseases as $result) {

            $disease = $result['disease'];

            /*
    |--------------------------------------------------------------------------
    | Exclude Already Selected Signs
    |--------------------------------------------------------------------------
    */

            $alreadySelectedSigns = $selectedSigns;

            /*
    |--------------------------------------------------------------------------
    | Step 1:
    | Pathognomonic Signs
    |--------------------------------------------------------------------------
    */

            $candidateSigns =

                $disease->clinicalSigns()

                ->wherePivot(
                    'is_pathognomonic',
                    1
                )

                ->whereNotIn(
                    'clinical_signs.id',
                    $alreadySelectedSigns
                )

                ->get();


            /*
    |--------------------------------------------------------------------------
    | Step 2:
    | Required Signs
    |--------------------------------------------------------------------------
    */

            if ($candidateSigns->isEmpty()) {

                $candidateSigns =

                    $disease->clinicalSigns()

                    ->wherePivot(
                        'is_required',
                        1
                    )

                    ->whereNotIn(
                        'clinical_signs.id',
                        $alreadySelectedSigns
                    )

                    ->orderByPivot(
                        'weight',
                        'desc'
                    )

                    ->get();
            }


            /*
    |--------------------------------------------------------------------------
    | Step 3:
    | High Weight Diagnostic Signs
    |--------------------------------------------------------------------------
    */

            if ($candidateSigns->isEmpty()) {

                $candidateSigns =

                    $disease->clinicalSigns()

                    ->whereNotIn(
                        'clinical_signs.id',
                        $alreadySelectedSigns
                    )

                    ->wherePivot(
                        'weight',
                        '>=',
                        5
                    )

                    ->orderByPivot(
                        'weight',
                        'desc'
                    )

                    ->limit(5)

                    ->get()

                    ->reject(function ($sign) use ($generalSigns) {

                        return in_array(

                            strtolower(
                                trim(
                                    $sign->canonical_name
                                )
                            ),

                            $generalSigns
                        );
                    });
            }


            /*
    |--------------------------------------------------------------------------
    | Skip Empty Disease Refinement
    |--------------------------------------------------------------------------
    */

            if ($candidateSigns->isEmpty()) {

                continue;
            }


            /*
    |--------------------------------------------------------------------------
    | Store Refinement Questions
    |--------------------------------------------------------------------------
    */
            /*
|--------------------------------------------------------------------------
| Merge Into Unified Refinement Pool
|--------------------------------------------------------------------------
*/

            $refinementSignsPool =

                $refinementSignsPool->merge(
                    $candidateSigns
                );
        }

        /*
|--------------------------------------------------------------------------
| Remove Duplicate Clinical Signs
|--------------------------------------------------------------------------
*/

        $refinementSignsPool =

            $refinementSignsPool
            ->unique(function ($sign) {

                return strtolower(

                    trim(
                        $sign->canonical_name
                    )
                );
            })

            ->values();

        $refinementSignsPool =

            $refinementSignsPool

            ->sortByDesc(function ($sign) {

                return

                    $sign->pivot->weight

                    ?? 0;
            })

            ->take(15)

            ->values();


        return view(

            'diagnostic.refinement',

            compact(

                'results',

                'selectedSigns',

                'refinementSignsPool'
            )
        );
    }

    public function refinedResults(
        Request $request
    ) {

        /*
    |--------------------------------------------------------------------------
    | Original + Refinement Signs
    |--------------------------------------------------------------------------
    */

        $selectedSigns =

            $request->clinical_signs ?? [];

        $refinementSigns =

            $request->refinement_signs ?? [];

        $hostSpeciesId =

            $request->host_species_id;


        /*
    |--------------------------------------------------------------------------
    | Merge All Signs
    |--------------------------------------------------------------------------
    */

        $allSigns = array_unique(

            array_merge(

                $selectedSigns,

                $refinementSigns
            )
        );


        /*
    |--------------------------------------------------------------------------
    | Disease Retrieval
    |--------------------------------------------------------------------------
    */

        $diseases = Disease::query()

            ->with([
                'clinicalSigns',
                'hostSpecies',
            ]);


        if ($hostSpeciesId) {

            $diseases->whereHas(

                'hostSpecies',

                function ($query) use ($hostSpeciesId) {

                    $query->where(
                        'host_species.id',
                        $hostSpeciesId
                    )

                        ->where(function ($q) {

                            $q->where(
                                'is_primary_host',
                                true
                            )

                                ->orWhere(
                                    'susceptibility',
                                    'high'
                                )

                                ->orWhere(
                                    'susceptibility',
                                    'moderate'
                                );
                        });
                }
            );
        }


        $diseases = $diseases->get();


        $results = [];


        /*
    |--------------------------------------------------------------------------
    | Diagnostic Recalculation
    |--------------------------------------------------------------------------
    */

        foreach ($diseases as $disease) {

            $score = 0;
            /*
|--------------------------------------------------------------------------
| Species Diagnostic Boost
|--------------------------------------------------------------------------
*/

            if ($hostSpeciesId) {

                $hostRelation =

                    $disease->hostSpecies

                    ->firstWhere(
                        'id',
                        $hostSpeciesId
                    );

                if ($hostRelation) {

                    /*
        |--------------------------------------------------------------------------
        | Primary Host Boost
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_primary_host

                    ) {

                        $score += 40;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Reservoir Boost
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_reservoir

                    ) {

                        $score += 10;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Carrier Boost
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_carrier

                    ) {

                        $score += 10;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Incidental Host Penalty
        |--------------------------------------------------------------------------
        */

                    if (

                        $hostRelation
                        ->pivot
                        ->is_incidental_host

                    ) {

                        $score -= 20;
                    }

                    /*
        |--------------------------------------------------------------------------
        | Susceptibility Weighting
        |--------------------------------------------------------------------------
        */

                    $susceptibility = strtolower(

                        $hostRelation
                            ->pivot
                            ->susceptibility

                            ?? ''
                    );

                    if (

                        str_contains(
                            $susceptibility,
                            'high'
                        )

                    ) {

                        $score += 20;
                    }

                    if (

                        str_contains(
                            $susceptibility,
                            'low'
                        )

                    ) {

                        $score -= 10;
                    }
                }
            }

            $matchedSigns = [];

            $reasons = [];

            $matchedPathognomonic = 0;

            $matchedRequired = 0;

            $matchedSpecific = 0;


            foreach (

                $disease->clinicalSigns

                as $sign

            ) {

                if (

                    ! in_array(
                        $sign->id,
                        $allSigns
                    )

                ) {

                    continue;
                }


                $pivot = $sign->pivot;

                $weight =
                    $pivot->weight ?? 1;


                /*
            |--------------------------------------------------------------------------
            | Base Weight
            |--------------------------------------------------------------------------
            */

                $score += min($weight, 10);


                /*
            |--------------------------------------------------------------------------
            | Refinement Bonus
            |--------------------------------------------------------------------------
            */

                if (

                    in_array(
                        $sign->id,
                        $refinementSigns
                    )

                ) {

                    /*
                |--------------------------------------------------------------------------
                | Pathognomonic Boost
                |--------------------------------------------------------------------------
                */

                    if (

                        $pivot->is_pathognomonic

                    ) {

                        $score += 50;

                        $matchedPathognomonic++;

                        $reasons[] =

                            'Pathognomonic clinical finding confirmed.';
                    }


                    /*
                |--------------------------------------------------------------------------
                | Required Boost
                |--------------------------------------------------------------------------
                */

                    if (

                        $pivot->is_required

                    ) {

                        $score += 30;

                        $matchedRequired++;

                        $reasons[] =

                            'Core diagnostic feature identified.';
                    }


                    /*
                |--------------------------------------------------------------------------
                | Specific Boost
                |--------------------------------------------------------------------------
                */

                    if (

                        $pivot->is_specific

                    ) {

                        $score += 20;

                        $matchedSpecific++;

                        $reasons[] =

                            'Disease-specific clinical evidence identified.';
                    }


                    /*
                |--------------------------------------------------------------------------
                | High Weight Refinement
                |--------------------------------------------------------------------------
                */

                    if ($weight >= 7) {

                        $score += 15;
                    }
                }


                $matchedSigns[strtolower(
                    trim(
                        $sign->canonical_name
                    )
                )] = $sign->display_name;
            }


            /*
        |--------------------------------------------------------------------------
        | Ignore Empty Diseases
        |--------------------------------------------------------------------------
        */

            if (

                count($matchedSigns) === 0

            ) {

                continue;
            }


            /*
        |--------------------------------------------------------------------------
        | Confidence
        |--------------------------------------------------------------------------
        */

            $confidence = 'Low';

            if (

                $matchedPathognomonic >= 1

            ) {

                $confidence = 'High';
            } elseif (

                $matchedRequired >= 1

            ) {

                $confidence = 'Moderate';
            }


            /*
        |--------------------------------------------------------------------------
        | Match Type
        |--------------------------------------------------------------------------
        */

            $matchType =
                'Refined Match';


            if (

                $matchedPathognomonic >= 1

            ) {

                $matchType =
                    'Pathognomonic Reinforced Match';
            }


            /*
        |--------------------------------------------------------------------------
        | Store Result
        |--------------------------------------------------------------------------
        */
            /*
|--------------------------------------------------------------------------
| Missing Key Diagnostic Findings
|--------------------------------------------------------------------------
*/

            $missingKeyFindings = [];

            foreach (

                $disease->clinicalSigns

                as $diseaseSign

            ) {

                /*
    |--------------------------------------------------------------------------
    | Skip Already Selected
    |--------------------------------------------------------------------------
    */

                if (

                    in_array(
                        $diseaseSign->id,
                        $allSigns
                    )

                ) {

                    continue;
                }

                $pivot =
                    $diseaseSign->pivot;

                /*
    |--------------------------------------------------------------------------
    | Pathognomonic
    |--------------------------------------------------------------------------
    */

                if (

                    $pivot->is_pathognomonic

                ) {

                    $missingKeyFindings[] =

                        $diseaseSign->display_name;

                    continue;
                }

                /*
    |--------------------------------------------------------------------------
    | Required
    |--------------------------------------------------------------------------
    */

                if (

                    $pivot->is_required

                ) {

                    $missingKeyFindings[] =

                        $diseaseSign->display_name;

                    continue;
                }

                /*
    |--------------------------------------------------------------------------
    | Highly Specific
    |--------------------------------------------------------------------------
    */

                if (

                    $pivot->is_specific

                    &&

                    $pivot->weight >= 7

                ) {

                    $missingKeyFindings[] =

                        $diseaseSign->display_name;
                }
            }

            $results[] = [

                'disease' => $disease,

                'score' => $score,

                'confidence' => $confidence,

                'match_type' => $matchType,

                'matched_signs' =>

                array_values(
                    $matchedSigns
                ),

                'reasons' =>

                collect($reasons)

                    ->unique()

                    ->values()

                    ->toArray(),

                'missing_key_findings' =>

                collect($missingKeyFindings)

                    ->unique()

                    ->take(5)

                    ->values()

                    ->toArray(),




            ];
        }


        /*
    |--------------------------------------------------------------------------
    | Relative Match Strength
    |--------------------------------------------------------------------------
    */

        $maxScore =

            collect($results)

            ->max('score');


        foreach ($results as &$result) {

            $result['relative_match_strength'] =

                $maxScore > 0

                ? max(

                    1,

                    min(

                        round(
                            (
                                $result['score']
                                / $maxScore
                            ) * 100
                        ),

                        100
                    )
                )

                : 0;
        }


        /*
    |--------------------------------------------------------------------------
    | Final Sorting
    |--------------------------------------------------------------------------
    */

        usort($results, function ($a, $b) {

            return

                $b['score']
                <=>
                $a['score'];
        });


        /*
    |--------------------------------------------------------------------------
    | Return Final Results
    |--------------------------------------------------------------------------
    */
        /*
|--------------------------------------------------------------------------
| Final Ranked Collections
|--------------------------------------------------------------------------
*/

        $resultsCollection =

            collect($results)

            ->sortByDesc(function ($result) {

                return [

                    $result['score']

                        ?? 0,

                    $result['relative_match_strength']

                        ?? 0,
                ];
            })

            ->values();


        /*
|--------------------------------------------------------------------------
| Primary Differential Diagnoses
|--------------------------------------------------------------------------
*/

        $primaryResults =

            $resultsCollection

            ->take(5);


        /*
|--------------------------------------------------------------------------
| Secondary Differential Diagnoses
|--------------------------------------------------------------------------
*/

        $secondaryResults =

            $resultsCollection

            ->slice(5, 5)

            ->values();


        /*
|--------------------------------------------------------------------------
| Prevent Duplicate Diseases
|--------------------------------------------------------------------------
*/

        $usedDiseaseIds =

            collect()

            ->merge(
                $primaryResults->pluck('disease.id')
            )

            ->merge(
                $secondaryResults->pluck('disease.id')
            )

            ->unique();


        /*
|--------------------------------------------------------------------------
| Low Clinical Support Diagnoses
|--------------------------------------------------------------------------
*/

        $lowSupportResults =

            $resultsCollection

            ->reject(function ($result) use ($usedDiseaseIds) {

                return $usedDiseaseIds->contains(

                    $result['disease']->id
                );
            })

            ->sortBy('score')

            ->take(5)

            ->values();

        return view(

            'diagnostic.results',

            compact(

                'results',

                'primaryResults',

                'secondaryResults',

                'lowSupportResults'
            )
        );
    }
}
