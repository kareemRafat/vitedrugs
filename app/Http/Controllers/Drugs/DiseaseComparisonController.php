<?php

namespace App\Http\Controllers\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseComparisonController extends Controller
{
    public function index()
    {
        return view('drugs.comparison.index', [
            'allDiseases' => $this->getAllDiseases(),
            'comparisonDiseases' => collect(),
            'selectedIds' => [],
        ]);
    }

    public function compare(Request $request)
    {
        $selectedIds = collect($request->input('disease_ids', []))
            ->filter()
            ->unique()
            ->take(4)
            ->values()
            ->toArray();

        $comparisonDiseases = Disease::query()
            ->whereIn('id', $selectedIds)
            ->get()
            ->map(function ($disease) {
                $disease->payload = $this->normalizePayload($disease->knowledge_payload);

                return $disease;
            });

        return view('drugs.comparison.results', [
            'allDiseases' => $this->getAllDiseases(),
            'comparisonDiseases' => $comparisonDiseases,
            'selectedIds' => $selectedIds,
            'clinicalSigns' => $this->buildPresenceMatrix($comparisonDiseases, 'payload.clinical_signs', 'canonical_name'),
            'postmortemFindings' => $this->buildPresenceMatrix($comparisonDiseases, 'payload.postmortem_findings', 'canonical_name'),
            'diagnosticMethods' => $this->buildPresenceMatrix($comparisonDiseases, 'payload.diagnosis', 'method'),
            'treatments' => $this->buildPresenceMatrix($comparisonDiseases, 'payload.treatment', 'intervention'),
            'preventions' => $this->buildPresenceMatrix($comparisonDiseases, 'payload.prevention_control', 'measure'),
        ]);
    }

    private function getAllDiseases()
    {
        return Disease::query()
            ->select('id', 'name', 'name_ar', 'slug')
            ->orderBy('name')
            ->get();
    }

    private function normalizePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (empty($payload)) {
            return [];
        }

        return json_decode($payload, true) ?? [];
    }

    private function buildPresenceMatrix($comparisonDiseases, string $path, string $field)
    {
        $allItems = [];

        foreach ($comparisonDiseases as $disease) {
            $values = collect(data_get($disease, $path, []))
                ->pluck($field)
                ->filter()
                ->unique();

            $allItems = array_merge($allItems, $values->toArray());
        }

        $allItems = collect($allItems)
            ->unique()
            ->sort()
            ->values();

        return $allItems->map(function ($item) use ($comparisonDiseases, $path, $field) {
            $presence = [];

            foreach ($comparisonDiseases as $disease) {
                $values = collect(data_get($disease, $path, []))
                    ->pluck($field)
                    ->filter()
                    ->unique();

                $presence[$disease->slug] = $values->contains($item);
            }

            return [
                'name' => $item,
                'presence' => $presence,
            ];
        });
    }
}
