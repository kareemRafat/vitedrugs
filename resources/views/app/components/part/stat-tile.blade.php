@props([
    /** Lucide icon name. */
    'icon' => null,
    'label' => '',
    'value' => 0,
])

{{--
    One figure in the stats band.

    The number is `tabular-nums` because these sit in a grid and the default
    proportional figures make the baseline jitter as the values differ in
    digit count.

    The figure is emitted on the same line as the opening tag on purpose.
    PoultryLandingTest scrapes these values out of the rendered HTML with
    `/tabular-nums leading-none">([\d,]+)</`, so a newline between `>` and the
    digits breaks the tests without breaking the page.
--}}
<div class="p-4 text-center rounded-base shadow-xs border border-default-medium bg-neutral-primary-soft dark:bg-slate-800 dark:border-slate-700">
    @if ($icon)
        <div class="inline-flex items-center justify-center w-8 h-8 mb-2 rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
            <x-dynamic-component :component="'lucide-'.$icon" class="w-4 h-4" />
        </div>
    @endif

    <p class="text-2xl font-extrabold text-heading dark:text-white tabular-nums leading-none">{{ is_numeric($value) ? number_format($value) : $value }}</p>

    <p class="mt-1.5 text-xs font-medium text-body dark:text-slate-400">{{ $label }}</p>
</div>