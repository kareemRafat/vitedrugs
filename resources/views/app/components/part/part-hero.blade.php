@props([
    'badge' => null,
    /** Lucide icon name shown in the badge, e.g. 'paw-print'. */
    'badgeIcon' => null,
    'heading' => '',
    'subtitle' => null,
])

{{--
    The hero every part page opens with.

    A brand-coloured gradient card rather than a photograph. That is deliberate:
    the page below the navbar is inset by app.layouts.master's gutter, so a
    full-bleed band would need a layout change to work, and the rounded card is
    what the rest of each part already looks like.

    Colours are the part's own `--color-brand` tokens, so this component is
    orange on /large-animals and green on /poultry without being told which.
    The primary/secondary buttons are x-part.hero-action, so the two weights
    stay defined once instead of per page.
--}}
<section class="relative overflow-hidden rounded-base bg-gradient-to-br from-brand to-brand-strong dark:from-brand-subtle dark:to-brand-soft">
    <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none" aria-hidden="true">
        <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-white dark:bg-brand-soft"></div>
        <div class="absolute -bottom-32 -start-32 w-80 h-80 rounded-full bg-white dark:bg-brand-soft"></div>
    </div>

    <div class="relative px-6 py-14 sm:py-20 max-w-3xl mx-auto text-center">
        @if ($badge)
            <span class="inline-flex items-center gap-2 px-4 py-1.5 mb-5 bg-white/15 backdrop-blur-sm rounded-full text-xs font-semibold uppercase tracking-widest text-white border border-white/20">
                @if ($badgeIcon)
                    <x-dynamic-component :component="'lucide-'.$badgeIcon" class="w-4 h-4" />
                @endif
                {{ $badge }}
            </span>
        @endif

        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
            {{ $heading }}
        </h1>

        @if ($subtitle)
            <p class="mt-4 text-base sm:text-lg text-white/85 leading-relaxed">{{ $subtitle }}</p>
        @endif

        @unless (trim((string) $slot) === '')
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                {{ $slot }}
            </div>
        @endunless
    </div>
</section>