<?php

namespace Database\Seeders;

use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\Species;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Loads the legacy poultry SQL dump into the shared knowledge tables.
 *
 * Every row is written with updateOrCreate so re-running after a corrected
 * dump refreshes the data instead of duplicating it. Rows are tagged with the
 * `poultry` domain, which keeps them separate from Large Animals in the same
 * tables rather than forking the schema per animal group.
 *
 * Dump columns deliberately NOT loaded, and why:
 *
 *   diseases.agent_type / agent_name / family / genus   no column; the same
 *       facts live in the JSON knowledge payload under `etiology`.
 *   diseases.incubation_period                         ditto (`incubation_period`).
 *   diseases.morbidity_min / morbidity_max             ditto (`morbidity`).
 *   diseases.mortality_min / mortality_max             ditto (`mortality`).
 *   findings.body_system                               free text; `findings` has
 *       no body_system_id column, so the grouping is deferred to the Phase 8
 *       body map work rather than invented here.
 *   disease_finding.negative_finding                   `disease_clinical_sign`
 *       has no matching column.
 *   disease_finding.likelihood_ratio_positive/negative ditto.
 *   disease_age_groups (179 rows)                      no table in this schema.
 *   diagnostic_tests / diagnostic_sessions / risk_factors / transmission_routes
 *       out of Phase 1 scope.
 */
class PoultryDataSeeder extends Seeder
{
    /** @var array<string, string> dump finding type => [ontology_type, category] */
    private const FINDING_TYPES = [
        'clinical_sign' => ['sign', 'clinical'],
        'gross_lesion' => ['lesion', 'postmortem'],
    ];

    /** Specificity values that count as a specific finding for the engine. */
    private const SPECIFIC_VALUES = ['high', 'very_high'];

    /**
     * Arabic display names for the poultry species taxonomy.
     *
     * @var array<string, string>
     */
    private const SPECIES_ARABIC = [
        'chicken' => 'دجاج',
        'broiler' => 'بداري تسمين',
        'layer' => 'دجاج بياض',
        'turkey' => 'رومي',
        'duck' => 'بط',
        'goose' => 'إوز',
        'pigeon' => 'حمام',
        'quail' => 'سمان',
        'ostrich' => 'نعام',
    ];

    /** @var array<string, array{created: int, updated: int}> */
    private array $summary = [];

    public function run(): void
    {
        $path = (string) config('vetpedia.poultry.sql_dump');

        if (! is_file($path)) {
            $this->command?->error("Poultry SQL dump not found at [{$path}].");

            return;
        }

        $dump = $this->readDump($path);

        if ($dump === []) {
            $this->command?->error('Poultry SQL dump contained no readable INSERT statements.');

            return;
        }

        $domain = (string) config('vetpedia.poultry.domain', Disease::DOMAIN_POULTRY);

        DB::transaction(function () use ($dump, $domain): void {
            $bodySystems = $this->seedBodySystems($dump);
            $this->seedAnatomicalStructures($dump, $bodySystems);
            $species = $this->seedSpecies($dump);
            $diseases = $this->seedDiseases($dump, $domain);
            $findings = $this->seedFindings($dump);
            $signs = $this->seedClinicalSigns($findings);
            $this->seedDiseaseFindings($dump, $diseases, $signs);
            $this->seedDiseaseHostSpecies($dump, $diseases, $species);
        });

        $this->report();
    }

    /**
     * Upsert a model, counting whether the row was created or refreshed.
     *
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     */
    private function upsert(string $model, array $attributes, array $values): Model
    {
        $instance = $model::firstOrNew($attributes);

        $existed = $instance->exists;
        $instance->fill($values)->save();

        $this->count(class_basename($model), $existed);

        return $instance;
    }

    /**
     * Upsert a pivot row, counting whether the row was created or refreshed.
     *
     * @param  array<string, mixed>  $keys
     * @param  array<string, mixed>  $values
     */
    private function upsertPivot(string $table, array $keys, array $values, bool $timestamps = false): void
    {
        $existed = DB::table($table)->where($keys)->exists();

        $payload = $values;

        if ($timestamps) {
            $payload['updated_at'] = now();

            if (! $existed) {
                $payload['created_at'] = now();
            }
        }

        DB::table($table)->updateOrInsert($keys, $payload);

        $this->count($table, $existed);
    }

