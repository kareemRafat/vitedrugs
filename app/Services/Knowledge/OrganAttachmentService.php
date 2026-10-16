<?php

namespace App\Services\Knowledge;

use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\Finding;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Attaches diseases, signs and lesions to the organ they concern.
 *
 * There are two ways an organ can be linked to what goes wrong in it, and they are
 * not equally trustworthy, so they are returned separately and never merged.
 *
 * **Mapped** is the curated relationship: a clinical sign carries
 * `anatomical_structure_id`, so a disease reached through one is attached to that
 * organ because somebody said so. In the poultry data that is 2 of 151 diseases —
 * Coccidiosis and Enteritis, both through "Intestines". It is trustworthy and
 * almost empty.
 *
 * **Derived** is a name match. The finding vocabulary names the organ in plain
 * text — "Liver lesions", "Tracheal hemorrhage", "Cloacal prolapse" — and 195 of
 * those labels carry one of the 58 organ names. Matching on that name attaches
 * diseases to organs nobody has mapped: 21 poultry diseases reach "Liver", 13
 * reach "Trachea", 13 reach "Kidneys". It is an inference and it is wrong
 * sometimes, so it is labelled as one wherever it is rendered, kept out of the
 * database, and it doubles as the candidate list for the curation pass that would
 * promote any of it to mapped.
 *
 * Those two tiers are the honest answer to a phase planned on the belief that
 * `important_organs_affected` was populated for 152 diseases. It does not exist,
 * in a column or in `knowledge_payload`. Without this the choice is a chicken
 * diagram with nothing on it or a diagram that quietly passes a guess off as
 * curated data.
 *
 * Matching is on word boundaries rather than a bare `LIKE '%liver%'`, so "Crop"
 * does not swallow "Cropped beak", and a trailing-s singular is tried too so
 * "Lungs" still finds "Lung consolidation".
 *
 * @see AnatomicalStructure::scopeForPart()
 */
class OrganAttachmentService
{
    /**
     * Every structure in the part with what is attached to it, keyed by structure
     * id.
     *
     * Four queries for the whole page: the structures, the curated links, the
     * finding vocabulary, and the diseases with their classification. Matching
     * happens in PHP because it is 58 organs against a thousand labels, and asking
     * the database that 58 times to save a few milliseconds of string work is how a
     * page ends up at two hundred queries.
     *
     * `derivedReasons` is the audit trail for the guessed tier: it says which label
     * put each disease on this organ, which is the difference between "here are 38
     * diseases that mention the liver somewhere" and a list a veterinarian can
     * check. It is keyed by disease id and rides alongside `derived` rather than
     * inside it, so the tier itself stays a plain collection of diseases.
     *
     * @return Collection<int, array{
     *     structure: AnatomicalStructure,
     *     mapped: Collection<int, Disease>,
     *     derived: Collection<int, Disease>,
     *     derivedReasons: array<string, Collection<int, Finding>>,
     *     signs: Collection<int, Finding>,
     *     lesions: Collection<int, Finding>,
     * }>
     */
    public function attachments(string $domain): Collection
    {
        $structures = AnatomicalStructure::query()
            ->forPart($domain)
            ->with('bodySystem')
            ->orderBy('display_name')
            ->get();

        if ($structures->isEmpty()) {
            return collect();
        }

        $structureIds = $structures->pluck('id')->all();

        $curated = DB::table('clinical_signs as cs')
            ->join('disease_clinical_sign as dcs', 'dcs.clinical_sign_id', '=', 'cs.id')
            ->join('diseases as d', 'd.id', '=', 'dcs.disease_id')
            ->whereIn('cs.anatomical_structure_id', $structureIds)
            ->where('d.domain', $domain)
            ->where('d.is_active', true)
            ->select(['cs.anatomical_structure_id', 'd.id as disease_id'])
            ->get();

        $curatedByStructure = $curated
            ->groupBy('anatomical_structure_id')
            ->map(fn (Collection $rows): Collection => $rows->pluck('disease_id')->unique()->values());

        $matched = $this->matchByName($structures, $domain);

        $diseaseIds = $curated
            ->pluck('disease_id')
            ->merge(collect($matched)->flatMap(fn (array $rows): array => $rows['disease_ids']))
            ->unique()
            ->values();

        $diseases = Disease::query()
            ->whereIn('id', $diseaseIds)
            // The panel renders a zoonotic badge on every disease it lists. Loaded
            // here rather than lazily, because the alternative is one query per row
            // on an organ that reaches 38 of them.
            ->with('diseaseClassification')
            ->orderBy('name')
            ->get()
            ->keyBy(fn (Disease $disease): string => (string) $disease->getKey());

        $findingIds = collect($matched)
            ->flatMap(fn (array $rows): array => $rows['finding_ids'])
            ->unique()
            ->values();

        $findings = Finding::query()
            ->whereIn('id', $findingIds)
            ->orderBy('display_name')
            ->get()
            ->keyBy(fn (Finding $finding): string => (string) $finding->getKey());

        return $structures->mapWithKeys(function (AnatomicalStructure $structure) use ($curatedByStructure, $matched, $diseases, $findings): array {
            $structureId = (int) $structure->getKey();
            $mappedIds = $curatedByStructure->get($structureId, collect());
            $hits = $matched[$structureId] ?? ['disease_ids' => [], 'finding_ids' => [], 'reasons' => []];

            $derivedIds = collect($hits['disease_ids'])
                ->reject(fn (string $id): bool => $mappedIds->contains($id))
                ->unique()
                ->values();

            $matchedFindings = $findings
                ->filter(fn (Finding $finding): bool => in_array((int) $finding->getKey(), $hits['finding_ids'], true));

            $reasons = collect($hits['reasons'])->map(
                fn (array $matching): Collection => $findings->filter(
                    fn (Finding $finding): bool => in_array((int) $finding->getKey(), $matching, true),
                )->values(),
            );

            return [$structureId => [
                'structure' => $structure,
                'mapped' => $diseases->filter(fn (Disease $disease): bool => $mappedIds->contains((string) $disease->getKey()))->values(),
                'derived' => $diseases->filter(fn (Disease $disease): bool => $derivedIds->contains((string) $disease->getKey()))->values(),
                // Only the diseases the name match actually placed here. A curated
                // disease is dropped from `derivedIds` above, and its labels are
                // not this organ's evidence, so it gets no entry either.
                'derivedReasons' => $reasons
                    ->only($derivedIds->all())
                    ->all(),
                'signs' => $matchedFindings->where('ontology_type', 'sign')->values(),
                // Everything that is not a clinical sign is a postmortem finding.
                // `ontology_type` is null on 22 rows and those are lesions too —
                // Phase 7 learned that lesson matching `'sign'` exactly, which
                // silently dropped Pyrexia, Inappetence and Hemorrhagic diarrhea.
                'lesions' => $matchedFindings->where(fn (Finding $finding): bool => $finding->ontology_type !== 'sign')->values(),
            ]];
        });
    }

