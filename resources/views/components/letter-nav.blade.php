@props([
    // The letters this list actually has. A rail hardcoded to A–Z on a part with
    // 18 diseases is mostly dead keys, so callers pass the pool they derived.
    'letters' => [],
    'active' => null,
    // The Livewire action to call. Named per component because `wire:click` is
    // resolved against the component it renders inside, and two lists on one page
    // would otherwise drive each other.
    'wireAction' => 'selectLetter',
])

{{--
    The alphabet rail, shared by the lesion atlas and the disease list.

    Single-quoted `wire:click` on purpose: a letter is a bare `[A-Z]` character,
    but this component takes caller-provided tokens too, and a double-quoted
    attribute would break on the first one carrying a quote.

    `cursor-pointer` is explicit on the live letters rather than inherited. Tailwind
    v4's preflight resets `button` to `cursor: default` and never restores it, so
    a clickable letter is indistinguishable from a dead one without it — which is
    the whole signal a rail gives. The two `cursor-default` spans below are
    deliberate: the selected letter is not itself a control, and a letter with no
    diseases under it cannot be pressed.
--}}
<div class="bg-neutral-primary-soft shadow-xs p-4 rounded-base dark:bg-slate-800">
    <div class="flex flex-nowrap md:flex-wrap items-center justify-start md:justify-center gap-4 md:gap-2 overflow-x-auto md:overflow-visible pb-1">
        @php $allLetters = range('A', 'Z'); @endphp
        @foreach ($allLetters as $letter)
            @if (in_array($letter, $letters, true))
                @if ($active === $letter)
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-base text-brand dark:text-amber-400 text-base font-extrabold cursor-default">{{ $letter }}</span>
                @else
                    <button type="button" wire:click='{{ $wireAction }}("{{ $letter }}")'
                        class="inline-flex items-center justify-center w-9 h-9 rounded-base text-sm font-medium text-heading bg-neutral-secondary-soft max-md:bg-transparent dark:bg-transparent hover:bg-brand-soft hover:text-fg-brand dark:text-white dark:hover:text-brand transition-colors cursor-pointer">
                        {{ $letter }}
                    </button>
                @endif
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-base text-sm text-body/30 dark:text-slate-600 cursor-default" aria-disabled="true">{{ $letter }}</span>
            @endif
        @endforeach
    </div>
</div>