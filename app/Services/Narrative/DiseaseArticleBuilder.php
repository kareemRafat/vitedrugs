<?php

namespace App\Services\Narrative;

use App\Models\Disease;

class DiseaseArticleBuilder
{
    public function build(Disease $disease): array
    {
        $payload = $this->normalizePayload($disease->knowledge_payload);

        $sections = [
            $this->overviewSection($disease),
        ];

        $signs = $this->clinicalSigns($disease, $payload);

        if ($signs) {
            $sections[] = [
                'id' => 'clinical-signs',
                'title' => 'Clinical Signs',
                'items' => $signs,
            ];
        }

        foreach ([
            'postmortem' => ['key' => 'postmortem_findings', 'title' => 'Postmortem Findings', 'label' => 'display_name'],
            'diagnosis' => ['key' => 'diagnosis', 'title' => 'Diagnosis', 'label' => 'method'],
            'treatment' => ['key' => 'treatment', 'title' => 'Treatment', 'label' => 'intervention'],
            'prevention' => ['key' => 'prevention_control', 'title' => 'Prevention & Control', 'label' => 'measure'],
        ] as $id => $config) {
            $items = collect($payload[$config['key']] ?? [])
                ->map(fn (array $item) => [
                    'label' => $item[$config['label']] ?? null,
                    'description' => $item['description'] ?? null,
                ])
                ->filter(fn (array $item) => filled($item['label']))
                ->values()
                ->toArray();

            if ($items) {
                $sections[] = [
                    'id' => $id,
                    'title' => $config['title'],
                    'items' => $items,
                ];
            }
        }

        $references = $payload['references'] ?? [];

        if ($references) {
            $sections[] = [
                'id' => 'references',
                'title' => 'References',
                'items' => collect($references)
                    ->map(fn (array $item) => [
                        'label' => $item['title'] ?? null,
                        'url' => $item['url'] ?? null,
                    ])
                    ->filter(fn (array $item) => filled($item['label']))
                    ->values()
                    ->toArray(),
            ];
        }

        return [
            'title' => $disease->name,
            'slug' => $disease->slug,
            'summary' => $disease->description,
            'etiology_type' => $disease->diseaseClassification?->etiology_type,
            'sections' => $sections,
        ];
    }

    private function overviewSection(Disease $disease): array
    {
        $items = [];

        if ($etiology = $disease->diseaseClassification?->etiology_type) {
            $items[] = [
                'label' => 'Etiology',
                'description' => $etiology,
            ];
        }

        $species = $disease->hostSpecies
            ->pluck('display_name')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($species->isNotEmpty()) {
            $items[] = [
                'label' => 'Affected species',
                'description' => $species->implode(', '),
            ];
        }

        $microorganisms = $disease->microorganisms
            ->pluck('display_name')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($microorganisms->isNotEmpty()) {
            $items[] = [
                'label' => 'Associated microorganisms',
                'description' => $microorganisms->implode(', '),
            ];
        }

        return [
            'id' => 'overview',
            'title' => 'Overview',
            'items' => $items,
        ];
    }

    private function clinicalSigns(Disease $disease, array $payload): array
    {
        $signs = $payload['clinical_signs'] ?? [];

        if (! $signs) {
            $signs = $disease->clinicalSigns
                ->map(fn ($sign) => [
                    'canonical_name' => $sign->canonical_name,
                    'display_name' => $sign->display_name,
                    'weight' => $sign->pivot->weight,
                    'is_specific' => (bool) $sign->pivot->is_specific,
                    'is_required' => (bool) $sign->pivot->is_required,
                    'is_pathognomonic' => (bool) $sign->pivot->is_pathognomonic,
                ])
                ->values()
                ->toArray();
        }

        return collect($signs)
            ->map(fn (array $item) => [
                'label' => $item['display_name'] ?? $item['canonical_name'] ?? null,
                'description' => null,
                'meta' => [
                    'weight' => $item['weight'] ?? null,
                    'is_specific' => (bool) ($item['is_specific'] ?? false),
                    'is_required' => (bool) ($item['is_required'] ?? false),
                    'is_pathognomonic' => (bool) ($item['is_pathognomonic'] ?? false),
                ],
            ])
            ->filter(fn (array $item) => filled($item['label']))
            ->values()
            ->toArray();
    }

    private function normalizePayload(mixed $payload): array
    {
        if (is_array($payload)) {
            return $payload;
        }

        if (empty($payload)) {
            return [];
        }

        return json_decode((string) $payload, true) ?? [];
    }
}
