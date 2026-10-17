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
 * **The layout is a grid of photograph tiles, and the tile is the whole answer.**
 * That is the shape the page takes because it is waiting for photographs: a
 * lesion's photograph is the first thing worth seeing, so a tile is a frame at
 * the photograph's aspect ratio and the name sits under it.
 *
 * There is no drawer. It was built and then cut, and the reason is the data
 * rather than the taste: 571 of the 640 poultry lesions name exactly one
 * disease. A drawer over that shows the name, the organ, the system and the
 * photograph frame the tile already had, plus a single disease link — so every
 * click dims the page, slides a panel in, and hands back what was already on
 * screen. What the drawer would really be for is the full-size photograph, and
 * there is no media table in this application to put one in. When photographs
 * arrive, the drawer comes back for the image and the long tail; the worst
 * lesion here names 43 diseases, which is the only list too long for a tile.
 *
 * The diseases a lesion causes therefore sit on the tile, linked. That also
 * makes the one thing a reader came for reachable without a round trip.
 *
 * The body-system sections survive the change: a tile says which system it is
 * in, but the section header is what reports the whole-atlas count, and
 * dropping it would put a number on screen that the grid beneath it does not
 * add up to.
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
     * One page of lesions, filtered by search, body system and letter.
     *
     * All three are applied in SQL. The letter used to be filtered here in PHP,
     * after the paginator had already chosen a page — so page 2 meant "rows 49 to
     * 96 of the whole atlas, then keep the A's". That page is nearly always empty
     * while the pager underneath still offers thirteen more pages, which is how a
     * reader ended up on "no data" with a full pager above it.
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
            $this->letter,
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
     * **The system filter is deliberately not passed here.** These counts answer
     * "how big is each system", which is a property of the atlas and of the search
     * — not of the system currently selected. Passing `$this->system` made the
     * counts collapse to the one selected system, and because the filter chips are
     * built from these counts, selecting any system deleted every other chip from
     * the rail. The reader could pick a system and then have no way to pick a
     * different one except the clear button.
     *
     * It also corrupted two hero stats, which read from here: the systems count
     * fell to 1 while a system was selected, and "Not yet placed" fell to 0
     * because the unplaced lesions are excluded by any system filter. Both are
     * atlas totals, like "640 lesions recorded", and they must not move when a
     * filter is applied.
     *
     * The counts are unaffected for the selected system itself — filtering to
     * Digestive yields exactly the lesions these counts attribute to Digestive, so
     * the header and the chip still agree with the rows.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function sectionCounts(): array
    {
        return app(LesionAtlasService::class)->sectionCounts(
            $this->domain(),
            trim($this->search),
            '',
            $this->letter,
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
     * The filter chips: every system a reader can switch to, with its whole-atlas
     * count.
     *
     * The system filter is deliberately not passed to `systemNames()` for the same
     * reason `sectionCounts()` ignores it — the rail lists the options, and a list
     * of options narrowed to the one already chosen is a list of one option.
     *
     * The chip *counts* come from `sectionCounts()` above, which is also
     * system-independent, so a chip never advertises a number the grid beneath it
     * cannot reach. A system with no lesion under the current search has no key in
     * that map and is skipped here, so the rail never offers a dead chip.
     *
     * @return array<string, array{name: string, count: int}>
     */
    #[Computed]
    public function systems(): array
    {
        $names = app(LesionAtlasService::class)->systemNames(
            $this->domain(),
            trim($this->search),
            '',
            $this->letter,
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

    public function selectLetter(string $letter): void
    {
        $this->letter = $letter === $this->letter ? '' : $letter;
        $this->resetPage();
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

{{--
    Two wrappers, deliberately.

    `relative` on the outer one so the progress bar can pin itself to the top of
    this block rather than to the viewport — a page-wide bar would read as a
    browser loading bar for a filter change that only touches one section.

    `space-y-6` on the *inner* one, and the drawer is a sibling of that rather
    than its last child. Tailwind v4 implements `space-y-6` as
    `> :not(:last-child) { margin-block-end: 24px }`, so a child only keeps its
    spacing while it is last. Rendering the drawer inside the spaced element
    made the content stop being `:last-child` the moment a tile was opened, and
    it gained 24px — the footer visibly jumped down and sprang back on close.
    Keeping the overlay outside the spaced run means opening the drawer cannot
    change anyone's `:last-child` status, which is what makes this safe rather
    than merely fixed today.
--}}
<div class="relative">
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
                            'rounded-xs px-2.5 py-1 text-xs font-medium border transition-colors duration-150 cursor-pointer',
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
                            'rounded-xs px-2 py-0.5 text-xs font-semibold border transition-colors duration-150 cursor-pointer',
                            'bg-brand text-white border-brand' => $this->letter === '',
                            'border-transparent text-body dark:text-slate-400 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700' => $this->letter !== '',
                        ])>{{ $t('all_letters') }}</button>
                    @foreach ($letters as $letter)
                        <button type="button" wire:click="selectLetter('{{ $letter }}')"
                            @class([
                                'rounded-xs px-2 py-0.5 text-xs font-semibold border transition-colors duration-150 cursor-pointer',
                                'bg-brand text-white border-brand' => $this->letter === $letter,
                                'border-transparent text-body dark:text-slate-400 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700' => $this->letter !== $letter,
                            ])>{{ $letter }}</button>
                    @endforeach
                </nav>
            @endif

            <span class="ms-auto text-xs text-body dark:text-slate-400">{{ $t('showing', ['count' => $visible]) }}</span>

            @if ($this->hasFilters())
                <button type="button" wire:click="clearFilters"
                    class="inline-flex items-center gap-1 text-xs font-medium text-fg-brand hover:underline cursor-pointer">
                    <x-lucide-x class="w-3.5 h-3.5" />
                    {{ $t('clear') }}
                </button>
            @endif
        </div>
    </div>

    {{-- The request indicator.
         A bar across the top of the results, not a spinner beside the count: the
         filter row reflows when a chip wraps, so anything sitting in it jumps
         around, and this page replaces every tile on each request — a spinner the
         size of one tile reads as "one tile is busy" rather than "the page is
         loading". Absolutely positioned and 2px tall, so it never takes layout
         space away from the grid when it appears. --}}
    <div class="absolute inset-x-0 -top-1 h-0.5 overflow-hidden rounded-full" aria-hidden="true">
        <div wire:loading class="atlas-progress absolute inset-0 bg-brand-subtle/40 dark:bg-brand-subtle/60"></div>
    </div>
    <span wire:loading class="sr-only" role="status">{{ $t('loading') }}</span>

    {{-- Results ------------------------------------------------------------ --}}
    @if ($sections->isEmpty())
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 p-12 text-center">
            <x-lucide-search-x class="w-8 h-8 mx-auto text-body dark:text-slate-500" />
            <p class="mt-3 text-sm text-body dark:text-slate-400">{{ $t('empty') }}</p>
        </div>
    @else
        <div class="space-y-6">
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
                            // than the tiles on this page, say so rather than
                            // printing a number the grid does not add up to.
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

                    {{-- The grid. A tile is a photograph that has not been taken yet, so the frame
                         is drawn at the ratio the photograph will use.

                         The tile is a container, not a button: it holds links to
                         the diseases that cause the lesion, and a link inside a
                         button is not a link. It also means the reader's next
                         action — opening a disease — is the thing they can reach
                         directly, rather than a panel they have to open first to
                         find. --}}
                    <div class="grid gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($section['lesions'] as $lesion)
                            <div class="group flex flex-col bg-neutral-primary-soft dark:bg-slate-800 rounded-base border border-default-medium dark:border-slate-600 hover:border-brand hover:shadow-md dark:hover:border-brand transition-all overflow-hidden">
                                <div class="relative flex aspect-4/3 w-full flex-col items-center justify-center gap-1 bg-neutral-secondary-soft dark:bg-slate-900/40 border-b border-default-medium dark:border-slate-700">
                                    {{-- The photograph, when there is one. The frame
                                         keeps the same 4:3 box either way, so a
                                         page of photographed lesions and a page of
                                         unphotographed ones are the same grid —
                                         which is what makes the atlas useful while
                                         it is still filling up. --}}
                                    @if ($lesion['photo'] !== null)
                                        {{-- `alt` names the image so a screen reader
                                             says which lesion it belongs to; `title`
                                             carries the caption, which describes it.
                                             Same string would do neither job
                                             properly. --}}
                                        <img src="{{ $lesion['photo']->url() }}"
                                            alt="{{ $lesion['name'] }}"
                                            @if ($lesion['photo']->localized_caption !== '')
                                                title="{{ $lesion['photo']->localized_caption }}"
                                            @endif
                                            loading="lazy"
                                            class="absolute inset-0 h-full w-full object-cover" />
                                    @else
                                        <x-lucide-image-off class="w-6 h-6 text-body dark:text-slate-500 group-hover:text-fg-brand transition-colors duration-150" />
                                        {{-- Not a visible caption: forty-eight tiles all
                                             reading "photograph coming soon" is noise on a
                                             page whose job is to be scannable. It is
                                             announced instead, because the frame being a
                                             placeholder rather than a failed image is
                                             exactly what a screen reader needs told. --}}
                                        <span class="sr-only">{{ $t('image_soon') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1 p-3 grow">
                                    <h3 class="text-sm font-semibold text-heading dark:text-white leading-snug line-clamp-2">{{ $lesion['name'] }}</h3>
                                    <p class="text-xs text-body dark:text-slate-400 truncate">
                                        {{ $lesion['structure'] !== null
                                            ? $t('organ').': '.$lesion['structure']->localized_display_name
                                            : $t('names_no_organ') }}
                                    </p>
                                    {{-- No system name here: the section header above
                                         already says which system these tiles are in,
                                         and repeating it per tile is what makes a grid
                                         read as a list. --}}

                                    @if ($lesion['diseases']->isEmpty())
                                        <p class="mt-auto pt-2 text-xs text-body dark:text-slate-400">{{ $t('no_diseases') }}</p>
                                    @else
                                        @php
                                            // Three, then a count. The long tail is real —
                                            // one lesion names 43 diseases — but a tile
                                            // listing all of them is a wall of chips and
                                            // stops being scannable, which is the one thing
                                            // this grid is for.
                                            $shown = $lesion['diseases']->take(3);
                                            $rest = $lesion['diseases']->count() - $shown->count();
                                        @endphp
                                        <p class="mt-auto pt-2">
                                            <span class="inline-block rounded-xs bg-neutral-secondary-soft dark:bg-slate-700 px-2 py-0.5 text-[11px] tabular-nums text-body dark:text-slate-300">
                                                {{ $t('diseases_count', ['count' => $lesion['diseases']->count()]) }}
                                            </span>
                                        </p>
                                        <ul class="mt-1.5 flex flex-wrap gap-1">
                                            @foreach ($shown as $disease)
                                                <li>
                                                    <a href="{{ route($this->part . '.diseases.show', $disease->slug) }}" wire:navigate
                                                        class="inline-block max-w-full truncate rounded-xs bg-danger-soft border border-danger-subtle px-1.5 py-0.5 text-[11px] text-fg-danger-strong hover:underline dark:text-red-300"
                                                        title="{{ $disease->localized_name }}">{{ $disease->localized_name }}</a>
                                                </li>
                                            @endforeach
                                            @if ($rest > 0)
                                                <li>
                                                    <span class="inline-block rounded-xs bg-neutral-secondary-soft dark:bg-slate-700 px-1.5 py-0.5 text-[11px] tabular-nums text-body dark:text-slate-300">
                                                        +{{ $rest }}
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            {{-- Pager. Placed inside the results so it moves with the grid, and
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
</div>
