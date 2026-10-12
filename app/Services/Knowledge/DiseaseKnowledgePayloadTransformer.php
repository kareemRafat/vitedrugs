<?php

namespace App\Services\Knowledge;

class DiseaseKnowledgePayloadTransformer
{
    /**
     * Output keys produced for every disease, in render order.
     *
     * @var array<int, string>
     */
    public const KEYS = [
        'clinical_signs',
        'postmortem_findings',
        'microscopic_lesions',
        'diagnosis',
        'differential_diagnoses',
        'treatment',
        'prevention_control',
        'pathognomonic_findings',
        'field_diagnosis_clues',
        'epidemiological_clues',
        'important_organs_affected',
        'morbidity',
        'mortality',
        'references',
    ];

    /**
     * Source key aliases, in priority order, for each output key.
     *
     * The poultry knowledge documents use a different vocabulary from the
     * public pages: they call gross lesions "gross_lesions", diagnostic tests
     * "diagnostic_tests" and prevention "prevention_and_control".
     *
     * @var array<string, array<int, string>>
     */
    private const SOURCES = [
        'clinical_signs' => ['clinical_manifestations', 'clinical_signs'],
        'postmortem_findings' => ['gross_lesions', 'postmortem_findings'],
        'microscopic_lesions' => ['microscopic_lesions'],
        'diagnosis' => ['diagnostic_tests', 'diagnostic_methods', 'diagnosis'],
        'differential_diagnoses' => ['differential_diagnoses'],
        'treatment' => ['treatment'],
        'prevention_control' => ['prevention_and_control', 'prevention_control'],
        'pathognomonic_findings' => ['pathognomonic_findings'],
        'field_diagnosis_clues' => ['field_diagnosis_clues'],
        'epidemiological_clues' => ['epidemiological_clues'],
        'important_organs_affected' => ['important_organs_affected'],
        'morbidity' => ['morbidity'],
        'mortality' => ['mortality'],
        'references' => ['references'],
    ];

