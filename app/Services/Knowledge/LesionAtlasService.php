<?php

namespace App\Services\Knowledge;

use App\Enums\OntologyType;
use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\FindingImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The lesion atlas.
 *
 * Phase 9 was planned as "browse the merged lesion set, grouped by body system,
 * with images attached later". The first three of those hold. The fourth does
 * not exist yet — there is no `media_assets` table in this application at all,
 * only `product_images` — so the atlas ships as a text reference behind an
 * image-ready layout, and the empty box is drawn on purpose rather than left
 * broken.
 *
 * **Two things the plan assumed and the data does not have.**
 *
 * Lesion *descriptions* do not exist. `findings` carries `canonical_name`,
 * `display_name` and `category`, and there is no `gross_lesions` table to read
 * prose from, so search covers names only. The disease rows that carry a lesion
 * do have a `knowledge_payload`, but that describes the disease, not the lesion,
 * and matching a lesion name inside a disease description would attach text that
 * is about something else.
 *
 * No lesion carries an organ. Zero of the 640 have
 * `clinical_signs.anatomical_structure_id`, so "group by body system" cannot be
 * answered from the data. It is answered by `OrganAttachmentService`, which
 * reaches an organ when the lesion's *name* contains it — "Liver lesions",
 * "Thickened Air Sacs". That is the same inference the body map draws as its
 * amber tier, it carries the same caveat, and it is reused here rather than
 * reimplemented so the two pages cannot drift apart.
 *
 * The matcher places 412 of the 640 poultry lesions. The remaining 228 name no
 * organ at all — "Post-mortem: birds dull", "Necropsy: enlarged abdomen". They
 * are not a failure state to be tidied away; a vet looking at a bird needs to
 * reach them. They are listed in their own bucket, at the same level as the body
 * systems, rather than dropped.
 *
 * A lesion that names two organs is filed under the first structure in display
 * order. "Hepatic and renal lesions" is one row in `findings`, not two, and
 * duplicating it into both systems would inflate the counts the sections report.
 *
 * @see OrganAttachmentService
 */
class LesionAtlasService
{
    /**
     * The pseudo-section holding lesions that name no organ.
     *
     * A string rather than a `canonical_name` because it can never collide with
     * one, and the filter reads it straight off the URL.
     */
    /**
     * Lesions per page.
     *
     * Rows are one line each with an expandable panel, so this is roughly two
     * screens — enough that the pager is rarely needed, small enough that the
     * largest section on the live data (163 lesions) still paginates.
     */
    public const PER_PAGE = 48;

    public const UNCLASSIFIED_KEY = '__unclassified';

    /** A lesion whose name names an organ, filed under that organ's body system. */
    public const TIER_GROUPED = 'grouped';

    /** A lesion that names no organ. Still reachable, just not placed. */
    public const TIER_UNCLASSIFIED = 'unclassified';

    /**
     * The lesion → organ map, memoised per domain.
     *
     * `assignments()` is six queries on its own, and the page needs the map from
     * three callers: the paginator for this page's rows, `sectionCounts()` for the
     * headers, and `systemNames()` for the filter chips. Left unmemoised the page
     * walked the whole organ vocabulary three times over, which is how a nine-query
     * component reached twenty-seven.
     *
     * @var array<string, Collection<int, AnatomicalStructure>>
     */
    private array $assignmentCache = [];

    /**
     * Section counts, memoised per filter signature.
     *
     * Same reason as `$assignmentCache`: `systemNames()` asks for the same tally the
     * headers use, and counting ids is not free once the organ map is behind it.
     *
     * @var array<string, array<string, int>>
     */
    private array $sectionCountCache = [];

    public function __construct(private readonly OrganAttachmentService $organs) {}

