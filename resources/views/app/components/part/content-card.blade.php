@props([
    'href' => null,
    /** Small chip above the title, e.g. a category or an article type. */
    'chip' => null,
    'title' => '',
    /** Short supporting copy under the title, clamped to two lines. */
    'excerpt' => null,
    /** Footer, leading side — usually a date. */
    'metaStart' => null,
    /**
     * Machine-readable form of `metaStart`, e.g. '2026-02-14'. When present the
     * leading footer item is wrapped in a <time datetime> instead of a bare
     * span, so the date stays parseable rather than only being drawn.
     */
    'metaStartIso' => null,
    /** Footer, trailing side — usually a read time or a category. */
    'metaEnd' => null,
])

{{--
    The one card design every content teaser on a part page uses.

    It takes already-resolved strings rather than a model, because three
    different models feed it (Blog, MedicalArticle) and each has its own
    Arabic columns. The mapping lives in the wrapper component; this file is
    only the shape. That split is what keeps the /drugs blog cards and the
    /large-animals article cards from drifting apart.

    There is deliberately no image slot. Every row in `blogs` has
    `cover_image` empty, so an image here would render a column of identical
    placeholder boxes. If covers start arriving, this is where the slot goes.

    `mt-auto` on the footer pins it to the bottom of every card in the row
    however long the titles run.
--}}
<a href="{{ $href }}"
    {{ $attributes->class([
        'group flex flex-col p-5 rounded-base shadow-xs border border-default-medium bg-neutral-primary-soft',
        'hover:border-brand hover:shadow-md transition-all duration-300',
        'dark:bg-slate-800 dark:border-slate-700 dark:hover:border-brand',
    ]) }}>

    @if ($chip)
        <span class="self-start inline-flex items-center px-2.5 py-1 mb-3 rounded-full text-[11px] font-semibold bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
            {{ $chip }}
        </span>
    @else
        {{-- Reserves the chip's row so titles still line up across the row when
             one entry happens to have no chip. --}}
        <span class="self-start mb-3" aria-hidden="true"></span>
    @endif

    <h3 class="text-base font-bold text-heading dark:text-white group-hover:text-brand dark:group-hover:text-brand transition-colors duration-150 line-clamp-2">
        {{ $title }}
    </h3>

    @if ($excerpt)
        <p class="mt-2 text-sm text-body dark:text-slate-400 leading-relaxed line-clamp-2">{{ $excerpt }}</p>
    @endif

    @if ($metaStart || $metaEnd)
        <div class="mt-auto pt-4 flex items-center justify-between gap-3 border-t border-default-medium dark:border-slate-700 text-xs text-body dark:text-slate-400">
            @if ($metaStart)
                @if ($metaStartIso)
                    <time datetime="{{ $metaStartIso }}" class="truncate">{{ $metaStart }}</time>
                @else
                    <span class="truncate">{{ $metaStart }}</span>
                @endif
            @else
                <span></span>
            @endif

            @if ($metaEnd)
                <span class="shrink-0">{{ $metaEnd }}</span>
            @endif
        </div>
    @endif
</a>