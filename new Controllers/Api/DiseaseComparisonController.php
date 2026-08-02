<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompareDiseasesRequest;
use App\Http\Resources\DiseaseComparisonResource;
use App\Models\Disease;

class DiseaseComparisonController extends Controller
{
    public function compare(
        CompareDiseasesRequest $request
    ) {
        $slugs = collect(
            $request->validated()['slugs']
        );

        $comparisonDiseases = Disease::query()

            ->whereIn(
                'slug',
                $slugs
            )

            ->get()

            ->map(function ($disease) {

                $disease->payload = $this->normalizePayload(
                    $disease->knowledge_payload
                );

                return $disease;
            });

        $clinicalSigns = $this->buildPresenceMatrix(
            $comparisonDiseases,
            'payload.clinical_signs',
            'canonical_name'
        );

        $postmortemFindings = $this->buildPresenceMatrix(
            $comparisonDiseases,
            'payload.postmortem_findings',
            'canonical_name'
        );

        $diagnosticMethods = $this->buildPresenceMatrix(
            $comparisonDiseases,
            'payload.diagnosis',
            'method'
        );

        $treatments = $this->buildPresenceMatrix(
            $comparisonDiseases,
            'payload.treatment',
            'intervention'
        );

        $preventions = $this->buildPresenceMatrix(
            $comparisonDiseases,
            'payload.prevention_control',
            'measure'
        );

        return new DiseaseComparisonResource([

            'diseases' => $comparisonDiseases->map(
                fn($disease) => [

                    'name_en' => $disease->name_en,

                    'name_ar' => $disease->name_ar,

                    'slug' => $disease->slug,
                ]
            ),

            'clinical_signs' => $clinicalSigns,

            'postmortem_findings' => $postmortemFindings,

            'diagnostic_methods' => $diagnosticMethods,

            'treatments' => $treatments,

            'preventions' => $preventions,
        ]);
    }

    protected function normalizePayload(
        $payload
    ): array {

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

    protected function buildPresenceMatrix(
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