    /**
     * One page of lesions, carrying their diseases and the organ each names.
     *
     * The paginator carries the disease lists for the rows on *this* page only.
     * That is the point of paginating: a section header needs a count, not a list
     * of every disease behind every lesion on every page. See `sectionCounts`.
     *
     * Every filter is applied here, in SQL, including the letter. The letter used
     * to be applied by the caller *after* pagination, which made page 2 mean "rows
     * 49 to 96 of the whole atlas, then keep the A's" — almost always nothing,
     * while the pager underneath still offered thirteen more pages of the unfiltered
     * set. A page has to be a page of what is being looked at.
     *
     * @return LengthAwarePaginator<int, array{
     *     id: int,
     *     name: string,
     *     letter: string,
     *     tier: string,
     *     structure: AnatomicalStructure|null,
     *     system: BodySystem|null,
     *     diseases: Collection<int, Disease>,
     *     photo: FindingImage|null,
     * }>
     */
    public function lesionsPaginator(string $domain, string $search = '', string $system = '', string $part = 'poultry', string $letter = ''): LengthAwarePaginator
    {
        // The pager path is pinned from the route, deliberately.
        //
        // Left alone the paginator reuses the current request's path, and inside a
        // Livewire render that arrives *without* its leading slash — so the shared
        // pagination view emitted `<a href="poultry/atlas?page=2">`. Under
        // `wire:navigate` the browser resolves that against `/poultry/atlas` and
        // asks for `/poultry/poultry/atlas?page=2`, which 404s on every page after
        // the first. `withPath()` makes the links absolute whatever the lifecycle.
        // Eager-loaded rather than read per tile: one extra query covers all 48
        // rows on the page, and the alternative is 48 of them. The relation is
        // `findings -> images`, and the atlas renders the first one, so the
        // `orderBy` on the relation is what decides which that is.
        $findings = $this->query($domain, $search, $system, $letter)
            ->with('images')
            ->paginate(self::PER_PAGE, ['*'], 'page')
            ->withPath(route($part.'.atlas'));

        // `withQueryString()` is the obvious call here and it is wrong on this page.
        //
        // It copies the *request's* query string onto the links, and a Livewire
        // update has no query string: the filters travel in the request body and
        // only reach the address bar through `#[Url]`. So the pager built
        // `?page=2` with no filters on it, and following it dropped the reader out
        // of the system they had selected while the section header above them still
        // announced the system they thought they were reading. The header and the
        // rows disagreed, which is the one thing this page cannot afford.
        //
        // The filters are named here instead, so a link carries what it needs
        // whether it was rendered by a full page load or by a Livewire update.
        $findings->appends(array_filter([
            'q' => $search,
            'system' => $system,
            'letter' => $letter,
        ], fn (string $value): bool => $value !== ''));

        if ($findings->isEmpty()) {
            return $findings;
        }

        $diseases = $this->diseasesFor($findings->getCollection(), $domain);
        $assignments = $this->assignments($domain);

        return $findings->setCollection(
            $findings->getCollection()
                ->map(function (Finding $finding) use ($diseases, $assignments): array {
                    /** @var AnatomicalStructure|null $structure */
                    $structure = $assignments->get((int) $finding->getKey());
                    $system = $structure?->bodySystem;

                    return [
                        'id' => (int) $finding->getKey(),
                        'name' => $finding->localized_display_name,
                        // From the canonical name, not the localised one, so it
                        // matches both the SQL filter and the rail. On the Arabic
                        // page the localised first character is never `[A-Z]`, which
                        // would make every letter filter there match nothing.
                        'letter' => $this->letterFor($finding->display_name),
                        'tier' => $system === null
                            ? self::TIER_UNCLASSIFIED
                            : self::TIER_GROUPED,
                        'structure' => $structure,
                        'system' => $system,
                        'diseases' => $diseases->get((int) $finding->getKey(), collect()),
                        // Null until a photograph has been uploaded. The tile draws
                        // a framed placeholder rather than a broken image, which is
                        // the honest state for 640 lesions that have never been
                        // photographed.
                        'photo' => $finding->images->first(),
                    ];
                })
                ->values(),
        );
    }