    /**
     * Which findings name which organ, and which diseases those findings belong
     * to.
     *
     * @param  Collection<int, AnatomicalStructure>  $structures
     * @return array<int, array{
     *     disease_ids: array<int, string>,
     *     finding_ids: array<int, int>,
     *     reasons: array<string, array<int, int>>,
     * }>
     */
    private function matchByName(Collection $structures, string $domain): array
    {
        $rows = DB::table('findings as f')
            ->join('clinical_signs as cs', 'cs.finding_id', '=', 'f.id')
            ->join('disease_clinical_sign as dcs', 'dcs.clinical_sign_id', '=', 'cs.id')
            ->join('diseases as d', 'd.id', '=', 'dcs.disease_id')
            ->where('d.domain', $domain)
            ->where('d.is_active', true)
            ->select([
                'f.id as finding_id',
                'f.canonical_name',
                'f.display_name',
                'd.id as disease_id',
            ])
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        // Each label is reduced to its words once. Both spellings are indexed
        // because the organ is underscored in one column and spaced in the other.
        $labels = $rows->map(fn ($row): array => [
            'finding_id' => (int) $row->finding_id,
            'disease_id' => (string) $row->disease_id,
            'forms' => array_values(array_unique(array_filter([
                $this->normalize((string) $row->display_name),
                $this->normalize((string) $row->canonical_name),
            ]))),
        ])->values();

        $matched = [];

        foreach ($structures as $structure) {
            $needles = $this->needles($structure);
            $stems = $this->stems($structure);

            if ($needles === []) {
                continue;
            }

            $hits = $labels->filter(function (array $label) use ($needles, $stems): bool {
                foreach ($label['forms'] as $form) {
                    if ($this->matches($needles, $stems, $form)) {
                        return true;
                    }
                }

                return false;
            });

            if ($hits->isEmpty()) {
                continue;
            }

            $matched[(int) $structure->getKey()] = [
                'disease_ids' => $hits->pluck('disease_id')->unique()->values()->all(),
                'finding_ids' => $hits->pluck('finding_id')->unique()->values()->all(),
                // Which label put which disease here. A name match with no stated
                // reason is indistinguishable from a curated link in the output, and
                // this is the part that lets the page say "Hepatic necrosis" beside
                // the disease it put on the liver.
                'reasons' => $hits
                    ->groupBy('disease_id')
                    ->map(fn (Collection $rows): array => $rows->pluck('finding_id')->unique()->values()->all())
                    ->all(),
            ];
        }

        return $matched;
    }

