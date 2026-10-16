{{--
    The footer under a capped section of the body map panel.

    Included rather than written out four times: the wording, the chevron and the
    `wire:click` have to agree in all four places, and the one place that forgets
    is the one nobody tests. `anatomy.body-map` is the only caller, which is why
    `__()` is used directly instead of the component's `$t` helper.

    Every call site gates this on "are any rows actually hidden?" — the partial
    deliberately has no guard of its own. A `@if ($total > 0)` here reads like the
    thing that stops a short list from offering to expand, and it is not: it only
    checks the section is non-empty. Two of the four tiers rendered a "Show all 2"
    that way, over a list that was already showing both rows.

    `wire:target` carries the section name rather than just the method name. All
    four buttons call `toggleSection`, so a bare `wire:target="toggleSection"` puts
    a spinner on the other three every time one of them is clicked. Targeting by
    parameter is what keeps the spinner on the button that was pressed.

    Only the icon is swapped. The words stay put through the request because the
    label is about to invert — "Show all 38" becomes "Show fewer" — and replacing
    the text as well would make the button resize while it is being clicked.

    @param bool    $open   Whether the section is currently showing every row.
    @param int     $total  How many rows the section has in total.
    @param string  $key    The section name, as `toggleSection` validates it.
    @param bool    $plain  Outside a bordered tier, so no divider across the top.
--}}
@php $target = "toggleSection('{$key}')"; @endphp

<button type="button" wire:click="{{ $target }}"
    wire:loading.attr="disabled" wire:target="{{ $target }}"
    @class([
        'flex w-full items-center justify-center gap-1 py-2 text-xs font-medium text-fg-brand transition-colors duration-150 hover:bg-neutral-secondary-soft dark:hover:bg-slate-700 disabled:cursor-wait',
        'border-t border-brand/30' => ! ($plain ?? false),
        'border-t border-default-medium dark:border-slate-700' => (bool) ($plain ?? false),
    ])
    aria-expanded="{{ $open ? 'true' : 'false' }}">
    @if ($open)
        {{ __('poultry.body_map.show_less') }}
    @else
        {{ __('poultry.body_map.show_all', ['count' => $total]) }}
    @endif

    <span wire:loading.remove wire:target="{{ $target }}">
        @if ($open)
            <x-lucide-chevron-up class="w-3.5 h-3.5" />
        @else
            <x-lucide-chevron-down class="w-3.5 h-3.5" />
        @endif
    </span>

    <span wire:loading wire:target="{{ $target }}">
        @include('anatomy.partials.spinner')
    </span>
</button>
