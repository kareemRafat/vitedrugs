@props([
    /** Route NAME, not a URL — resolved here so a typo fails loudly. */
    'route' => null,
    /** 'primary' is the filled button, 'secondary' the outlined one. */
    'variant' => 'primary',
    /** Lucide icon name shown before the label. */
    'icon' => null,
])

@php
    // Both weights defined once. The old pages re-declared these gradients in
    // three different files, which is how they drifted.
    $weight = $variant === 'primary' ? 'font-bold' : 'font-semibold';

    $surface = $variant === 'primary'
        ? 'text-brand-strong bg-white hover:bg-white/90 shadow-lg shadow-black/10'
        : 'text-white border border-white/40 hover:bg-white/10';
@endphp

<a href="{{ route($route) }}" wire:navigate
    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 text-sm rounded-base transition-all active:scale-[0.98] {{ $weight }} {{ $surface }}">
    @if ($icon)
        <x-dynamic-component :component="'lucide-'.$icon" class="w-4 h-4" />
    @endif
    {{ $slot }}
</a>