    /**
     * How many lesions sit under each body system, across every page.
     *
     * This is a separate call rather than something derived from the paginator
     * because the two answer different questions. A header reading "Digestive 48"
     * when 163 lesions are filed under Digestive contradicts the pager beneath it
     * and the filter chip that offers the system, and the reader has no way to
     * tell which number is the real one.
     *
     * It counts ids only. No diseases are loaded, so this costs the finding query
     * and the shared organ map — not the disease graph the page already walked.
     *
     * @return array<string, int>
     */
    public function sectionCounts(string $domain, string $search = '', string $system = '', string $letter = ''): array
    {
        $cacheKey = $domain.'|'.$search.'|'.$system.'|'.$letter;

        if (isset($this->sectionCountCache[$cacheKey])) {
            return $this->sectionCountCache[$cacheKey];
        }

        $ids = $this->query($domain, $search, $system, $letter)
            ->pluck('findings.id')
            ->map(fn (int|string $id): int => (int) $id);

        if ($ids->isEmpty()) {
            return $this->sectionCountCache[$cacheKey] = [];
        }

        $assignments = $this->assignments($domain);

        $counts = [self::UNCLASSIFIED_KEY => 0];

        foreach ($ids as $id) {
            $structure = $assignments->get($id);
            $key = $structure === null
                ? self::UNCLASSIFIED_KEY
                : (string) $structure->bodySystem->canonical_name;

            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        return $this->sectionCountCache[$cacheKey] = $counts;
    }

    /**
     * The letters that exist across every page, so the rail never offers a dead one.
     *
     * Read from the unpaginated id list for the same reason `sectionCounts` is: a
     * rail built from the current page would hide every letter whose lesions all
     * happen to fall on pages 2 and 3, which is the common case for a sparse
     * letter rather than an edge one.
     *
     * @return array<int, string>
     */
    public function letters(string $domain, string $search = '', string $system = ''): array
    {
        $names = $this->query($domain, $search, $system)
            ->pluck('findings.display_name');

        return $names
            ->map(fn (string $name): string => $this->letterFor($name))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * The headline numbers, which must not move when a filter is applied.
     *
     * Counted unfiltered on purpose: a hero that reads "12 lesions" while the
     * filter says "Liver" makes the filter look like it shrank the database.
     *
     * @return array{lesions: int, diseases: int}
     */
    public function totals(string $domain): array
    {
        return [
            'lesions' => $this->query($domain, '')->count(),
            'diseases' => Disease::query()
                ->where('domain', $domain)
                ->where('is_active', true)
                ->count(),
        ];
    }

    /**
     * Roll this page's lesions into body-system sections plus the unclassified bucket.
     *
     * Pure: it takes what `lesionsPaginator()` already loaded and arranges it, so
     * the page groups in PHP off one query set rather than re-querying per section.
     *
     * `$counts` is the whole-atlas tally from `sectionCounts()`. A section that
     * happens to have rows on this page reports that total, not the number of rows
     * beside it — otherwise "Digestive 48" sits above four pages of Digestive and
     * the filter chip offers the same system at a third number.
     *
     * A section absent from `$counts` is a section with no lesions at all rather
     * than a section whose lesions are all on another page, so nothing renders.
     * That is the difference between paginating and losing rows.
     *
     * @param  Collection<int, array<string, mixed>>  $lesions
     * @param  array<string, int>  $counts
     * @return Collection<string, array{
     *     key: string,
     *     system: BodySystem|null,
     *     tier: string,
     *     count: int,
     *     pageCount: int,
     *     lesions: Collection<int, array<string, mixed>>,
     * }>
     */
    public function sections(Collection $lesions, array $counts = []): Collection
    {
        $grouped = $lesions
            ->filter(fn (array $lesion): bool => $lesion['system'] !== null)
            ->groupBy(fn (array $lesion): string => (string) $lesion['system']->canonical_name)
            ->map(fn (Collection $rows, string $key): array => [
                'key' => $key,
                'system' => $rows->first()['system'],
                'tier' => $rows->first()['tier'],
                'count' => $counts[$key] ?? $rows->count(),
                'pageCount' => $rows->count(),
                'lesions' => $rows->values(),
            ])
            ->sortByDesc(fn (array $section): int => $section['count']);

        $unclassified = $lesions
            ->filter(fn (array $lesion): bool => $lesion['system'] === null)
            ->values();

        if ($unclassified->isNotEmpty()) {
            $grouped->put(self::UNCLASSIFIED_KEY, [
                'key' => self::UNCLASSIFIED_KEY,
                'system' => null,
                'tier' => self::TIER_UNCLASSIFIED,
                'count' => $counts[self::UNCLASSIFIED_KEY] ?? $unclassified->count(),
                'pageCount' => $unclassified->count(),
                'lesions' => $unclassified,
            ]);
        }

        return $grouped;
    }

    /**
     * The body systems that hold at least one lesion under the current filters.
     *
     * Built from the counts rather than from the sections on this page, because a
     * filter chip offering "Respiratory 0" when Respiratory has 54 lesions is a
     * chip that looks like it returns nothing.
     *
     * @return array<string, string>
     */
    public function systemNames(string $domain, string $search = '', string $system = '', string $letter = ''): array
    {
        $keys = array_keys($this->sectionCounts($domain, $search, $system, $letter));

        if ($keys === [] || $keys === [self::UNCLASSIFIED_KEY]) {
            return [];
        }

        return BodySystem::query()
            ->whereIn('canonical_name', $keys)
            ->get()
            ->mapWithKeys(fn (BodySystem $bodySystem): array => [
                (string) $bodySystem->canonical_name => (string) $bodySystem->localized_display_name,
            ])
            ->all();
    }

    /**
     * Lesions in the part, filtered by name.
     *
     * Domain comes through `whereExists` on the finding's clinical signs rather
     * than a join, because a lesion reaches several diseases and a join would
     * return it once per disease. `findings` has no `domain` column at all, so
     * this subquery is the only way to ask which part a lesion belongs to.
     *
     * @return Builder<Finding>
     */
    private function query(string $domain, string $search, string $system = '', string $letter = ''): Builder
    {
        return Finding::query()
            ->where('findings.ontology_type', OntologyType::Lesion->value)
            ->when($system !== '', fn (Builder $query): Builder => $query->whereIn(
                'findings.id',
                $this->findingsForSystem($domain, $system),
            ))
            // Matched on `display_name`, the same column `letters()` reads to build
            // the rail, so a letter on the rail and a letter in the query can never
            // mean two different things. Deliberately not the localised name: on the
            // Arabic page that yields an Arabic first character, which is never
            // `[A-Z]`, and every A-Z filter there would match nothing.
            //
            // `SUBSTR` rather than `LEFT` because the suite runs on SQLite in memory,
            // which has no `LEFT`.
            ->when($letter !== '', fn (Builder $query): Builder => $query->whereRaw(
                'UPPER(SUBSTR(TRIM(findings.display_name), 1, 1)) = ?',
                [$letter],
            ))
            ->whereExists(function ($query) use ($domain): void {
                $query->select(DB::raw(1))
                    ->from('clinical_signs')
                    ->join('disease_clinical_sign', 'disease_clinical_sign.clinical_sign_id', '=', 'clinical_signs.id')
                    ->join('diseases', 'diseases.id', '=', 'disease_clinical_sign.disease_id')
                    ->whereColumn('clinical_signs.finding_id', 'findings.id')
                    ->where('diseases.domain', $domain)
                    ->where('diseases.is_active', true);
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $term = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';

                $query->where(function (Builder $query) use ($term): void {
                    $query->where('findings.display_name', 'like', $term)
                        ->orWhere('findings.display_name_ar', 'like', $term);
                });
            })
            ->orderBy('findings.display_name');
    }

    /**
     * @param  Collection<int, Finding>  $findings
     * @return Collection<int, Collection<int, Disease>>
     */
    private function diseasesFor(Collection $findings, string $domain): Collection
    {
        $links = DB::table('clinical_signs as cs')
            ->join('disease_clinical_sign as dcs', 'dcs.clinical_sign_id', '=', 'cs.id')
            ->join('diseases as d', 'd.id', '=', 'dcs.disease_id')
            ->whereIn('cs.finding_id', $findings->modelKeys())
            ->where('d.domain', $domain)
            ->where('d.is_active', true)
            ->select(['cs.finding_id', 'd.id as disease_id'])
            ->get();

        $diseases = Disease::query()
            ->whereIn('id', $links->pluck('disease_id')->unique())
            ->orderBy('name')
            ->get()
            ->keyBy(fn (Disease $disease): string => (string) $disease->getKey());

        return $links
            ->groupBy('finding_id')
            ->map(fn (Collection $rows): Collection => $rows
                ->pluck('disease_id')
                ->unique()
                ->map(fn (int|string $id): ?Disease => $diseases->get((string) $id))
                ->filter()
                ->values());
    }

    /**
     * Which organ each lesion names, keyed by finding id.
     *
     * The first structure to claim a lesion keeps it. `attachments()` walks its
     * structures in display order, so "Hepatic and renal lesions" lands under
     * whichever sorts first rather than whichever the database happened to
     * return, and the choice is the same one every time.
     *
     * @return Collection<int, AnatomicalStructure>
     */
    private function assignments(string $domain): Collection
    {
        if (isset($this->assignmentCache[$domain])) {
            return $this->assignmentCache[$domain];
        }

        $map = collect();

        foreach ($this->organs->attachments($domain) as $attachment) {
            foreach ($attachment['lesions'] as $lesion) {
                $id = (int) $lesion->getKey();

                if (! $map->has($id)) {
                    $map->put($id, $attachment['structure']);
                }
            }
        }

        return $this->assignmentCache[$domain] = $map;
    }

    /**
     * The lesion ids a body-system filter selects.
     *
     * Read off `assignments()` — the same map `sectionCounts()` and `sections()`
     * group by — rather than off the structures table. The previous version asked
     * the structures table for the ids belonging to a system and put them in
     * `whereIn('findings.id', …)`, which compared a **finding** id against an
     * **anatomical_structure** id. The two are different id spaces that happen to
     * share a low range: digestive has 22 structures, ids 5–44, and those matched
     * 18 findings with no digestive organ among them. After the `whereExists` that
     * requires a live poultry disease, zero survived, so selecting any system
     * rendered an empty page while the chip above it advertised 163 lesions.
     *
     * Deriving the ids from the matcher fixes that by construction: the filter and
     * the counts now read from one source, so the number on the chip and the rows
     * underneath it cannot disagree. That agreement is the whole point of the
     * filter chip — a reader who picks "Digestive 163" is entitled to reach 163.
     *
     * It also costs nothing. `assignments()` is memoised per domain and has
     * already been built by `sectionCounts()` and `systemNames()` on the same
     * request, so this is a walk over an in-memory map rather than a query — the
     * filter adds no round trip, it only adds a wider `whereIn`.
     *
     * @return array<int, int>
     */
    private function findingsForSystem(string $domain, string $system): array
    {
        return $this->assignments($domain)
            ->filter(fn (AnatomicalStructure $structure): bool => (string) $structure->bodySystem->canonical_name === $system)
            ->keys()
            ->map(fn (int|string $id): int => (int) $id)
            ->all();
    }

    /**
     * The rail letter for a name, or an empty string when it has none.
     *
     * Lesion names start with capitals and digits ("3-Day Disease", "Coccidiosis"),
     * so this is a real question rather than a formality: a name opening on a
     * digit belongs under no letter and is only reachable from "All".
     */
    private function letterFor(string $name): string
    {
        $first = mb_substr(trim($name), 0, 1);

        return preg_match('/^[A-Z]$/i', $first) === 1 ? mb_strtoupper($first) : '';
    }
}
