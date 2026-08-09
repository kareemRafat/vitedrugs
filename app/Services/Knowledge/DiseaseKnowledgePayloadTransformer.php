<?php

namespace App\Services\Knowledge;

class DiseaseKnowledgePayloadTransformer
{
    /**
     * Normalize an uploaded disease knowledge payload into the flat shape the
     * public pages render (clinical_signs, postmortem_findings, diagnosis,
     * treatment, prevention_control, references).
     *
     * Accepts either a rich disease document wrapped in a top-level "disease"
     * key or an already-flat payload. Source items are preserved as-is and only
     * augmented with the fields the pages expect when those are missing.
     */
    public static function transform(mixed $decoded): array
    {
        if (! is_array($decoded)) {
            return [];
        }

        $payload = $decoded['disease'] ?? $decoded;

        if (! is_array($payload)) {
            return [];
        }

        return [
            'clinical_signs' => array_values(self::mapClinicalSigns($payload['clinical_manifestations'] ?? $payload['clinical_signs'] ?? [])),
            'postmortem_findings' => array_values(self::mapPostmortemFindings($payload['postmortem_findings'] ?? [])),
            'diagnosis' => array_values(self::mapDiagnosis($payload['diagnostic_methods'] ?? $payload['diagnosis'] ?? [])),
            'treatment' => array_values(self::mapTreatment($payload['treatment'] ?? [])),
            'prevention_control' => array_values(self::mapPreventionControl($payload['prevention_control'] ?? [])),
            'references' => array_values($payload['references'] ?? []),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapClinicalSigns(array $items): array
    {
        return array_map(function (array $item): array {
            if (isset($item['display_name'], $item['weight'], $item['is_required'])) {
                return $item;
            }

            return [
                'canonical_name' => $item['canonical_name'] ?? null,
                'display_name' => $item['canonical_name'] ?? $item['finding'] ?? null,
                'weight' => $item['diagnostic_weight'] ?? null,
                'is_specific' => ($item['specificity'] ?? null) === 'high',
                'is_required' => (bool) ($item['is_required'] ?? false),
                'is_pathognomonic' => false,
            ];
        }, array_values($items));
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapPostmortemFindings(array $items): array
    {
        return array_map(function (array $item): array {
            if (isset($item['display_name'])) {
                return $item;
            }

            return [
                'canonical_name' => $item['canonical_name'] ?? null,
                'display_name' => $item['canonical_name'] ?? $item['finding'] ?? null,
                'description' => $item['description'] ?? null,
            ];
        }, array_values($items));
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapDiagnosis(array $items): array
    {
        return array_map(function (array $item): array {
            if (isset($item['method'])) {
                return $item;
            }

            return [
                'method' => $item['method'] ?? $item['name'] ?? null,
                'description' => $item['description'] ?? $item['priority'] ?? null,
            ];
        }, array_values($items));
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapTreatment(array $items): array
    {
        return array_map(function (array $item): array {
            if (! isset($item['description']) && isset($item['indication'])) {
                $item['description'] = $item['indication'];
            }

            return $item;
        }, array_values($items));
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapPreventionControl(array $items): array
    {
        return array_map(function (array $item): array {
            if (! isset($item['description']) && isset($item['target'])) {
                $item['description'] = $item['target'];
            }

            return $item;
        }, array_values($items));
    }
}
