<?php

use App\Services\Knowledge\LesionAtlasService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The poultry lesion atlas.
 *
 * The page answers one question: you have opened a bird, you can see something,
 * what is it. So it is a reference and not a tool — filters narrow a list rather
 * than narrowing a result set, and nothing here is scored or ranked.
 *
 * Two things about it are inferences and are labelled as such wherever they
 * render. Which body system a lesion sits under comes from the lesion's *name*
 * naming an organ, not from a link anybody curated — zero of the 640 poultry
 * lesions carry an `anatomical_structure_id`. And a third of them name no organ
 * at all, so they appear in an `Unclassified` bucket at the same level as the
 * systems rather than being quietly dropped, which would hide a third of the
 * atlas from anyone who filters to a body system.
 *
 * The images are not here. Phase 9 planned photographs behind a media table that
 * this application does not have, so every lesion carries a placeholder box at
 * the aspect ratio the photograph will use. It is drawn on purpose: a broken
 * image icon reads as a bug, an empty framed box reads as "not yet".
 *
 * @see \App\Services\Knowledge\LesionAtlasService
 */
new class extends Component
{
    // `resetPage()` and the `?page=` wiring live in this trait. Without it the
    // filters reset to page 1 by hand and the paginator's own page links stop
    // being read, so the pager and the filters disagree about the current page.
    use WithPagination;

    public string $part = 'poultry';

    #[Url(as: 'q', history: false)]
    public string $search = '';

    #[Url(as: 'system', history: false)]
    public string $system = '';

    #[Url(as: 'letter', history: false)]
    public string $letter = '';

    #[Url(as: 'lesion', history: false)]
    public string $expanded = '';

    /**
     * The page, in the URL.
     *
     * `queryString` rather than a plain `#[Url]` so this rides the paginator's own
     * `?page=` rather than taking the parameter name from the attribute — the
     * shared pagination view builds its links from `$paginator->url()`, which
     * hardcodes `page`.
     */
    #[Url(as: 'page', history: false)]
    public int $page = 1;

    public function mount(?string $part = null): void
    {
        $this->part = $this->resolvePart($part);
    }

    /**
     * One page of lesions, filtered by search and body system.
     *
     * Letter is applied downstream on purpose. Filtering it in SQL would paginate
     * the wrong set — page 4 of "all lesions" is not page 4 of "lesions under L" —
     * and the rail has to reflect what is reachable before a letter is picked.
     *
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    #[Computed]
    public function lesions(): LengthAwarePaginator
    {
        return app(LesionAtlasService::class)->lesionsPaginator(
            $this->domain(),
            trim($this->search),
            $this->system,
            $this->part,
        );
    }

    /**
     * This page's lesions grouped into sections, headed by the whole-atlas count.
     *
     * Read as `$this->lesions`, never called. Livewire caches a computed property
     * on property access; a method call goes straight to the real method and
     * re-runs the whole service, which is how this page went from 11 queries to 34
     * before it was noticed.
     *
     * @return Collection<string, array<string, mixed>>
     */
    #[Computed]
    public function sections(): Collection
    {
        $rows = collect($this->lesions->items());

        if ($this->letter !== '') {
            $rows = $rows->filter(fn (array $lesion): bool => $lesion['letter'] === $this->letter);
        }

        return app(LesionAtlasService::class)->sections(
            $rows->values(),
            $this->sectionCounts,
        );
    }

    /**
     * Body-system tallies across every page.
     *
     * Separate from the paginator on purpose: a header reading "Digestive 48" above
     * four pages of Digestive contradicts the pager beneath it, and the reader
     * cannot tell which number is real.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function sectionCounts(): array
    {
        return app(LesionAtlasService::class)->sectionCounts(
            $this->domain(),
            trim($this->search),
            $this->system,
        );
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function letters(): array
    {
        return app(LesionAtlasService::class)->letters(
            $this->domain(),
            trim($this->search),
            $this->system,
        );
    }

    /**
     * @return array{lesions: int, diseases: int}
     */
    #[Computed]
    public function totals(): array
    {
        return app(LesionAtlasService::class)->totals($this->domain());
    }

    /**
     * Body systems holding at least one lesion under the current filters.
     *
     * @return array<string, array{name: string, count: int}>
     */
    #[Computed]
    public function systems(): array
    {
        $names = app(LesionAtlasService::class)->systemNames(
            $this->domain(),
            trim($this->search),
            $this->system,
        );

        $chips = [];

        foreach ($this->sectionCounts as $key => $count) {
            if (! array_key_exists($key, $names)) {
                continue;
            }

            $chips[$key] = ['name' => $names[$key], 'count' => $count];
        }

        return $chips;
    }

    /**
     * A search that empties the rail must not leave a letter selected against
     * nothing, or the page renders empty with no way back except the clear button.
     *
     * Every filter change also resets the page. Page 7 of the unfiltered list is
     * usually past the end of a filtered one, and the result is a blank page that
     * looks like the filter found nothing. This is the step Livewire pagination
     * quietly skips and it is the first thing a user reports as broken.
     */
    public function updatedSearch(): void
    {
        $this->resetDeadLetter();
        $this->resetPage();
    }

    public function updatedSystem(): void
    {
        $this->resetDeadLetter();
        $this->resetPage();
    }

    public function selectSystem(string $system): void
    {
        $this->system = $system === $this->system ? '' : $system;
        $this->resetDeadLetter();
        $this->resetPage();
    }

    /**
     * The letter rail jumps to the first page rather than paginating within the
     * letter, so a sparse letter is one page long and a dense one starts at its
     * beginning.
     */
    public function selectLetter(string $letter): void
    {
        $this->letter = $letter === $this->letter ? '' : $letter;
        $this->resetPage();
    }

    public function toggleLesion(string $lesion): void
    {
        $this->expanded = $lesion === $this->expanded ? '' : $lesion;
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->system = '';
        $this->letter = '';
        $this->resetPage();
    }

    public function hasFilters(): bool
    {
        return trim($this->search) !== '' || $this->system !== '' || $this->letter !== '';
    }

    private function resetDeadLetter(): void
    {
        if ($this->letter !== '' && ! in_array($this->letter, $this->letters, true)) {
            $this->letter = '';
        }
    }

    

    private function domain(): string
    {
        return (string) config("vetpedia.parts.{$this->part}.domain");
    }

    private function resolvePart(?string $part): string
    {
        $parts = array_keys((array) config('vetpedia.parts', []));

        return in_array($part, $parts, true) ? $part : (string) array_key_first($parts);
    }
};
?>

