<?php

namespace App\Services\Narrative;

use App\Models\Disease;
use App\Models\LargeAnimals\Microorganism;

class DiseaseArticleBuilder
{
    public function build(Disease $disease): array
    {
        $payload = $this->normalizePayload($disease->knowledge_payload);

        $sections = [
            $this->overviewSection($disease),
        ];

        $microorganisms = $this->microorganismSection($disease);

        if ($microorganisms) {
            $sections[] = $microorganisms;
        }

        $signs = $this->clinicalSigns($disease, $payload);

        if ($signs) {
            $sections[] = [
                'id' => 'clinical-signs',
                'title' => __('large-animals.disease.clinical_signs'),
                'items' => $signs,
            ];
        }

        foreach ([
            'postmortem' => ['key' => 'postmortem_findings', 'title' => 'postmortem_findings', 'label' => 'display_name'],
            'diagnosis' => ['key' => 'diagnosis', 'title' => 'diagnosis', 'label' => 'method'],
            'treatment' => ['key' => 'treatment', 'title' => 'treatment', 'label' => 'intervention'],
            'prevention' => ['key' => 'prevention_control', 'title' => 'prevention_control', 'label' => 'measure'],
        ] as $id => $config) {
            $items = collect($payload[$config['key']] ?? [])
                ->map(fn (array $item) => [
                    'label' => $this->localized($item, $config['label']),
                    'description' => $this->localized($item, 'description'),
                ])
                ->filter(fn (array $item) => filled($item['label']))
                ->values()
                ->toArray();

            if ($items) {
                $sections[] = [
                    'id' => $id,
                    'title' => __('large-animals.disease.'.$config['title']),
                    'items' => $items,
                ];
            }
        }

        $references = $payload['references'] ?? [];

        if ($references) {
            $sections[] = [
                'id' => 'references',
                'title' => __('large-animals.disease.references'),
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
            'title' => $this->localizedString($disease->name_ar, $disease->name),
            'slug' => $disease->slug,
            'summary' => $this->localizedString($disease->description_ar, $disease->description),
            'etiology_type' => $disease->diseaseClassification?->etiology_type,
            'sections' => $sections,
        ];
    }

    private function localized(array $item, string $field): mixed
    {
        if (app()->getLocale() === 'ar' && filled($item[$field.'_ar'] ?? null)) {
            return $item[$field.'_ar'];
        }

        return $item[$field] ?? null;
    }

    private function localizedString(mixed $arabic, mixed $english): mixed
    {
        return app()->getLocale() === 'ar' && filled($arabic) ? $arabic : $english;
    }

    private function overviewSection(Disease $disease): array
    {
        $items = [];

        if ($etiology = $disease->diseaseClassification?->etiology_type) {
            $items[] = [
                'label' => __('large-animals.disease.etiology_type'),
                'description' => $etiology,
            ];
        }

        $species = $disease->hostSpecies
            ->pluck('localized_display_name')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        if ($species->isNotEmpty()) {
            $items[] = [
                'label' => __('large-animals.disease.host_species'),
                'description' => $species->implode(', '),
            ];
        }

        return [
            'id' => 'overview',
            'title' => __('large-animals.disease.overview'),
            'items' => $items,
        ];
    }

    private function microorganismSection(Disease $disease): array
    {
        $microorganisms = $disease->microorganisms
            ->map(fn (Microorganism $microorganism) => [
                'name' => $microorganism->name,
                'slug' => $microorganism->slug,
                'role' => $microorganism->pivot->role ?? 'cause',
            ])
            ->filter(fn (array $item) => filled($item['name']))
            ->values()
            ->toArray();

        if (! $microorganisms) {
            return [];
        }

        return [
            'id' => 'microorganisms',
            'title' => __('large-animals.disease.microorganisms'),
            'items' => $microorganisms,
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
                    'display_name_ar' => $sign->display_name_ar,
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
                'label' => $this->localized($item, 'display_name') ?? $item['canonical_name'] ?? null,
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
