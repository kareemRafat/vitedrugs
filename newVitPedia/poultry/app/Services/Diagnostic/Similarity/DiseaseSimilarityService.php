<?php

namespace App\Services\Diagnostic\Similarity;

use App\Models\Disease;
use Illuminate\Support\Collection;

class DiseaseSimilarityService
{
    public function findSimilarDiseases(
        Disease $disease,
        int $limit = 5
    ): Collection {

        $targetFindings = $disease
            ->findings
            ->pluck('canonical_name')
            ->map(fn($f) => strtolower(trim($f)))
            ->toArray();

        $targetLesions = $disease
            ->lesions
            ->pluck('canonical_name')
            ->map(fn($l) => strtolower(trim($l)))
            ->toArray();

        $targetSpecies = $disease
            ->species
            ->pluck('id')
            ->toArray();

        $results = collect();

        $allDiseases = Disease::with([
            'findings',
            'lesions',
            'species'
        ])
        ->where('id', '!=', $disease->id)
        ->get();

        foreach ($allDiseases as $candidate) {

            $candidateFindings = $candidate
                ->findings
                ->pluck('canonical_name')
                ->map(fn($f) => strtolower(trim($f)))
                ->toArray();

            $candidateLesions = $candidate
                ->lesions
                ->pluck('canonical_name')
                ->map(fn($l) => strtolower(trim($l)))
                ->toArray();

            $candidateSpecies = $candidate
                ->species
                ->pluck('id')
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | Findings Similarity
            |--------------------------------------------------------------------------
            */

            $sharedFindings = count(
                array_intersect(
                    $targetFindings,
                    $candidateFindings
                )
            );

            $findingsSimilarity = 0;

            if (count($targetFindings) > 0) {

                $findingsSimilarity =
                    ($sharedFindings
                    / count($targetFindings))
                    * 100;
            }

            /*
            |--------------------------------------------------------------------------
            | Lesions Similarity
            |--------------------------------------------------------------------------
            */

            $sharedLesions = count(
                array_intersect(
                    $targetLesions,
                    $candidateLesions
                )
            );

            $lesionsSimilarity = 0;

            if (count($targetLesions) > 0) {

                $lesionsSimilarity =
                    ($sharedLesions
                    / max(
                        1,
                        count($targetLesions)
                    )) * 100;
            }

            /*
            |--------------------------------------------------------------------------
            | Species Similarity
            |--------------------------------------------------------------------------
            */

            $sharedSpecies = count(
                array_intersect(
                    $targetSpecies,
                    $candidateSpecies
                )
            );

            $speciesSimilarity =
                $sharedSpecies > 0 ? 100 : 0;

            /*
            |--------------------------------------------------------------------------
            | Final Score
            |--------------------------------------------------------------------------
            */

            $similarity = round(

                ($findingsSimilarity * 0.6)

                +

                ($lesionsSimilarity * 0.3)

                +

                ($speciesSimilarity * 0.1)

            );

            if ($similarity < 20) {
                continue;
            }

            $results->push([

                'disease' => $candidate,

                'similarity' => $similarity,

                'shared_findings' => $sharedFindings,

                'shared_lesions' => $sharedLesions,
            ]);
        }

        return $results
            ->sortByDesc('similarity')
            ->take($limit)
            ->values();
    }
}

