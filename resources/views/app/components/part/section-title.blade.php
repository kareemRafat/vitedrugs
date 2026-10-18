@props([
    'heading' => '',
    'subtitle' => null,
])

{{--
    Band heading. Centred, with the subtitle underneath — the one shape both
    part pages already used for their section titles.
--}}
<div class="text-center max-w-2xl mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white tracking-tight">{{ $heading }}</h2>

    @if ($subtitle)
        <p class="mt-2 text-sm sm:text-base text-body dark:text-slate-400">{{ $subtitle }}</p>
    @endif
</div>