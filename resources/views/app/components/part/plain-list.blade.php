@props([
    /**
     * Each entry is:
     *   href   — a fully resolved URL. Resolved by the caller rather than here,
     *            because every row on these pages points at a route that takes a
     *            slug, and a component that guessed the parameter name would be
     *            one rename away from 404ing every list on the site.
     *   title  — the row's name
     *   meta   — optional supporting line under the title
     *   icon   — optional Lucide name shown before the title
     *   badges — optional list of ['label' => string, 'tone' => 'brand'|'danger']
     *
     * @var list<array{href: string, title: string, meta?: string|null, icon?: string|null, badges?: list<array{label: string, tone?: string}>}>
     */
    'items' => [],
])

{{--
    A plain list of real rows — recent articles, top microorganisms, diseases
    ranked by how much is recorded about them.

    Deliberately not a card grid. These sections exist to show that the database
    has depth, and the evidence is the rows themselves: names, counts and dates
    that came out of a query. Anything more decorative would compete with the
    content it is supposed to be evidence of.

    `divide-y` on the container gives the rules for free, so adding a row needs
    no markup change. Callers hide the whole section when the collection is
    empty rather than rendering an empty shell.
--}}
<div class="rounded-base shadow-xs border border-default-medium bg-neutral-primary-soft divide-y divide-default-medium dark:bg-slate-800 dark:border-slate-700 dark:divide-slate-700">
    @foreach ($items as $item)
        <a href="{{ $item['href'] }}"
            class="flex items-start gap-3 sm:gap-4 p-4 transition-colors duration-150 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand">

            @if ($item['icon'] ?? null)
                <span class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                    <x-dynamic-component :component="'lucide-'.$item['icon']" class="w-4 h-4" />
                </span>
            @endif

            <span class="flex-1 min-w-0">
                <span class="block text-sm font-semibold text-heading dark:text-white">{{ $item['title'] }}</span>

                @if ($item['meta'] ?? null)
                    <span class="block mt-0.5 text-xs text-body dark:text-slate-400">{{ $item['meta'] }}</span>
                @endif

                @if (! empty($item['badges']))
                    <span class="flex flex-wrap gap-1.5 mt-2">
                        @foreach ($item['badges'] as $badge)
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold',
                                'bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand' => ($badge['tone'] ?? 'brand') === 'brand',
                                'bg-danger-soft text-fg-danger-strong dark:bg-red-950/60 dark:text-red-300' => ($badge['tone'] ?? 'brand') === 'danger',
                            ])>{{ $badge['label'] }}</span>
                        @endforeach
                    </span>
                @endif
            </span>

            <x-lucide-chevron-right class="w-4 h-4 shrink-0 mt-1 text-body dark:text-slate-500 rtl:rotate-180" />
        </a>
    @endforeach
</div>