    /**
     * Normalize an uploaded disease knowledge payload into the flat shape the
     * public pages render.
     *
     * Accepts either a rich disease document wrapped in a top-level "disease"
     * key or an already-flat payload. Source items are preserved as-is and only
     * augmented with the fields the pages expect when those are missing.
     */
    public static function transform(mixed $decoded): array
    {
        if (! is_array($decoded)) {
            return self::emptyPayload();
        }

        $payload = $decoded['disease'] ?? $decoded;

        if (! is_array($payload)) {
            return self::emptyPayload();
        }

        return [
            'clinical_signs' => self::mapClinicalSigns(self::pick($payload, self::SOURCES['clinical_signs'])),
            'postmortem_findings' => self::mapPostmortemFindings(self::pick($payload, self::SOURCES['postmortem_findings'])),
            'microscopic_lesions' => self::mapScalarList(self::pick($payload, self::SOURCES['microscopic_lesions'])),
            'diagnosis' => self::mapDiagnosis(self::pick($payload, self::SOURCES['diagnosis'])),
            'differential_diagnoses' => self::mapScalarList(self::pick($payload, self::SOURCES['differential_diagnoses'])),
            'treatment' => self::mapTreatment(self::pick($payload, self::SOURCES['treatment'])),
            'prevention_control' => self::mapPreventionControl(self::pick($payload, self::SOURCES['prevention_control'])),
            'pathognomonic_findings' => self::mapScalarList(self::pick($payload, self::SOURCES['pathognomonic_findings'])),
            'field_diagnosis_clues' => self::mapScalarList(self::pick($payload, self::SOURCES['field_diagnosis_clues'])),
            'epidemiological_clues' => self::mapScalarList(self::pick($payload, self::SOURCES['epidemiological_clues'])),
            'important_organs_affected' => self::mapScalarList(self::pick($payload, self::SOURCES['important_organs_affected'])),
            'morbidity' => self::mapRange(self::pickRaw($payload, self::SOURCES['morbidity'])),
            'mortality' => self::mapRange(self::pickRaw($payload, self::SOURCES['mortality'])),
            'references' => self::mapEntries(self::pick($payload, self::SOURCES['references']), fn (array $item): array => $item),
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private static function emptyPayload(): array
    {
        return array_fill_keys(self::KEYS, []);
    }

    /**
     * Return the first non-empty value among the given source keys.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>  $keys
     */
    private static function pick(array $payload, array $keys): array
    {
        return self::pickRaw($payload, $keys) ?? [];
    }

    /**
     * Return the first non-null value among the given source keys, unconverted.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<int, string>  $keys
     */
    private static function pickRaw(array $payload, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = $payload[$key] ?? null;

            if ($value !== null && $value !== []) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Reduce a list to plain strings, dropping blanks and duplicates.
     *
     * @param  array<int, mixed>  $items
     * @return array<int, string>
     */
    private static function mapScalarList(array $items): array
    {
        $mapped = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                $value = trim($item);
            } elseif (is_scalar($item)) {
                $value = trim((string) $item);
            } else {
                continue;
            }

            if ($value !== '') {
                $mapped[] = $value;
            }
        }

        return array_values(array_unique($mapped));
    }

    /**
     * Normalize a morbidity/mortality block into a displayable range.
     *
     * The source uses a `minimum_percent` / `maximum_percent` / `notes` trio.
     * A minority of documents instead break the figures down per age group, so
     * any unrecognised key is preserved as a flat breakdown list.
     *
     * @return array{min: float|int|null, max: float|int|null, notes: string|null, breakdown: array<int, string>}
     */
    private static function mapRange(mixed $value): array
    {
        $empty = ['min' => null, 'max' => null, 'notes' => null, 'breakdown' => []];

        if (! is_array($value)) {
            return $empty;
        }

        $notes = $value['notes'] ?? null;
        $breakdown = $value;

        unset($breakdown['notes'], $breakdown['minimum_percent'], $breakdown['maximum_percent']);

        $min = self::toPercent($value['minimum_percent'] ?? null);
        $max = self::toPercent($value['maximum_percent'] ?? null);

        if ($min !== null && $max !== null && $min > $max) {
            [$min, $max] = [$max, $min];
        }

        return [
            'min' => $min,
            'max' => $max,
            'notes' => is_string($notes) && trim($notes) !== '' ? trim($notes) : null,
            'breakdown' => self::mapScalarList($breakdown),
        ];
    }

    private static function toPercent(mixed $value): float|int|null
    {
        return is_numeric($value) ? $value + 0 : null;
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapClinicalSigns(array $items): array
    {
        return self::mapEntries($items, function (array $item): array {
            if (isset($item['display_name'], $item['weight'], $item['is_required'])) {
                return $item;
            }

            return [
                'canonical_name' => $item['canonical_name'] ?? null,
                'display_name' => $item['display_name'] ?? $item['canonical_name'] ?? $item['finding'] ?? null,
                'description' => $item['notes'] ?? null,
                'body_system' => $item['body_system'] ?? null,
                'severity' => $item['severity'] ?? null,
                'weight' => $item['weight'] ?? $item['diagnostic_weight'] ?? null,
                'is_specific' => in_array($item['specificity'] ?? null, ['high', 'very_high'], true),
                'is_required' => (bool) ($item['is_required'] ?? false),
                'is_pathognomonic' => (bool) ($item['is_pathognomonic'] ?? false),
                'notes' => $item['notes'] ?? null,
            ];
        });
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapPostmortemFindings(array $items): array
    {
        return self::mapEntries($items, function (array $item): array {
            if (isset($item['display_name'], $item['canonical_name'])) {
                return $item;
            }

            return [
                'canonical_name' => $item['canonical_name'] ?? null,
                'display_name' => $item['display_name'] ?? $item['canonical_name'] ?? $item['finding'] ?? null,
                'description' => $item['description'] ?? $item['notes'] ?? null,
                'anatomical_location' => $item['anatomical_location'] ?? null,
                'weight' => $item['weight'] ?? null,
                'specificity' => $item['specificity'] ?? null,
                'is_pathognomonic' => (bool) ($item['is_pathognomonic'] ?? false),
                'notes' => $item['notes'] ?? null,
            ];
        });
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapDiagnosis(array $items): array
    {
        return self::mapEntries($items, function (array $item): array {
            if (isset($item['method'])) {
                return $item;
            }

            return [
                'method' => $item['method'] ?? $item['test_name'] ?? $item['name'] ?? null,
                'sample' => $item['sample'] ?? null,
                'description' => $item['description'] ?? $item['purpose'] ?? $item['priority'] ?? null,
            ];
        });
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapTreatment(array $items): array
    {
        return self::mapEntries($items, function (array $item): array {
            if (! isset($item['description']) && isset($item['indication'])) {
                $item['description'] = $item['indication'];
            }

            return $item;
        });
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function mapPreventionControl(array $items): array
    {
        return self::mapEntries($items, function (array $item): array {
            if (! isset($item['description']) && isset($item['target'])) {
                $item['description'] = $item['target'];
            }

            return $item;
        });
    }

    /**
     * Apply a mapper to every associative entry in a list, passing plain
     * strings through as single-field entries so mixed source data survives.
     *
     * @param  array<int, mixed>  $items
     * @param  callable(array<string, mixed>): array<string, mixed>  $mapper
     * @return array<int, array<string, mixed>>
     */
    private static function mapEntries(array $items, callable $mapper): array
    {
        $mapped = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $mapped[] = $mapper($item);

                continue;
            }

            if (is_string($item) && trim($item) !== '') {
                $mapped[] = ['description' => trim($item)];
            }
        }

        return $mapped;
    }
}
