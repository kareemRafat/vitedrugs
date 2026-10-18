@props([
    /** Lucide icon name. */
    'icon' => null,
    'title' => '',
    'description' => null,
    /** Route NAME, not a URL — resolved here so a typo fails loudly. */
    'route' => null,
    /** Label for the footer link, e.g. 'Open'. */
    'cta' => null,
])

{{--
    A tool or library card.

    `mt-auto` on the footer link is what keeps the arrows on a shared baseline
    when one description runs to three lines and the next runs to one.
--}}
<a href="{{ route($route) }}" wire:navigate
    class="group flex flex-col p-5 rounded-base shadow-xs border border-default-medium bg-neutral-primary-soft hover:border-brand hover:shadow-md transition-all duration-300 dark:bg-slate-800 dark:border-slate-700 dark:hover:border-brand">
    @if ($icon)
        <div class="w-11 h-11 mb-4 rounded-xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center group-hover:scale-105 transition-transform">
            <x-dynamic-component :component="'lucide-'.$icon" class="w-5 h-5" />
        </div>
    @endif

    <h3 class="text-base font-bold text-heading dark:text-white">{{ $title }}</h3>

    @if ($description)
        <p class="mt-1.5 text-sm text-body dark:text-slate-400 leading-relaxed">{{ $description }}</p>
    @endif

    @if ($cta)
        <span class="mt-auto pt-4 inline-flex items-center gap-1 text-sm font-semibold text-fg-brand dark:text-brand group-hover:gap-2 transition-all">
            {{ $cta }}
            <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
        </span>
    @endif
</a>