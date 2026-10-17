<?php

use App\Enums\OntologyType;
use App\Models\Disease;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The disease list for one part — `/poultry/diseases` and `/large-animals/diseases`.
 *
 * Shared by both parts for the reason the comparison builder is shared (§9.19):
 * listing a part's diseases is the same operation on both sides and only the
 * candidate pool differs. Neither part had a list before this — both went straight
 * from the hub to `diseases/{slug}` — so there is no large-animal page to copy and
 * no risk of one having diverged from the other.
 *
 * **The drugs list at `/drugs/diseases` is a different page and was left alone.**
 * It is unscoped by design: it answers "which diseases does this drug treat",
 * which is a question about the catalogue rather than about a species. It also
 * searches with `whereFullText(..., 'boolean')`, which is MySQL-only and has
 * therefore never run under the SQLite test database.
 *
 * **The sign/lesion counts follow the exclusion rule, not a `'sign'` match.**
 * `findings.ontology_type` is nullable and is null on the curated signs — Pyrexia,
 * Inappetence and Hemorrhagic diarrhea — so matching `Sign` drops the three most
 * common signs in the clinical vocabulary. A post-mortem finding is explicitly
 * typed `Lesion`; anything else is an observation. This is the same rule Phase 7
 * had to fix on the comparison matrix, and it is the rule `Disease::signItems()`
 * and `Disease::lesionItems()` already use, so the list and the detail page cannot
 * disagree about what a disease carries.
 *
 * The letters come from the canonical name, never the localised one: on the
 * Arabic page the localised first character is never `[A-Z]`, so a localised
 * alphabet filter would match nothing there.
 */
