@props([
    /** Route NAME for the button, or null for a plain card with no button. */
    'route' => null,
    'heading' => '',
    'subtitle' => null,
    'button' => null,
])

{{--
    Closing bar. The whole block is the link when a route is given, so the
    button is decorative and carries aria-hidden rather than being a second,
    duplicate target for screen readers.
--}}
@if ($route)
    <a href="{{ route($route) }}" wire:navigate
        class="group block relative overflow-hidden rounded-base bg-gradient-to-r from-brand to-brand-strong dark:from-brand-subtle dark:to-brand-soft">
        <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-16 -end-16 w-64 h-64 rounded-full bg-white"></div>
        </div>

        <div class="relative px-6 py-8 sm:px-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-white">{{ $heading }}</h2>
                @if ($subtitle)
                    <p class="mt-1 text-sm text-white/85">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($button)
                <span class="shrink-0 inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-brand-strong bg-white rounded-base group-hover:bg-white/90 transition-all active:scale-[0.98]">
                    {{ $button }}
                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 transition-transform group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5" />
                </span>
            @endif
        </div>
    </a>
@else
    <div class="relative overflow-hidden rounded-base bg-gradient-to-r from-brand to-brand-strong dark:from-brand-subtle dark:to-brand-soft">
        <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-16 -end-16 w-64 h-64 rounded-full bg-white"></div>
        </div>

        <div class="relative px-6 py-8 sm:px-10">
            <h2 class="text-lg sm:text-xl font-bold text-white">{{ $heading }}</h2>
            @if ($subtitle)
                <p class="mt-1 text-sm text-white/85">{{ $subtitle }}</p>
            @endif
            @unless (trim((string) $slot) === '')
                <div class="mt-4 flex flex-wrap gap-3">{{ $slot }}</div>
            @endunless
        </div>
    </div>
@endif