@php
    // Second parameter is the replacement map. Without it `__('…', ['count' => 3])`
    // drops the map and `:count` reaches the page literally.
    $t = fn (string $key, array $replace = []): string => __($this->part . '.atlas.' . $key, $replace);
    $sections = $this->sections;
    $systems = $this->systems;
    $letters = $this->letters;
    $lesions = $this->lesions;
    // The filtered total, not this page's row count: the hero says how many
    // lesions match, and the pager beneath says how many of them are on screen.
    $visible = $lesions->total();
    // Read from the counts, not from this page's sections: the stat is a
    // whole-atlas number and this page may hold no unclassified rows at all.
    $unclassifiedCount = $this->sectionCounts[LesionAtlasService::UNCLASSIFIED_KEY] ?? 0;
@endphp

<div class="space-y-6">
    <x-large-animals.page-hero
        :heading="$t('heading')"
        :subtitle="$t('subtitle')"
        :badge="$t('badge')"
        badgeIcon="scan-search"
        :stats="[
            ['count' => $this->totals['lesions'], 'label' => $t('stats.lesions'), 'icon' => 'scan-search'],
            ['count' => $this->totals['diseases'], 'label' => $t('stats.diseases'), 'icon' => 'stethoscope'],
            ['count' => count($systems), 'label' => $t('stats.systems'), 'icon' => 'layout-grid'],
            ['count' => $unclassifiedCount, 'label' => $t('stats.unclassified'), 'icon' => 'circle-help'],
        ]"
    />

    <p class="rounded-base bg-warning-soft border border-warning-subtle px-3 py-2 text-xs text-fg-warning-strong dark:text-yellow-300">
        {{ $t('grouping_note') }}
    </p>

    {{-- Filters ------------------------------------------------------------- --}}
    <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 p-4 space-y-4">
        <div class="relative">
            <span class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                <x-lucide-search class="w-4 h-4 text-body" />
            </span>
            <input type="search" wire:model.live.debounce.300ms="search"
                placeholder="{{ $t('search_placeholder') }}" aria-label="{{ $t('search_placeholder') }}"
                class="w-full bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs placeholder:text-body ps-10 pe-3 py-2.5 dark:bg-slate-900 dark:border-slate-600 dark:text-white" />
        </div>

        @if ($systems !== [])
            <div class="flex flex-wrap gap-1.5">
                @foreach ($systems as $key => $entry)
                    <button type="button" wire:click="selectSystem('{{ $key }}')"
                        @class([
                            'rounded-xs px-2.5 py-1 text-xs font-medium border transition-colors duration-150',
                            'bg-brand-soft border-brand text-fg-brand-strong' => $this->system === $key,
                            'bg-neutral-secondary-soft border-default-medium text-body dark:text-slate-300 dark:bg-slate-700 dark:border-slate-600 hover:border-brand' => $this->system !== $key,
                        ])>
                        {{ $entry['name'] }}
                        <span class="opacity-70">{{ $entry['count'] }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-3">
            @if ($letters !== [])
                <nav class="flex flex-wrap gap-1" aria-label="{{ $t('letter_nav') }}">
                    <button type="button" wire:click="selectLetter('')"
                        @class([
                            'rounded-xs px-2 py-0.5 text-xs font-semibold border transition-colors duration-150',
                            'bg-brand text-white border-brand' => $this->letter === '',
                            'border-transparent text-body dark:text-slate-400 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700' => $this->letter !== '',
                        ])>{{ $t('all_letters') }}</button>
                    @foreach ($letters as $letter)
                        <button type="button" wire:click="selectLetter('{{ $letter }}')"
                            @class([
                                'rounded-xs px-2 py-0.5 text-xs font-semibold border transition-colors duration-150',
                                'bg-brand text-white border-brand' => $this->letter === $letter,
                                'border-transparent text-body dark:text-slate-400 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700' => $this->letter !== $letter,
                            ])>{{ $letter }}</button>
                    @endforeach
                </nav>
            @endif

            <span class="ms-auto text-xs text-body dark:text-slate-400">{{ $t('showing', ['count' => $visible]) }}</span>

            @if ($this->hasFilters())
                <button type="button" wire:click="clearFilters"
                    class="inline-flex items-center gap-1 text-xs font-medium text-fg-brand hover:underline">
                    <x-lucide-x class="w-3.5 h-3.5" />
                    {{ $t('clear') }}
                </button>
            @endif
        </div>
    </div>

    {{-- Results ------------------------------------------------------------ --}}
    @if ($sections->isEmpty())
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 p-12 text-center">
            <x-lucide-search-x class="w-8 h-8 mx-auto text-body dark:text-slate-500" />
            <p class="mt-3 text-sm text-body dark:text-slate-400">{{ $t('empty') }}</p>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-2 items-start">
            @foreach ($sections as $section)
                <section class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 overflow-hidden">
                    <header class="flex items-baseline justify-between gap-3 border-b border-default-medium dark:border-slate-700 px-4 py-3">
                        <h2 class="flex items-center gap-2 text-sm font-bold text-heading dark:text-white">
                            @if ($section['system'] === null)
                                <x-lucide-circle-help class="w-4 h-4 text-body dark:text-slate-400" />
                                {{ $t('unclassified') }}
                            @else
                                <x-lucide-layout-grid class="w-4 h-4 text-body dark:text-slate-400" />
                                {{ $section['system']->localized_display_name }}
                            @endif
                        </h2>
                        @php
                            // The header counts the whole atlas. Where that is more
                            // than the rows on this page, say so rather than
                            // printing a number the list does not add up to.
                            $pageLabel = $section['count'] > $section['pageCount']
                                ? $t('count_on_page', ['on' => $section['pageCount']])
                                : null;
                        @endphp
                        <span class="text-xs tabular-nums text-body dark:text-slate-400">
                            {{ number_format($section['count']) }}
                            @if ($pageLabel)
                                <span class="opacity-75">· {{ $pageLabel }}</span>
                            @endif
                        </span>
                    </header>

                    <ul class="divide-y divide-default-medium dark:divide-slate-700">
                        @foreach ($section['lesions'] as $lesion)
                            <li>
                                <button type="button" wire:click="toggleLesion('{{ $lesion['id'] }}')"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-3 text-start transition-colors duration-150 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700"
                                    aria-expanded="{{ $this->expanded === (string) $lesion['id'] ? 'true' : 'false' }}">
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-heading dark:text-white">{{ $lesion['name'] }}</span>
                                        @if ($lesion['structure'] !== null)
                                            <span class="block text-xs text-body dark:text-slate-400">
                                                {{ $t('organ') }}: {{ $lesion['structure']->localized_display_name }}
                                            </span>
                                        @endif
                                    </span>
                                    <span class="flex shrink-0 items-center gap-2">
                                        <span class="rounded-xs bg-neutral-secondary-soft dark:bg-slate-700 px-2 py-0.5 text-xs tabular-nums text-body dark:text-slate-300">
                                            {{ $t('diseases_count', ['count' => $lesion['diseases']->count()]) }}
                                        </span>
                                        <x-lucide-chevron-down
                                            @class(['w-4 h-4 text-body dark:text-slate-400 transition-transform duration-150', 'rotate-180' => $this->expanded === (string) $lesion['id']]) />
                                    </span>
                                </button>

                                @if ($this->expanded === (string) $lesion['id'])
                                    <div class="px-4 pb-4">
                                        <div class="flex flex-col gap-4 sm:flex-row">
                                            {{-- Image-ready: a fixed 4:3 frame the photograph
                                                 drops into. There is no media table in this
                                                 application yet, so nothing renders here. --}}
                                            <div class="sm:w-48 shrink-0">
                                                <div class="flex aspect-4/3 w-full flex-col items-center justify-center gap-1.5 rounded-base border-2 border-dashed border-default-medium dark:border-slate-600 bg-neutral-secondary-soft dark:bg-slate-900/40 text-center">
                                                    <x-lucide-image-off class="w-5 h-5 text-body dark:text-slate-500" />
                                                    <span class="px-2 text-[11px] leading-tight text-body dark:text-slate-500">{{ $t('image_soon') }}</span>
                                                </div>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                @if ($lesion['diseases']->isEmpty())
                                                    <p class="text-sm text-body dark:text-slate-400">{{ $t('no_diseases') }}</p>
                                                @else
                                                    <h3 class="text-xs font-semibold uppercase tracking-wide text-body dark:text-slate-400">
                                                        {{ $t('caused_by') }}
                                                    </h3>
                                                    <ul class="mt-2 flex flex-wrap gap-1.5">
                                                        @foreach ($lesion['diseases'] as $disease)
                                                            <li>
                                                                <a href="{{ route($this->part . '.diseases.show', $disease->slug) }}" wire:navigate
                                                                    class="inline-block rounded-xs bg-danger-soft border border-danger-subtle px-2 py-0.5 text-xs text-fg-danger-strong hover:underline dark:text-red-300">
                                                                    {{ $disease->localized_name }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach

            {{-- Pager. Placed inside the results so it moves with the list, and
                 driven by the shared component so it inherits the Flowbite
                 tokens and the RTL chevrons rather than reimplementing them. --}}
            @if ($lesions->hasPages())
                <x-pagination
                    :paginator="$lesions"
                    translationPrefix="poultry.atlas"
                    borderTop
                />
            @endif
        </div>
    @endif
</div>