new class extends Component
{
    // `resetPage()` and the `?page=` wiring live in this trait. Without it the
    // letter filter resets to page 1 by hand and the paginator's own page links
    // stop being read, so the pager and the rail disagree about the current page.
    use WithPagination;

    private const PER_PAGE = 21;

    public string $part = 'large-animals';

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
     * One page of this part's diseases.
     *
     * Alphabetised, scoped to the domain, and narrowed by the letter rail. The
     * three counts are subqueries on the one paginator query rather than reads on
     * a loaded relation — 21 cards, three relations each, is 63 queries if this is
     * done the obvious way.
     *
     * @return LengthAwarePaginator<int, Disease>
     */
    #[Computed]
    public function diseases(): LengthAwarePaginator
    {
        $query = Disease::query()
            ->forDomain($this->domain())
            ->withCount([
                'products',
                'clinicalSigns as sign_items_count' => fn (Builder $q) => $q
                    ->whereDoesntHave('finding', fn (Builder $f) => $f
                        ->where('ontology_type', OntologyType::Lesion)),
                'clinicalSigns as lesion_items_count' => fn (Builder $q) => $q
                    ->whereHas('finding', fn (Builder $f) => $f
                        ->where('ontology_type', OntologyType::Lesion)),
            ]);

        if ($this->letter !== '') {
            $query->where('name', 'like', $this->letter.'%');
        }

        // `withPath()` is not optional here. Left alone, the paginator reuses the
        // current request's path, and inside a Livewire render that path arrives
        // *without* its leading slash — so the shared pagination view emitted
        // `diseases?page=2`, which `wire:navigate` resolves against `/poultry`
        // into `/poultry/diseases?page=2` only by accident and 404s on any path
        // that is not itself the section root.
        //
        // `appends()` is the mirror of that trap and just as necessary. The shared
        // pagination view builds its page links from `withQueryString()`, which
        // reads the *request's* query string — and a Livewire update has none: the
        // letter travels in the request body and only reaches the address bar
        // through `#[Url]`. Without this, filtering to "C" and then clicking page 2
        // silently drops the filter while the rail still shows "C" selected, and
        // page 2 of the unfiltered list is not what the reader asked for.
        return $query
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withPath(route($this->part.'.diseases.index'))
            ->appends(array_filter(['letter' => $this->letter], fn (string $value): bool => $value !== ''));
    }

    /**
     * The letters this part actually has diseases under.
     *
     * A rail offering all 26 letters on a part with 18 diseases is mostly dead
     * keys, so the list is derived from the pool rather than hardcoded. Read from
     * the canonical `name` and not the localised one — see the class docblock.
     *
     * `SUBSTR`, not `LEFT`. `LEFT` does not exist in SQLite, so the MySQL spelling
     * is a query that cannot run under the test database at all — which is how the
     * drugs disease list ended up with no tests and an unverified search. Both
     * engines accept `SUBSTR(name, 1, 1)`.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function letters(): array
    {
        return Disease::query()
            ->forDomain($this->domain())
            ->where('is_active', true)
            ->selectRaw('UPPER(SUBSTR(name, 1, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter')
            ->filter(fn (?string $letter): bool => $letter !== null && preg_match('/^[A-Z]$/', $letter) === 1)
            ->values()
            ->all();
    }

    #[Computed]
    public function totals(): array
    {
        return [
            'diseases' => Disease::query()->forDomain($this->domain())->where('is_active', true)->count(),
        ];
    }

    /**
     * Clicking the active letter clears it — the rail's own "off" control, so a
     * reader who filtered themselves into a corner does not have to find the
     * clear button.
     */
    public function selectLetter(string $letter): void
    {
        $this->letter = $letter === $this->letter ? '' : $letter;
        $this->resetPage();
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
    $t = fn (string $key, array $replace = []): string => __($this->part . '.diseases_list.' . $key, $replace);
@endphp

<div class="space-y-6">
    <x-large-animals.page-hero
        :heading="$t('heading')"
        :subtitle="$t('subtitle')"
        :badge="$t('badge')"
        badgeIcon="bug"
        :stats="[
            ['count' => number_format($this->totals['diseases']), 'label' => $t('total'), 'icon' => 'bug'],
        ]"
    />

    <x-letter-nav :letters="$this->letters" :active="$this->letter" wire-action="selectLetter" />

    <div class="bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-slate-800 dark:border-slate-700">
        <div class="px-4 py-3 border-b border-default-medium dark:border-slate-700 flex items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-heading dark:text-white">{{ $t('directory_title') }}</h2>
            <span class="text-xs tabular-nums text-body dark:text-slate-400">
                {{ $t('showing', ['count' => $this->diseases->total()]) }}
            </span>
        </div>

        <div class="p-4">
            @if ($this->diseases->isEmpty())
                <div class="py-16 text-center">
                    <x-lucide-search-x class="w-10 h-10 mx-auto mb-3 text-body dark:text-slate-500" />
                    <h3 class="text-base font-semibold text-heading dark:text-white mb-1">{{ $t('empty_title') }}</h3>
                    <p class="text-sm text-body dark:text-slate-400">{{ $t('empty_desc') }}</p>
                    @if ($this->letter !== '')
                        <button type="button" wire:click="selectLetter('')"
                            class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-fg-brand hover:underline cursor-pointer">
                            <x-lucide-x class="w-3.5 h-3.5" />
                            {{ $t('clear_letter') }}
                        </button>
                    @endif
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->diseases as $disease)
                        {{-- A container, not a button: it holds the link to the detail
                             page and the counters read off it, and a link inside a
                             button is not a link. --}}
                        <div class="group flex flex-col bg-neutral-primary-soft dark:bg-slate-800 rounded-base border border-default-medium dark:border-slate-600 hover:border-brand hover:shadow-md dark:hover:border-brand transition-all overflow-hidden">
                            <div class="p-4 grow">
                                <a href="{{ route($this->part . '.diseases.show', $disease->slug) }}" wire:navigate
                                    class="block group/title">
                                    <h3 class="text-lg font-bold leading-snug tracking-wide text-heading dark:text-white line-clamp-2 transition-colors duration-150 group-hover/title:text-fg-brand dark:group-hover/title:text-brand"
                                        title="{{ $disease->name }}">{{ $disease->name }}</h3>
                                    {{-- The Arabic name only when it is actually a
                                         different string. Every curated row stores
                                         `name_ar` equal to `name`, and an unguarded
                                         @if printed the name twice on every card.

                                         Deliberately **no** `tracking-*` here, where the
                                         heading above has it. `letter-spacing` is
                                         applied between glyphs, and Arabic is a
                                         cursive script whose letters join along the
                                         baseline — spacing them pulls the word apart
                                         into unconnected letterforms. The heading is
                                         safe because it always renders the canonical
                                         `name`, which is never Arabic. --}}
                                    @if (filled($disease->name_ar) && $disease->name_ar !== $disease->name)
                                        <p class="mt-1 text-xs text-body dark:text-slate-400 line-clamp-1" dir="rtl">{{ $disease->name_ar }}</p>
                                    @endif
                                </a>

                                <dl class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1.5">
                                    <div class="inline-flex items-center gap-1.5">
                                        <x-lucide-activity class="w-3.5 h-3.5 text-body dark:text-slate-500" />
                                        <dt class="sr-only">{{ $t('signs') }}</dt>
                                        <dd class="text-xs tabular-nums text-body dark:text-slate-300">{{ number_format((int) $disease->sign_items_count) }}</dd>
                                    </div>
                                    <div class="inline-flex items-center gap-1.5">
                                        <x-lucide-scan-search class="w-3.5 h-3.5 text-body dark:text-slate-500" />
                                        <dt class="sr-only">{{ $t('lesions') }}</dt>
                                        <dd class="text-xs tabular-nums text-body dark:text-slate-300">{{ number_format((int) $disease->lesion_items_count) }}</dd>
                                    </div>
                                    <div class="inline-flex items-center gap-1.5">
                                        <x-lucide-pill class="w-3.5 h-3.5 text-body dark:text-slate-500" />
                                        <dt class="sr-only">{{ $t('drugs') }}</dt>
                                        <dd class="text-xs tabular-nums text-body dark:text-slate-300">{{ number_format((int) $disease->products_count) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="px-4 py-3 border-t border-default-medium dark:border-slate-700 bg-neutral-secondary-soft dark:bg-slate-900/40 flex items-center justify-end">
                                <a href="{{ route($this->part . '.diseases.show', $disease->slug) }}" wire:navigate
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-fg-brand hover:underline dark:text-brand">
                                    {{ $t('view_disease') }}
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-0.5" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if ($this->diseases->hasPages())
        <x-pagination
            :paginator="$this->diseases"
            :translation-prefix="$this->part . '.diseases_list'"
            borderTop
        />
    @endif
</div>