    private function count(string $label, bool $existed): void
    {
        $bucket = $this->summary[$label] ?? ['created' => 0, 'updated' => 0];
        $bucket[$existed ? 'updated' : 'created']++;
        $this->summary[$label] = $bucket;
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $dump
     * @return array<string, int> dump body_system id => local id
     */
    private function seedBodySystems(array $dump): array
    {
        $map = [];

        foreach ($dump['body_systems'] ?? [] as $row) {
            $bodySystem = $this->upsert(
                BodySystem::class,
                ['canonical_name' => $row['canonical_name']],
                [
                    // Only set on create: existing display names are already curated.
                    'display_name' => $this->normalizeLabel($row['display_name'])
                        ?: Str::headline($row['canonical_name']),
                ],
            );

            $map[(string) $row['id']] = (int) $bodySystem->id;
        }

        return $map;
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $dump
     * @param  array<string, int>  $bodySystems
     */
    private function seedAnatomicalStructures(array $dump, array $bodySystems): void
    {
        $map = [];

        // First pass: every structure, so children can resolve their parent.
        foreach ($dump['anatomical_structures'] ?? [] as $row) {
            $structure = $this->upsert(
                AnatomicalStructure::class,
                ['canonical_name' => $row['canonical_name']],
                [
                    'display_name' => $this->normalizeLabel($row['display_name'])
                        ?: Str::headline($row['canonical_name']),
                    'body_system_id' => $bodySystems[(string) $row['body_system_id']] ?? null,
                ],
            );

            $map[(string) $row['id']] = $structure;
        }

        // Second pass: wire up the self-referencing hierarchy.
        foreach ($dump['anatomical_structures'] ?? [] as $row) {
            $parentId = $row['parent_id'] === null ? null : ($map[(string) $row['parent_id']] ?? null);

            $structure = $map[(string) $row['id']] ?? null;

            if ($structure !== null && $parentId !== null && $structure->parent_id !== $parentId->id) {
                $structure->parent_id = $parentId->id;
                $structure->save();
            }
        }
    }

    /**
     * Seed the poultry taxonomy into both host_species (diseases) and species
     * (drugs), since the two tables serve different consumers.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $dump
     * @return array<string, int> dump species id => host_species id
     */
    private function seedSpecies(array $dump): array
    {
        $domain = (string) config('vetpedia.poultry.domain', Disease::DOMAIN_POULTRY);
        $map = [];

        foreach ($dump['species'] ?? [] as $row) {
            $canonical = Str::slug($row['name']);
            $display = $row['name'];
            $arabic = self::SPECIES_ARABIC[$canonical] ?? $display;

            $hostSpecies = $this->upsert(
                HostSpecies::class,
                ['canonical_name' => $canonical],
                [
                    'display_name' => $display,
                    'display_name_ar' => $arabic,
                    'taxonomy_group' => 'poultry',
                    'is_domestic' => true,
                    'is_wildlife' => false,
                    'is_human' => false,
                ],
            );

            $this->upsert(
                Species::class,
                ['slug' => $canonical],
                [
                    'name' => $display,
                    'name_ar' => $arabic,
                    'is_active' => true,
                    'domain' => $domain,
                ],
            );

            $map[(string) $row['id']] = (int) $hostSpecies->id;
        }

        return $map;
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $dump
     * @return array<string, string> dump disease id => local id
     */
    private function seedDiseases(array $dump, string $domain): array
    {
        $map = [];

        foreach ($dump['diseases'] ?? [] as $row) {
            $disease = $this->upsert(
                Disease::class,
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'] ?: null,
                    'is_active' => true,
                    'is_zoonotic' => (bool) $row['zoonotic'],
                    'domain' => $domain,
                ],
            );

            $map[(string) $row['id']] = $disease->id;
        }

        return $map;
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $dump
     * @return array<string, int> dump finding id => local finding id
     */
    private function seedFindings(array $dump): array
    {
        $map = [];

        foreach ($dump['findings'] ?? [] as $row) {
            // The fallback was `['finding', null]`, which is not a value anything
            // in this application recognises — it matched neither the atlas filter
            // nor the body map's test, so an unmapped dump type produced a row
            // that appeared on no page. An unknown type is a clinical sign, which
            // is the same rule `Disease::signItems()` applies.
            [$ontologyType, $category] = self::FINDING_TYPES[$row['type']] ?? ['sign', null];

            // The dump mixes "sudden death" with "sudden_death" for the same
            // concept. Normalising to spaces lets both collapse onto the one
            // canonical row our schema already expects.
            $canonical = $this->normalizeLabel($row['canonical_name']);

            $finding = $this->upsert(
                Finding::class,
                ['canonical_name' => $canonical],
                [
                    // display_name is left alone on existing rows; these are curated.
                    'display_name' => $this->normalizeLabel($row['display_name']) ?: Str::headline($canonical),
                    'slug' => Str::slug($canonical),
                    'ontology_type' => $ontologyType,
                    'category' => $category,
                ],
            );

            $map[(string) $row['id']] = (int) $finding->id;
        }

        return $map;
    }

    /**
     * Turn a snake_case or hyphenated source label into a spaced one.
     *
     * The dump spells the same finding three ways ("sudden death",
     * "sudden_death", "sudden-death"). Collapsing all of them onto one
     * canonical name lets duplicates merge instead of fighting over the
     * unique slug.
     */
    private function normalizeLabel(?string $value): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        $spaced = preg_replace('/[_\-\s]+/', ' ', $value) ?? $value;

        // "Inability_to_move" reads as a heading; "weight loss" reads as prose.
        return Str::startsWith($spaced, Str::upper($spaced))
            ? lcfirst(Str::headline($spaced))
            : $spaced;
    }

    /**
     * Give every finding a plain clinical sign so the diagnostic engine, which
     * walks disease -> clinical_signs, can see it. Compositions with a modifier
     * or an anatomical structure are left to the curation phase.
     *
     * @param  array<string, int>  $findings  dump finding id => local finding id
     * @return array<string, int> dump finding id => clinical_sign id
     */
    private function seedClinicalSigns(array $findings): array
    {
        $map = [];
        $dumpIdByFindingId = array_flip($findings);

        $local = Finding::query()->whereIn('id', $findings)->get()->keyBy('id');

        foreach ($local as $finding) {
            $sign = $this->upsert(
                ClinicalSign::class,
                [
                    'finding_id' => $finding->id,
                    'modifier_id' => null,
                    'anatomical_structure_id' => null,
                ],
                [
                    'canonical_name' => $finding->canonical_name,
                    'display_name' => $finding->display_name,
                    'display_name_ar' => $finding->display_name_ar,
                    'stage' => $finding->ontology_type === 'lesion' ? 'postmortem' : 'clinical',
                ],
            );

            $map[(string) $dumpIdByFindingId[$finding->id]] = (int) $sign->id;
        }

        return $map;
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $dump
     * @param  array<string, string>  $diseases
     * @param  array<string, int>  $signs
     */
    private function seedDiseaseFindings(array $dump, array $diseases, array $signs): void
    {
        foreach ($dump['disease_finding'] ?? [] as $row) {
            $diseaseId = $diseases[(string) $row['disease_id']] ?? null;
            $signId = $signs[(string) $row['finding_id']] ?? null;

            if ($diseaseId === null || $signId === null) {
                continue;
            }

            $this->upsertPivot(
                'disease_clinical_sign',
                ['disease_id' => $diseaseId, 'clinical_sign_id' => $signId],
                [
                    'weight' => (int) $row['weight'],
                    'is_specific' => in_array($row['specificity'], self::SPECIFIC_VALUES, true),
                    'is_required' => (bool) $row['required_for_diagnosis'],
                    'is_pathognomonic' => (bool) $row['is_pathognomonic'],
                ],
                timestamps: false,
            );
        }
    }

    /**
     * Link each disease to the birds it affects. The first species recorded for
     * a disease is treated as the primary host; the rest are secondary.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $dump
     * @param  array<string, string>  $diseases
     * @param  array<string, int>  $species
     */
    private function seedDiseaseHostSpecies(array $dump, array $diseases, array $species): void
    {
        $primaryAssigned = [];

        foreach ($dump['disease_species'] ?? [] as $row) {
            $diseaseId = $diseases[(string) $row['disease_id']] ?? null;
            $hostSpeciesId = $species[(string) $row['species_id']] ?? null;

            if ($diseaseId === null || $hostSpeciesId === null) {
                continue;
            }

            $isPrimary = ! isset($primaryAssigned[$diseaseId]);
            $primaryAssigned[$diseaseId] = true;

            $this->upsertPivot(
                'disease_host_species',
                ['disease_id' => $diseaseId, 'host_species_id' => $hostSpeciesId],
                [
                    'role' => 'host',
                    'susceptibility' => $isPrimary ? 'high' : 'moderate',
                    'is_primary_host' => $isPrimary,
                    'is_reservoir' => false,
                    'is_vector' => false,
                    'is_carrier' => false,
                    'is_incidental_host' => false,
                ],
                timestamps: true,
            );
        }
    }

    private function report(): void
    {
        $this->command?->newLine();
        $this->command?->info('Poultry data loaded:');

        foreach ($this->summary as $label => $counts) {
            $this->command?->line(sprintf(
                '  %-22s created %5d   updated %5d',
                $label,
                $counts['created'],
                $counts['updated'],
            ));
        }
    }

    /**
     * Parse a phpMyAdmin dump into rows keyed by table.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function readDump(string $path): array
    {
        $sql = (string) file_get_contents($path);

        $pattern = '/INSERT INTO `([^`]+)` \((.+?)\) VALUES\s*(.*?);\s*$/ms';

        if (preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER) === false) {
            return [];
        }

        $dump = [];

        foreach ($matches as $match) {
            $columns = array_map(fn (string $column): string => trim($column, ' `'), explode(',', $match[2]));

            foreach ($this->splitTuples($match[3]) as $tuple) {
                $values = array_map($this->decodeValue(...), $this->splitValues($tuple));

                if (count($values) !== count($columns)) {
                    continue;
                }

                $dump[$match[1]][] = array_combine($columns, $values);
            }
        }

        return $dump;
    }

    /**
     * Split a VALUES body into parenthesised tuples, honouring quoted strings
     * and backslash escapes.
     *
     * @return array<int, string>
     */
    private function splitTuples(string $values): array
    {
        $tuples = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $length = strlen($values);

        for ($i = 0; $i < $length; $i++) {
            $char = $values[$i];

            if ($inString) {
                $current .= $char;

                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $values[++$i];

                    continue;
                }

                if ($char === "'") {
                    $inString = false;
                }

                continue;
            }

            if ($char === "'") {
                $inString = true;
                $current .= $char;

                continue;
            }

            if ($char === '(') {
                $depth++;

                if ($depth === 1) {
                    $current = '';

                    continue;
                }
            } elseif ($char === ')') {
                $depth--;

                if ($depth === 0) {
                    $tuples[] = $current;
                    $current = '';

                    continue;
                }
            }

            if ($depth > 0) {
                $current .= $char;
            }
        }

        return $tuples;
    }

    /**
     * Split one tuple into its column values, dropping the quotes.
     *
     * @return array<int, string>
     */
    private function splitValues(string $tuple): array
    {
        $values = [];
        $current = '';
        $inString = false;
        $length = strlen($tuple);

        for ($i = 0; $i < $length; $i++) {
            $char = $tuple[$i];

            if ($inString) {
                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $tuple[++$i];

                    continue;
                }

                if ($char === "'") {
                    $inString = false;

                    continue;
                }

                $current .= $char;

                continue;
            }

            if ($char === "'") {
                $inString = true;

                continue;
            }

            if ($char === ',') {
                $values[] = trim($current);
                $current = '';

                continue;
            }

            $current .= $char;
        }

        $values[] = trim($current);

        return $values;
    }

    private function decodeValue(string $value): ?string
    {
        if ($value === 'NULL') {
            return null;
        }

        if (strlen($value) > 1 && $value[0] === "'" && str_ends_with($value, "'")) {
            return str_replace(["\\'", '\\\\'], ["'", '\\'], substr($value, 1, -1));
        }

        return $value === '' ? null : $value;
    }
}