    /**
     * Words that describe an organ in this vocabulary when the organ's own name
     * is not the word anybody writes.
     *
     * Veterinary lesion labels reach for the clinical adjective — "hepatic
     * lesions", "renal droppings", "pulmonary consolidation" — and no amount of
     * stemming gets from "liver" to "hepatic". Each pair below is a veterinary
     * fact, not a guess, which is why they are written out rather than derived.
     * Add to this list rather than loosening the matcher; a wider matcher buys a
     * little recall with a great deal of noise.
     *
     * @var array<string, array<int, string>>
     */
    private const VOCABULARY = [
        'liver' => ['hepatic'],
        'kidneys' => ['renal', 'nephritic'],
        'lungs' => ['pulmonary'],
        'skin' => ['cutaneous', 'dermal', 'epidermal'],
        'spleen' => ['splenic'],
        'muscles' => ['muscular'],
        'joints' => ['articular'],
        'bones' => ['osseous'],
        'blood_vessels' => ['vascular'],
        'ovary' => ['ovarian'],
        'testes' => ['testicular'],
        'pancreas' => ['pancreatic'],
        'pharynx' => ['pharyngeal'],
        'thymus' => ['thymic'],
        'spinal_cord' => ['spinal'],
        'lymph_nodes' => ['lymphoid'],
        'syrinx' => ['syringeal'],
        'oral_cavity' => ['oral'],
        'nasal_cavity' => ['nasal'],
        'esophagus' => ['esophageal'],
        'ceca' => ['cecal'],
        'heart' => ['cardiac'],
        // The eye goes by two names in veterinary usage, and the structure's own
        // name is neither of them: the artwork says "Eye" and the labels say
        // "Ocular discharge" or "Ophthalmic lesions". Without both, the organ
        // reads as empty while its findings sit in the same table.
        'eye' => ['ocular', 'ophthalmic'],
    ];

    /**
     * Words that describe a shape rather than an organ, and are therefore never
     * used as stems.
     *
     * "Oral Cavity" otherwise claims "Obliterated Marrow Cavity" through the
     * word "cavity", and "Blood Vessels" claims every "bloody" label through
     * the word "blood" — a structure called a vessel is not the place a bird
     * has bloody droppings. Both organs still match on their own words and on
     * the vocabulary above; only the stem is given up.
     *
     * @var array<int, string>
     */
    private const NOT_A_STEM = [
        'blood',
        'canal',
        'cavity',
        'cord',
        'fluid',
        'gland',
        'membrane',
        'node',
        'nodes',
        'plexus',
        'sac',
        'space',
        'tract',
        'wall',
    ];

    /**
     * The words to look for, spaced and whole.
     *
     * @return array<int, string>
     */
    private function needles(AnatomicalStructure $structure): array
    {
        $needles = [
            trim($this->normalize($structure->display_name)),
            trim($this->normalize($structure->canonical_name)),
            ...self::VOCABULARY[$structure->canonical_name] ?? [],
        ];

        foreach ($needles as $needle) {
            // "Lungs" has to find "Lung consolidation". Trailing-s only, so
            // "Lymph nodes" is left alone.
            if (Str::endsWith($needle, 's') && ! Str::endsWith($needle, 'ss')) {
                $needles[] = Str::beforeLast($needle, 's');
            }
        }

        return array_values(array_unique(array_filter($needles)));
    }

    /**
     * Word beginnings to look for, which is how an adjective gets found.
     *
     * "Cloaca" has to reach "Cloacal prolapse" and "Duodenum" has to reach
     * "Duodenal necrosis", and both are the same word with a suffix the stem
     * does not have to know about. Five characters is the floor: at four,
     * "Peripheral Nerves" claims "peritoneum" and "Spinal Cord" claims
     * "cordlike", and at six "Trachea" stops reaching "tracheitis".
     *
     * @return array<int, string>
     */
    private function stems(AnatomicalStructure $structure): array
    {
        $stems = [];

        foreach ([$structure->display_name, $structure->canonical_name] as $source) {
            foreach (explode(' ', trim($this->normalize($source))) as $word) {
                if (strlen($word) >= 5 && ! in_array($word, self::NOT_A_STEM, true)) {
                    $stems[] = Str::substr($word, 0, 5);
                }
            }
        }

        return array_values(array_unique($stems));
    }

    /**
     * @param  array<int, string>  $needles
     * @param  array<int, string>  $stems
     */
    private function matches(array $needles, array $stems, string $haystack): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, ' '.$needle.' ')) {
                return true;
            }
        }

        if ($stems === []) {
            return false;
        }

        foreach (explode(' ', trim($haystack)) as $word) {
            foreach ($stems as $stem) {
                if (str_starts_with($word, $stem)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Lowercase with every separator turned into a space, so `air_sacs` and
     * "Air Sacs" reduce to the same words and a padded `str_contains` becomes a
     * word-boundary test.
     */
    private function normalize(string $value): string
    {
        return ' '.Str::of($value)
            ->lower()
            ->replaceMatches('/[^\p{L}\p{N}]+/u', ' ')
            ->squish()
            ->toString().' ';
    }
}
