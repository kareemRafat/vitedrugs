<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseComparisonController extends Controller
{
    /**
     * Display comparison page
     */
    public function index()
    {
        return view(
            'diseases.comparison',
            [
                'allDiseases'        => $this->getAllDiseases(),
                'comparisonDiseases' => collect(),
                'selectedIds'        => [],
            ]
        );
    }

    /**
     * Compare selected diseases
     */
    public function compare(Request $request)
    {
        $selectedIds = collect(
            $request->input('disease_ids', [])
        )
            ->filter()
            ->unique()
            ->take(4)
            ->values()
            ->toArray();

        $comparisonDiseases = Disease::query()
            ->whereIn('id', $selectedIds)
            ->get()
            ->map(function ($disease) {

                $disease->payload = $this->normalizePayload(
                    $disease->knowledge_payload
                );

                return $disease;
            });


        $clinicalSigns = $this->buildComparisonMatrix(
            $comparisonDiseases,
            'payload.clinical_signs',
            'canonical_name'
        );

        $postmortemFindings = $this->buildComparisonMatrix(
            $comparisonDiseases,
            'payload.postmortem_findings',
            'canonical_name'
        );

        $diagnosticMethods = $this->buildComparisonMatrix(
            $comparisonDiseases,
            'payload.diagnosis',
            'method'
        );

        $treatments = $this->buildComparisonMatrix(
            $comparisonDiseases,
            'payload.treatment',
            'intervention'
        );

        $preventions = $this->buildComparisonMatrix(
            $comparisonDiseases,
            'payload.prevention_control',
            'measure'
        );


        return view(
            'diseases.comparison',
            [
                'allDiseases'        => $this->getAllDiseases(),
                'comparisonDiseases' => $comparisonDiseases,
                'selectedIds'        => $selectedIds,
                'clinicalSigns' => $clinicalSigns,

                'postmortemFindings' => $postmortemFindings,

                'diagnosticMethods' => $diagnosticMethods,

                'treatments' => $treatments,

                'preventions' => $preventions,
            ]
        );
    }


    /**
     * Diseases list for selector
     */
    private function getAllDiseases()
    {
        return Disease::query()
            ->select(
                'id',
                'name_en',
                'name_ar'
            )
            ->orderBy('name_en')
            ->get();
    }

    /**
     * Normalize knowledge payload
     */
    private function normalizePayload($payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (empty($payload)) {
            return [];
        }

        return json_decode(
            $payload,
            true
        ) ?? [];
    }

    private function buildComparisonMatrix(
        $comparisonDiseases,
        string $path,
        string $field
    ) {

        $items = [];

        foreach ($comparisonDiseases as $disease) {

            $values = collect(
                data_get(
                    $disease,
                    $path,
                    []
                )
            )

                ->pluck($field)

                ->filter()

                ->unique()

                ->values()

                ->toArray();

            $items = array_merge(
                $items,
                $values
            );
        }

        $items = collect($items)

            ->unique()

            ->sort()

            ->values();

        return $items;
    }
    private function buildPresenceMatrix(
        $comparisonDiseases,
        string $path,
        string $field
    ) {
        $allItems = [];

        foreach ($comparisonDiseases as $disease) {

            $values = collect(
                data_get(
                    $disease,
                    $path,
                    []
                )
            )
                ->pluck($field)
                ->filter()
                ->unique();

            $allItems = array_merge(
                $allItems,
                $values->toArray()
            );
        }

        $allItems = collect($allItems)
            ->unique()
            ->sort()
            ->values();

        return $allItems->map(function (
            $item
        ) use (
            $comparisonDiseases,
            $path,
            $field
        ) {

            $presence = [];

            foreach ($comparisonDiseases as $disease) {

                $values = collect(
                    data_get(
                        $disease,
                        $path,
                        []
                    )
                )
                    ->pluck($field)
                    ->filter()
                    ->unique();

                $presence[$disease->slug] =
                    $values->contains($item);
            }

            return [

                'name' => $item,

                'presence' => $presence,
            ];
        });
    }
}
