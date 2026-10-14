@props([
    /**
     * Organs keyed by `canonical_name`, each `['name' => string, 'diseases' => int]`.
     */
    'organs' => [],
    'selectedOrgan' => '',
    'selectedSystem' => '',
    'label' => '',
])

{{--
    The bird, drawn from a supplied illustration rather than here.

    This started as an inline SVG hen, because no illustration had been licensed
    and none existed in the repository. One has since been supplied, so the bird is
    an image and this file is the layer that makes it navigable.

    Hotspots are organs and they are authored here, not data-driven: the geometry
    is a drawing, and a drawing does not come out of a join. Each entry is keyed
    by the same `canonical_name` the structures table uses, so a key that stops
    matching does not error — the marker simply never renders and the organ
    quietly falls off the bird. That is the only place it shows up, which is why
    `PartBodyMapTest` asserts the keys as hard as it does.

    `intestines` is what the diagram calls "Small Intestine". The artwork splits
    large from small, the table keeps one `intestines` row, and that row is the
    only structure in the whole vocabulary carrying curated links — so the label
    points at the structure that can actually answer.

    The image is never mirrored for RTL: anatomy should not reverse under an
    Arabic locale, so markers stay on `left`/`top` and the illustration keeps
    facing start.
--}}
@php
    /**
     * One entry per organ labelled on the illustration. Percentages of the image
     * box, so the map stays responsive and the markers move with the artwork.
     *
     * @var array<string, array{label: string, x: float, y: float}>
     */
    $hotspots = [
        'eye' => ['label' => 'Eye', 'x' => 21.6, 'y' => 9.1],
        'beak' => ['label' => 'Beak / Oral Cavity', 'x' => 17.4, 'y' => 15.7],
        'esophagus' => ['label' => 'Esophagus', 'x' => 27.7, 'y' => 21.4],
        'trachea' => ['label' => 'Trachea', 'x' => 28.4, 'y' => 27.6],
        'lungs' => ['label' => 'Lungs', 'x' => 39.6, 'y' => 35.8],
        'crop' => ['label' => 'Crop', 'x' => 28.3, 'y' => 47.8],
        'heart' => ['label' => 'Heart', 'x' => 34.6, 'y' => 45.2],
        'proventriculus' => ['label' => 'Proventriculus', 'x' => 38.0, 'y' => 52.7],
        'gizzard' => ['label' => 'Gizzard', 'x' => 45.6, 'y' => 54.3],
        'liver' => ['label' => 'Liver', 'x' => 41.9, 'y' => 62.5],
        'spleen' => ['label' => 'Spleen', 'x' => 51.5, 'y' => 59.8],
        'pancreas' => ['label' => 'Pancreas', 'x' => 59.6, 'y' => 69.6],
        'ovary' => ['label' => 'Ovary', 'x' => 54.4, 'y' => 36.8],
        'kidneys' => ['label' => 'Kidney', 'x' => 63.8, 'y' => 35.2],
        'oviduct' => ['label' => 'Oviduct', 'x' => 74.3, 'y' => 43.1],
        'cloaca' => ['label' => 'Cloaca', 'x' => 78.1, 'y' => 50.3],
        'large_intestine' => ['label' => 'Large Intestine', 'x' => 73.2, 'y' => 56.2],
        'intestines' => ['label' => 'Small Intestine', 'x' => 67.6, 'y' => 61.2],
        'ceca' => ['label' => 'Ceca', 'x' => 68.4, 'y' => 67.1],
    ];
@endphp

<div class="relative mx-auto w-full max-w-4xl select-none">
    <img src="{{ asset('images/poultry-body-map.jpg') }}" alt="{{ $label }}"
        class="block h-auto w-full rounded-base dark:opacity-90">

    @foreach ($hotspots as $canonical => $spot)
        @php
            $organ = $organs[$canonical] ?? null;
        @endphp

        @if ($organ !== null)
            @php
                $isSelected = $selectedOrgan === $canonical;
                $isDimmed = ! $isSelected && ($selectedOrgan !== '' || $selectedSystem !== '');
            @endphp

            <button type="button" class="group absolute -translate-x-1/2 -translate-y-1/2 cursor-pointer rounded-full border-2 border-white
                    before:absolute before:-inset-5 lg:before:-inset-7 before:content-[''] focus-visible:outline-none
                    lg:border-[3px] dark:border-slate-700"
                style="top: {{ $spot['y'] }}%; left: {{ $spot['x'] }}%; animation-delay: {{ round($loop->index * 0.12, 2) }}s"
                role="button" tabindex="0" aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                aria-label="{{ $organ['name'] }} — {{ $organ['diseases'] }}"
                wire:key="organ-{{ $canonical }}" wire:click="selectOrgan('{{ $canonical }}')"
                wire:keydown.enter.prevent="selectOrgan('{{ $canonical }}')"
                wire:keydown.space.prevent="selectOrgan('{{ $canonical }}')"
                @class([
                    // Bigger than a touch target strictly needs to be, because the
                    // dot is not on a plain background: it sits on a busy pink
                    // illustration and has to be found before it is aimed at. The
                    // steps keep it in proportion as the artwork scales down.
                    'h-7 w-7 sm:h-8 sm:w-8 lg:h-9 lg:w-9 transition duration-200 hover:scale-110 hover:bg-success focus-visible:scale-110',
                    // A ring rather than a fill change on selection: nineteen
                    // markers already overlap the artwork, and a ring reads on top
                    // of a label where a recoloured dot does not.
                    'bg-success ring-4 ring-brand/60 ring-offset-2 ring-offset-white dark:ring-offset-slate-800' => $isSelected,
                    // The faint white ring is what lifts an unselected marker off
                    // the artwork. Without it the dots sink into the organ.
                    'bg-danger shadow-md ring-2 ring-white/50 dark:ring-white/25' => ! $isSelected,
                    'opacity-25' => $isDimmed,
                    'hover:opacity-100 focus-visible:opacity-100' => $isDimmed,
                    // The ping is what says "these are clickable", so it only runs
                    // while nothing is chosen and stops competing with selection.
                    'after:absolute after:inset-0 after:rounded-full after:bg-danger/40 after:content-[""] after:animate-ping motion-reduce:after:hidden' => ! $isDimmed,
                ])>
                <span aria-hidden="true"
                    class="pointer-events-none absolute bottom-full left-1/2 mb-2 -translate-x-1/2 rounded-full bg-slate-900/90 px-2.5 py-1 text-xs font-semibold whitespace-nowrap text-white opacity-0 shadow-sm transition-opacity duration-150 group-hover:opacity-100 group-focus-visible:opacity-100 dark:bg-slate-700">
                    {{ $organ['name'] }} ({{ $organ['diseases'] }})
                </span>
            </button>
        @endif
    @endforeach
</div>