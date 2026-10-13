<?php

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Side-by-side comparison of up to four diseases of one part.
 *
 * Shared by `/poultry/compare` and `/large-animals/compare` (§9.19). One
 * component rather than two because the comparison *is* the same operation on
 * both sides — only the candidate pool differs — and two copies of a 500-line
 * matrix builder is how they would drift apart.
 *
 * Three things here are deliberately not what they look like:
 *
 * 1. **Selection is slugs, not ids.** `?diseases[]=newcastle-disease` is a
 *    shareable link a vet can read out loud; four ULIDs is not.
 * 2. **Everything is scoped to the part, including the picker.** The previous
 *    version queried `Disease::where('name','like',…)` with no domain filter,
 *    so `/large-animals/compare` offered 151 poultry diseases (§ same defect
 *    class as the unscoped search page).
 * 3. **Signs and lesions come from the relational ontology first.** Only 2 of
 *    151 poultry diseases carry a `knowledge_payload`, so a payload-only
 *    matrix renders empty for almost the whole section. The pivot tables are
 *    populated — signs reach 159 diseases and lesions 148. The payload is
 *    merged on top for the fields that have no table at all.
 */
new class extends Component
{
    private const MAX_COLUMNS = 4;

    private const PER_COLUMN = 10;

    /**
     * Susceptibility is free text on the pivot. Ranked rather than displayed
     * raw so "one host is highly susceptible" wins over "a dozen are moderate".
     */
    private const SUSCEPTIBILITY_RANK = [
        'low' => 1,
        'moderate' => 2,
        'high' => 3,
        'susceptible' => 3,
    ];

    public string $part = 'large-animals';

    /** @var array<int, string> Disease slugs, in the order they were picked. */
    #[Url(as: 'diseases', history: false)]
    public array $selected = [];

    /** @var array<int, string> Per-slot picker text. Not part of the URL. */
    public array $searches = [];

    public function mount(?string $part = null): void
    {
        $this->part = $this->resolvePart($part);
        $this->searches = array_fill(0, self::MAX_COLUMNS, '');

        // Strips anything that is not a disease of this part, so a link carrying
        // a slug from the other half of the site degrades to a smaller
        // comparison rather than rendering a disease from the wrong section.
        $this->selected = $this->onlyThisPart($this->selected);
    }

    /**
     * Scoped on the way in, not only on the way out.
     *
     * `selectedDiseases()` drops anything outside the part, so a foreign slug
     * cannot render — but if it were filtered only there it would still occupy
     * one of the four slots. Four crafted calls would fill the comparison with
     * nothing at all, so the check belongs at the setter too.
     *
     * **The column is the picker's, not the end of the list.** This used to
     * append, which meant a choice made in the third picker landed in the first
     * card whenever the earlier ones were empty — the picker you typed into had
     * no bearing on where the disease appeared, which reads as the page ignoring
     * you. The slot index is carried through instead, so the card you searched
     * from is the card that fills.
     *
     * @see selectedDiseases()
     */
    public function addDisease(int $column, string $slug): void
    {
        if ($slug === '' || in_array($slug, $this->selected, true)) {
            return;
        }

        if ($column < 0 || $column >= self::MAX_COLUMNS) {
            return;
        }

        if (count($this->selected) >= self::MAX_COLUMNS) {
            return;
        }

        if (! $this->belongsToPart($slug)) {
            return;
        }

        // Placing at $column can leave a hole to its left — a user who fills the
        // third card first gets an empty first and second. That is the honest
        // result of the slot they chose, and the picker cards read correctly;
        // the table skips the holes, since a column for a disease nobody picked
        // would be wider than the disease it holds.
        $this->selected[$column] = $slug;
        $this->searches[$column] = '';
    }

    private function belongsToPart(string $slug): bool
    {
        return Disease::query()
            ->forDomain($this->domain())
            ->where('slug', $slug)
            ->exists();
    }

    /**
     * Removal closes the gap — the remaining diseases slide left.
     *
     * The table's columns come from the selection in order, so a hole left in
     * the middle would render an empty column between two diseases, which reads
     * as "these two share nothing" rather than as "a card was removed".
     *
     * Both arrays are rebuilt from the non-null slugs rather than shifted with
     * `unset` plus `array_values`, because `addDisease()` writes at a slot index
     * and can leave holes to the left of what it filled. Reindexing both from
     * the same source is the only thing guaranteed to keep a card's search box
     * attached to its disease — shifting them independently is how a half-typed
     * search ends up filtering the wrong column.
     *
     * Picker text is discarded rather than moved. It was cleared on every
     * successful pick anyway, so anything left is a search the user abandoned.
     */
    public function removeDisease(int $index): void
    {
        unset($this->selected[$index]);

        $this->selected = array_values(array_filter($this->selected, fn (mixed $slug): bool => $slug !== null && $slug !== ''));
        $this->searches = array_fill(0, self::MAX_COLUMNS, '');
    }

    public function clearAll(): void
    {
        $this->selected = [];
        $this->searches = array_fill(0, self::MAX_COLUMNS, '');
    }

    /**
     * Only the part's own diseases. A disease deactivated since a link was
     * shared is still hidden from the picker, but a comparison already holding it
     * keeps rendering rather than silently shrinking.
     */
    public function searchResults(int $column): Collection
    {
        $search = trim((string) ($this->searches[$column] ?? ''));

        if ($search === '') {
            return collect();
        }

        return Disease::query()
            ->forDomain($this->domain())
            ->where('is_active', true)
            ->where(fn (Builder $query): Builder => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%"))
            ->whereNotIn('slug', $this->selected)
            ->orderBy('name')
            ->take(self::PER_COLUMN)
            ->get(['id', 'slug', 'name', 'name_ar']);
    }

    /**
     * @return Collection<int, Disease>
     */
    #[Computed]
    public function selectedDiseases(): Collection
    {
        if ($this->selected === []) {
            return collect();
        }

        $found = Disease::query()
            ->forDomain($this->domain())
            ->whereIn('slug', $this->selected)
            ->with([
                'diseaseClassification',
                'hostSpecies',
                'microorganisms',
                'products',
                'clinicalSigns.finding',
            ])
            ->get()
            ->keyBy('slug');

        // Reordered to the picked order rather than the order the database
        // returned, because the comparison reads left to right as a ranking of
        // "how worried am I about each of these".
        //
        // Holes are dropped rather than preserved: the table renders one column
        // per disease, and a column with nothing in it is wider than the disease
        // it holds. The *picker* cards keep their slots (the view indexes into
        // `$this->selected` directly for those), so filling the third card first
        // still shows the disease in the third card.
        // Sorted by slot, not iterated in insertion order. Filling the third
        // card first and the first card second leaves the array keyed
        // [2 => ..., 0 => ..., 1 => ...], and iterating that would put the
        // disease picked into card three in the table's first column, which is
        // the picker bug reappearing one layer down.
        return collect($this->selected)
            ->filter(fn (mixed $slug): bool => filled($slug))
            ->sortKeys()
            ->map(fn (string $slug): ?Disease => $found->get($slug))
            ->filter()
            ->values();
    }

    #[Computed]
    public function totalCount(): int
    {
        return $this->selectedDiseases->count();
    }

    #[Computed]
    public function isFull(): bool
    {
        return count($this->selected) >= self::MAX_COLUMNS;
    }

    // ---------------------------------------------------------------- matrices

    #[Computed]
    public function overviewMatrix(): array
    {
        return [
            $this->valueRow('etiology', fn (Disease $disease): mixed => $disease->diseaseClassification?->etiology_type),
            $this->valueRow('host_species', fn (Disease $disease): string => $disease->hostSpecies
                ->pluck('display_name')->filter()->unique()->sort()->implode(', ')),
            $this->valueRow('susceptibility', fn (Disease $disease): string => $this->susceptibilityOf($disease)),
            $this->valueRow('organisms', fn (Disease $disease): string => $disease->microorganisms
                ->map(fn ($microorganism): ?string => $microorganism->display_name ?? $microorganism->name)
                ->filter()->unique()->sort()->implode(', ')),
            $this->valueRow('products', fn (Disease $disease): string => $disease->products
                ->map(fn (Product $product): string => app()->getLocale() === 'ar' && $product->trade_name_ar
                    ? $product->trade_name_ar
                    : (string) $product->trade_name)
                ->filter()->unique()->sort()->implode(', ')),
            $this->booleanRow('zoonotic', fn (Disease $disease): bool => (bool) $disease->diseaseClassification?->zoonotic),
            $this->booleanRow('notifiable', fn (Disease $disease): bool => (bool) $disease->diseaseClassification?->notifiable),
            $this->valueRow('oie_category', fn (Disease $disease): mixed => $disease->diseaseClassification?->oie_category),
        ];
    }

    #[Computed]
    public function signMatrix(): array
    {
        return $this->presenceMatrix(fn (Disease $disease): array => $this->signItems($disease));
    }

    /**
     * Organs, from the knowledge payload.
     *
     * The obvious alternative — deriving them from `clinicalSigns.anatomical_
     * structure` — was built and then removed: only **27 of 1,109** sign rows
     * carry an anatomy and only **2 of the 151** poultry diseases have any, so
     * the tab was empty for everything that matters.
     *
     * `important_organs_affected` is populated in **152 of the 154** source
     * documents. It is simply not imported yet (Phase 1 §1.6 — 2 of 151
     * diseases carry a payload), so this tab is empty today and needs no code
     * change when the bulk import runs. An empty tab that fills itself is
     * honest; a derived one built on 2 of 151 records is not.
     */
    #[Computed]
    public function organMatrix(): array
    {
        return $this->presenceMatrix(fn (Disease $disease): array => $this->payloadList($disease, 'important_organs_affected'));
    }

    #[Computed]
    public function lesionMatrix(): array
    {
        return $this->presenceMatrix(fn (Disease $disease): array => $disease->lesionItems());
    }

    // Payload-only. These render as no tab at all until the JSON import lands,
    // which is honest: an empty tab looks like the page is broken.
    #[Computed]
    public function diagnosisMatrix(): array
    {
        return $this->payloadMatrix('diagnosis', 'method');
    }

    #[Computed]
    public function treatmentMatrix(): array
    {
        return $this->payloadMatrix('treatment', 'intervention');
    }

    #[Computed]
    public function preventionMatrix(): array
    {
        return $this->payloadMatrix('prevention_control', 'measure');
    }

    #[Computed]
    public function referencesMatrix(): array
    {
        return $this->payloadMatrix('references', 'title');
    }

    /**
     * @param  Closure(Disease): array<string, string>  $items
     * @return array<int, array<string, mixed>>
     */
    private function presenceMatrix(Closure $items): array
    {
        $diseases = $this->selectedDiseases;

        if ($diseases->count() < 2) {
            return [];
        }

        $labels = [];

        foreach ($diseases as $disease) {
            foreach ($items($disease) as $key => $label) {
                $labels[$key] = $label;
            }
        }

        ksort($labels);

        $rows = collect($labels)->map(function (string $label, string $key) use ($diseases, $items): array {
            $presence = [];
            $presentCount = 0;

            foreach ($diseases as $disease) {
                $present = array_key_exists($key, $items($disease));
                $presence[$disease->slug] = $present;
                $presentCount += $present ? 1 : 0;
            }

            $total = $diseases->count();

            return [
                'is_header' => false,
                'name' => $label,
                'presence' => $presence,
                'present_count' => $presentCount,
                'total' => $total,
                // Every row in the union is present somewhere, so "differs" is
                // simply "not present everywhere".
                'differs' => $presentCount !== $total,
                'group' => match (true) {
                    $presentCount === $total => 'shared',
                    $presentCount === 1 => 'unique',
                    default => 'partial',
                },
            ];
        })->values();

        return $rows->isEmpty() ? [] : $this->groupRows($rows);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function payloadMatrix(string $path, string $field): array
    {
        return $this->presenceMatrix(
            fn (Disease $disease): array => $this->payloadItems($disease, $path, $field),
        );
    }

    /**
     * Splice group headers into the row list.
     *
     * `unique` first, because a finding only one of the diseases has is the
     * entire reason to open this page. `shared` last, because a finding every
     * one of them has cannot tell them apart at all — which is worth saying, but
     * it is the least informative thing on the page.
     *
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function groupRows(Collection $rows): array
    {
        $output = [];

        foreach (['unique', 'partial', 'shared'] as $group) {
            $subset = $rows->where('group', $group)->sortBy('name')->values();

            if ($subset->isEmpty()) {
                continue;
            }

            $output[] = [
                'is_header' => true,
                'group' => $group,
                'name' => __($this->part.'.comparison.groups.'.$group),
                'count' => $subset->count(),
                // A whole group can be identical across every column, in which
                // case the "differences only" toggle should hide the header too
                // rather than leave a label over nothing.
                'has_diff' => $subset->contains('differs', true),
            ];

            foreach ($subset as $row) {
                $output[] = $row;
            }
        }

        return $output;
    }

    /**
     * @return array<string, mixed>
     */
    private function valueRow(string $label, Closure $value): array
    {
        $values = [];

        foreach ($this->selectedDiseases as $disease) {
            $values[$disease->slug] = (string) $value($disease);
        }

        return [
            'name' => $label,
            'type' => 'value',
            'values' => $values,
            'differs' => count(array_unique($values)) > 1,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function booleanRow(string $label, Closure $value): array
    {
        $values = [];

        foreach ($this->selectedDiseases as $disease) {
            $values[$disease->slug] = (bool) $value($disease);
        }

        return [
            'name' => $label,
            'type' => 'boolean',
            'values' => $values,
            'differs' => count(array_unique($values)) > 1,
        ];
    }

    // ------------------------------------------------------------------ sources

    /**
     * @return array<string, string>
     */
    private function signItems(Disease $disease): array
    {
        return array_merge(
            $this->payloadItems($disease, 'clinical_signs', 'canonical_name'),
            $disease->signItems(),
        );
    }

    /**
     * @return array<string, string>
     */
    private function payloadItems(Disease $disease, string $path, string $field): array
    {
        $payload = is_array($disease->knowledge_payload) ? $disease->knowledge_payload : [];

        return collect(data_get($payload, $path, []))
            ->filter(fn (mixed $row): bool => is_array($row))
            ->mapWithKeys(fn (array $row): array => [
                $this->normalise((string) data_get($row, $field)) => (string) (data_get($row, 'display_name') ?: data_get($row, $field)),
            ])
            ->filter(fn (string $label, string $key): bool => $key !== '')
            ->all();
    }

    /**
     * A payload field that is a flat list of strings rather than a list of
     * objects — `important_organs_affected` is `["liver", "spleen"]`, not
     * `[{"name": "liver"}]`. `mapScalarList` in the transformer preserves that
     * shape, so it needs a different reader than `payloadItems()`.
     *
     * @return array<string, string>
     */
    private function payloadList(Disease $disease, string $path): array
    {
        $payload = is_array($disease->knowledge_payload) ? $disease->knowledge_payload : [];

        return collect(data_get($payload, $path, []))
            ->filter(fn (mixed $value): bool => is_scalar($value) && trim((string) $value) !== '')
            ->mapWithKeys(fn (mixed $value): array => [
                $this->normalise((string) $value) => trim((string) $value),
            ])
            ->all();
    }

    /**
     * The highest susceptibility recorded against any of the disease's hosts.
     *
     * Returned as a headline-cased vocabulary value rather than a translated
     * one, matching how `etiology_type` is already displayed raw in the column
     * card. The three values in use are moderate / high / susceptible.
     */
    private function susceptibilityOf(Disease $disease): string
    {
        $top = $disease->hostSpecies
            ->pluck('pivot.susceptibility')
            ->filter()
            ->map(fn (?string $value): int => self::SUSCEPTIBILITY_RANK[mb_strtolower(trim($value))] ?? 0)
            ->max();

        if (! $top) {
            return '';
        }

        return Str::headline((string) array_search($top, self::SUSCEPTIBILITY_RANK, true));
    }

    private function domain(): string
    {
        return (string) config("vetpedia.parts.{$this->part}.domain");
    }

    /**
     * @return string
     */
    private function resolvePart(?string $part): string
    {
        $parts = array_keys((array) config('vetpedia.parts', []));

        return in_array($part, $parts, true) ? $part : (string) array_key_first($parts);
    }

    /**
     * @param  array<int, mixed>  $slugs
     * @return array<int, string>
     */
    private function onlyThisPart(array $slugs): array
    {
        $slugs = array_slice(
            array_values(array_unique(array_filter(
                array_map(fn (mixed $slug): string => (string) $slug, $slugs),
                fn (string $slug): bool => $slug !== '',
            ))),
            0,
            self::MAX_COLUMNS,
        );

        if ($slugs === []) {
            return [];
        }

        $known = Disease::query()
            ->forDomain($this->domain())
            ->whereIn('slug', $slugs)
            ->pluck('slug')
            ->all();

        return array_values(array_filter(
            $slugs,
            fn (string $slug): bool => in_array($slug, $known, true),
        ));
    }

    private function normalise(?string $value): string
    {
        return mb_strtolower(trim((string) $value));
    }
};
?>

@php
    // Shared by both parts (§12.1): $part is the route-name prefix and the
    // translation namespace at once, so one typo moves a URL and its copy
    // together. Translating through a closure keeps every key below from
    // repeating the prefix.
    $t = fn (string $key, array $replace = []): string => __($part . '.' . $key, $replace);

    // The four picker cards are addressed by **slot**, not by position in the
    // compacted selection: a disease picked from the third search box has to
    // appear in the third card, so the card looks itself up in `$selected` at
    // its own index rather than in the compacted list.
    //
    // The comparison table uses $columns instead, one column per disease, in
    // selection order, with no holes.
    $columns = $this->selectedDiseases;
    $selected = $columns->count();

    $cardFor = function (int $slot) use ($columns): ?Disease {
        $slug = $this->selected[$slot] ?? null;

        return filled($slug) ? $columns->firstWhere('slug', $slug) : null;
    };
@endphp

<div class="space-y-6">
    <x-large-animals.page-hero
        :heading="$t('comparison.heading')"
        :subtitle="$t('comparison.subtitle')"
        :badge="$t('hero.badge.comparison')"
        badgeIcon="columns-3"
        :stats="[
            ['count' => $selected, 'label' => $t('comparison.selected'), 'icon' => 'columns-3'],
        ]"
    />

    {{-- Pickers. Four fixed slots rather than a growing list, so the comparison
         table always has somewhere to put the next column and the layout does
         not jump when one is added. --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6">
        @foreach (range(0, 3) as $column)
            @php
                // Resolved through the slot, so picking from this search box
                // fills this card.
                $disease = $cardFor($column);
                $results = $disease ? collect() : $this->searchResults($column);
                $term = (string) ($this->searches[$column] ?? '');
            @endphp

            <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs flex flex-col">
                <div class="p-4 border-b border-default-medium dark:border-slate-700">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                        </div>

                        <input type="text"
                            wire:model.live.debounce.300ms="searches.{{ $column }}"
                            placeholder="{{ $t('comparison.search_placeholder') }}"
                            @disabled($this->isFull && ! $disease)
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-9 px-3 py-2 shadow-xs placeholder:text-body disabled:opacity-50 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    </div>

                    <div wire:key="compare-results-{{ $column }}" class="relative mt-1">
                        @if ($term !== '' && $results->isNotEmpty())
                            <div class="absolute z-10 w-full bg-white dark:bg-slate-700 border border-default-medium dark:border-slate-600 rounded-base shadow-lg max-h-60 overflow-y-auto">
                                @foreach ($results as $result)
                                    <button type="button"
                                        wire:click="addDisease({{ $column }}, @js($result->slug))"
                                        class="w-full text-start px-3 py-2.5 hover:bg-neutral-secondary-soft dark:hover:bg-slate-600 border-b border-default-medium dark:border-slate-600 last:border-b-0 transition-colors">
                                        <span class="block text-sm font-medium text-heading dark:text-white">{{ $result->name }}</span>
                                        @if ($result->name_ar)
                                            <span class="text-xs text-body dark:text-slate-400" dir="rtl">{{ $result->name_ar }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    @if ($disease)
                        <div class="relative flex gap-3">
                            <button type="button"
                                wire:click="removeDisease({{ $column }})"
                                title="{{ $t('comparison.remove') }}"
                                aria-label="{{ $t('comparison.remove') }}"
                                class="absolute -top-1.5 -end-1.5 z-10 w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/50 text-red-500 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800 flex items-center justify-center transition-colors">
                                <x-lucide-x class="w-4 h-4" />
                            </button>

                            <div class="w-12 h-12 shrink-0 rounded-base bg-brand/10 dark:bg-brand/20 border border-brand/20 dark:border-brand/30 flex items-center justify-center">
                                <x-lucide-activity class="w-6 h-6 text-brand" />
                            </div>

                            <div class="flex-1 min-w-0 pe-6">
                                <a href="{{ route($part . '.diseases.show', $disease->slug) }}" wire:navigate
                                    class="block text-sm font-semibold text-heading dark:text-white hover:text-brand dark:hover:text-sky-400 transition-colors leading-tight line-clamp-2">
                                    {{ $disease->name }}
                                </a>

                                @if ($disease->name_ar)
                                    <p class="text-xs text-body dark:text-slate-400 mt-0.5 truncate" dir="rtl">{{ $disease->name_ar }}</p>
                                @endif

                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @if ($disease->diseaseClassification?->etiology_type)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand/15 text-brand-strong dark:bg-brand/25 dark:text-brand">
                                            {{ $disease->diseaseClassification->etiology_type }}
                                        </span>
                                    @endif
                                    @if ($disease->diseaseClassification?->zoonotic)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-danger-soft text-fg-danger-strong dark:bg-red-950 dark:text-red-300">
                                            {{ $t('disease.zoonotic') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs text-body dark:text-slate-400 mt-1 space-y-0.5">
                                    <p>{{ $disease->hostSpecies->count() }} {{ $t('hero.stats.species') }}</p>
                                    <p>{{ $disease->clinicalSigns->count() }} {{ $t('hero.stats.clinical_signs') }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <x-lucide-search class="w-10 h-10 text-body/40 dark:text-slate-500 mx-auto mb-2" />
                                <p class="text-sm text-body/60 dark:text-slate-500">
                                    {{ $this->isFull ? $t('comparison.max_reached') : $t('comparison.search_to_add') }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($selected > 0)
        <div class="flex justify-end">
            <button type="button" wire:click="clearAll" wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 text-sm text-body hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 underline transition-colors disabled:opacity-50 disabled:cursor-wait">
                <span wire:loading.remove wire:target="clearAll">
                    <x-lucide-trash-2 class="w-4 h-4" />
                </span>
                <span wire:loading wire:target="clearAll">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="clearAll">{{ $t('comparison.clear_all') }}</span>
                <span wire:loading wire:target="clearAll">{{ $t('comparison.clearing') }}</span>
            </button>
        </div>
    @endif

    @php
        // A tab with no rows is not rendered at all. Better than an empty panel:
        // four of these read the knowledge payload, which 149 of the 151 poultry
        // diseases do not have yet.
        $comparisonTabs = collect([
            ['id' => 'overview', 'label' => $t('comparison.overview'), 'icon' => 'clipboard-list', 'type' => 'overview', 'rows' => $this->overviewMatrix],
            ['id' => 'clinical_signs', 'label' => $t('comparison.clinical_signs'), 'icon' => 'stethoscope', 'type' => 'presence', 'rows' => $this->signMatrix],
            ['id' => 'organs', 'label' => $t('comparison.organs'), 'icon' => 'bone', 'type' => 'presence', 'rows' => $this->organMatrix],
            ['id' => 'postmortem_findings', 'label' => $t('comparison.postmortem_findings'), 'icon' => 'microscope', 'type' => 'presence', 'rows' => $this->lesionMatrix],
            ['id' => 'diagnosis', 'label' => $t('comparison.diagnosis'), 'icon' => 'test-tube-2', 'type' => 'presence', 'rows' => $this->diagnosisMatrix],
            ['id' => 'treatment', 'label' => $t('comparison.treatment'), 'icon' => 'syringe', 'type' => 'presence', 'rows' => $this->treatmentMatrix],
            ['id' => 'prevention', 'label' => $t('comparison.prevention'), 'icon' => 'shield-check', 'type' => 'presence', 'rows' => $this->preventionMatrix],
            ['id' => 'references', 'label' => $t('comparison.references'), 'icon' => 'book-marked', 'type' => 'presence', 'rows' => $this->referencesMatrix],
        ])
            ->filter(fn (array $tab): bool => count($tab['rows']) > 0)
            ->map(fn (array $tab): array => [
                ...$tab,
                'rowCount' => count($tab['rows']),
                'diffCount' => collect($tab['rows'])->where('differs', true)->count(),
            ])
            ->values()
            ->all();

        $defaultTab = $comparisonTabs[0]['id'] ?? 'overview';
    @endphp

    @if ($selected >= 2 && $comparisonTabs !== [])
        <div x-data="{ active: '{{ $defaultTab }}', diffOnly: false }" class="space-y-4">

            {{-- What the grouping means, stated once. Reading it off three
                 differently-coloured headers in a dense table is guesswork. --}}
            <p class="text-sm text-body dark:text-slate-400">
                <x-lucide-info class="w-4 h-4 inline-block align-text-bottom" />
                {{ $t('comparison.legend') }}
            </p>

            <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs p-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-1" role="tablist">
                        @foreach ($comparisonTabs as $tab)
                            <button type="button" role="tab"
                                x-on:click="active = '{{ $tab['id'] }}'"
                                x-bind:class="active === '{{ $tab['id'] }}'
                                    ? 'bg-brand text-white shadow-xs'
                                    : 'text-body hover:bg-neutral-secondary-soft dark:hover:bg-slate-700 dark:text-slate-300'"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-base text-sm font-medium transition-colors">
                                <x-dynamic-component :component="'lucide-' . $tab['icon']" class="w-4 h-4" />
                                <span>{{ $tab['label'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" x-model="diffOnly"
                            class="w-4 h-4 rounded-xs text-brand focus:ring-2 focus:ring-brand-soft dark:bg-slate-600 dark:border-slate-500">
                        <span class="text-sm font-medium text-heading dark:text-white">{{ $t('comparison.show_differences_only') }}</span>
                    </label>
                </div>
            </div>

            @foreach ($comparisonTabs as $tab)
                <div x-show="active === '{{ $tab['id'] }}'" x-cloak class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <x-dynamic-component :component="'lucide-' . $tab['icon']" class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $tab['label'] }}</h2>
                        </div>
                        <span class="text-xs text-body dark:text-slate-400">
                            <span x-show="!diffOnly">{{ $tab['rowCount'] }} {{ $t('comparison.items') }}</span>
                            <span x-show="diffOnly" x-cloak>{{ $tab['diffCount'] }} {{ $t('comparison.different_items') }}</span>
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right [&_th:not(:first-child)]:border-s [&_th:not(:first-child)]:border-default-medium dark:[&_th:not(:first-child)]:border-slate-700 [&_td:not(:first-child)]:border-s [&_td:not(:first-child)]:border-default-medium dark:[&_td:not(:first-child)]:border-slate-700">
                            <thead class="bg-neutral-secondary-soft dark:bg-slate-700">
                                <tr>
                                    <th class="px-5 py-4 text-xs uppercase tracking-wider text-body dark:text-slate-400 font-semibold w-48">
                                        {{ $t('comparison.attribute') }}
                                    </th>
                                    @foreach ($columns as $disease)
                                        <th class="px-5 py-4 min-w-[200px] text-sm font-bold text-heading dark:text-white">
                                            <a href="{{ route($part . '.diseases.show', $disease->slug) }}" wire:navigate class="hover:text-brand dark:hover:text-sky-400 transition-colors">{{ $disease->name }}</a>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-default-medium dark:divide-slate-700">
                                @foreach ($tab['rows'] as $item)
                                    @if ($tab['type'] === 'overview')
                                        <tr x-show="!diffOnly || @js($item['differs'])">
                                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                                {{ $t('comparison.' . $item['name']) }}
                                            </td>
                                            @foreach ($columns as $disease)
                                                <td class="px-5 py-4">
                                                    @if ($item['type'] === 'boolean')
                                                        <span class="text-sm font-medium text-heading dark:text-white">
                                                            {{ ($item['values'][$disease->slug] ?? false) ? $t('disease.yes') : $t('disease.no') }}
                                                        </span>
                                                    @else
                                                        <span class="text-sm text-heading dark:text-white">{{ ($item['values'][$disease->slug] ?? '') ?: '—' }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>

                                    @elseif ($item['is_header'])
                                        {{-- Group label. Hidden with its rows when the
                                             "differences only" toggle leaves the
                                             whole group identical. --}}
                                        <tr x-show="!diffOnly || @js($item['has_diff'])">
                                            <td colspan="{{ $selected + 1 }}"
                                                @class([
                                                    'px-5 py-2.5 text-xs font-semibold uppercase tracking-wider',
                                                    'bg-brand/10 text-brand-strong dark:bg-brand/15 dark:text-brand' => $item['group'] === 'unique',
                                                    'bg-neutral-secondary-soft text-body dark:bg-slate-700 dark:text-slate-300' => $item['group'] === 'partial',
                                                    'bg-danger-soft/40 text-fg-danger-strong dark:bg-red-950/40 dark:text-red-300' => $item['group'] === 'shared',
                                                ])>
                                                {{ $item['name'] }} · {{ $item['count'] }}
                                            </td>
                                        </tr>

                                    @else
                                        <tr x-show="!diffOnly || @js($item['differs'])">
                                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                                {{ $item['name'] }}
                                            </td>
                                            @foreach ($columns as $disease)
                                                <td class="px-5 py-4">
                                                    @if ($item['presence'][$disease->slug] ?? false)
                                                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-success-strong dark:text-emerald-400" title="{{ $t('comparison.present') }}">
                                                            <x-lucide-check-circle class="w-4 h-4" />
                                                            {{ $t('comparison.present') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 text-sm text-body/60 dark:text-slate-500" title="{{ $t('comparison.absent') }}">
                                                            <x-lucide-minus-circle class="w-4 h-4" />
                                                            {{ $t('comparison.absent') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>

                        <p x-show="diffOnly && @js($tab['diffCount'] === 0)" x-cloak class="text-sm text-body dark:text-slate-400 py-8 text-center">
                            {{ $t('comparison.no_differences') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    @elseif ($selected === 1)
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs px-6 py-10 text-center">
            <x-lucide-git-compare class="w-10 h-10 text-body dark:text-slate-400 mx-auto mb-3" />
            <p class="text-sm text-body dark:text-slate-400">{{ $t('comparison.select_more') }}</p>
        </div>

    @else
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs py-16 text-center">
            <x-lucide-columns-3 class="w-16 h-16 text-body dark:text-slate-400 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ $t('comparison.empty_title') }}</h3>
            <p class="text-sm text-body dark:text-slate-400 mb-6">{{ $t('comparison.empty_desc') }}</p>
        </div>
    @endif
</div>
