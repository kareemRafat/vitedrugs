@props([
    // The window event that opens it. Namespaced per caller so two modals can
    // coexist on one page without either one reacting to the other's payload.
    'openEvent' => 'modal-open',
    'title' => '',
    'titleId' => 'modal-title',
    // Required, not defaulted. This component is shared and a shared component
    // that reaches into `poultry.*` for its own close button reads in the wrong
    // language the moment anybody mounts it on another part. The caller owns the
    // string, exactly as it owns the event name.
    'label',
])

{{--
    A reusable dialog: the caller opens it by dispatching a window event and the
    body reads the payload off `detail`.

        <button x-on:click="$dispatch('atlas-diseases', { title: '…', diseases: [ … ] })">
        <x-modal open-event="atlas-diseases" label="Close">
            <template x-for="row in detail.rows"><span x-text="row.label"></span></template>
        </x-modal>

    **The payload travels with the click rather than being fetched afterwards.**
    That is the whole reason this is Alpine and not a Livewire property: the caller
    is holding the rows already, so a round trip to re-read them would cost a query
    and a spinner to return data the page is sitting on. `detail` is null until
    something opens it, so the body must guard (`detail?.rows ?? []`).

    **Focus is trapped by hand.** `@alpinejs/focus` would give this for free and is
    not installed, and adding a dependency is not this component's call to make. So
    the panel takes focus on open, Tab cycles within it, and focus returns to
    whatever opened it on close — the three things that matter, without the package.

    **The `x-data` attribute is single-quoted, and that is load-bearing.**
    HTML has no escaping inside an attribute value: a backslash means nothing to the
    parser, so the `\"` in the focus-trap selector below ends a double-quoted
    attribute at that quote. Alpine then evaluates a truncated object literal, and
    every method in it silently fails to exist — the dialog renders, the close
    button logs `hide is not defined`, and nothing opens. Single quotes let the JS
    use `"` freely, which is what makes the selector writable at all.

    For the same reason there is no `@js()` in that expression. `Js::from()` wraps
    its output in single quotes, so it would close the attribute. The one value it
    carried — the fallback title — is a `data-` attribute instead, which is what a
    caller-authored string belongs in anyway, and it is omitted when empty rather
    than rendered as `title=""`.

    Everything is sized in logical properties (`ms-`, `pe-`, `text-start`) so the
    Arabic page mirrors without a second set of `dark:`/`rtl:` overrides.
--}}
<div @if ($title !== '')data-modal-title="{{ $title }}" @endif
     x-data='{
        open: false,
        title: "",
        detail: null,
        opener: null,
        init() {
            // Self-heal the scroll lock. `hide()` is the tidy path, but a Livewire
            // re-render can replace this element out from under an open dialog, and
            // the `overflow-hidden` it put on <body> would outlive it — leaving the
            // whole page unscrollable with no way back. An instance that boots while
            // an earlier one still holds the lock takes ownership of it instead, so
            // the lock means "a dialog is open right now" rather than something that
            // can be orphaned.
            document.body.classList.remove("overflow-hidden");
        },
        show(payload) {
            this.opener = document.activeElement;
            this.detail = payload ?? null;
            this.title = payload?.title || this.$el.dataset.modalTitle || "";
            this.open = true;
            document.body.classList.add("overflow-hidden");
            this.$nextTick(() => this.$refs.panel?.focus());
        },
        hide() {
            this.open = false;
            this.detail = null;
            document.body.classList.remove("overflow-hidden");
            this.opener?.focus?.();
            this.opener = null;
        },
        cycle(event) {
            const items = [...this.$refs.panel.querySelectorAll(
                "a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex=\"-1\"])"
            )];

            if (items.length === 0) {
                return;
            }

            const first = items[0];
            const last = items[items.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        },
    }'
     x-on:{{ $openEvent }}.window="show($event.detail)"
     x-on:keydown.escape.window="if (open) hide()"
     x-on:keydown.tab="if (open) cycle($event)">

    {{-- The overlay is the backdrop and the hit target. It is its own element so
         clicking the panel never bubbles out and closes the dialog under the
         reader's cursor. --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         role="dialog" aria-modal="true" aria-labelledby="{{ $titleId }}">
        <div class="absolute inset-0 bg-gray-900/60 dark:bg-black/70" x-on:click="hide()" aria-hidden="true"></div>

        <div x-ref="panel" tabindex="-1"
             class="relative flex max-h-[85vh] w-full max-w-md flex-col overflow-hidden rounded-base border border-default-medium bg-neutral-primary-soft shadow-lg focus:outline-none dark:border-slate-600 dark:bg-slate-800">

            <div class="flex items-start justify-between gap-3 border-b border-default-medium px-4 py-3 dark:border-slate-700">
                <h2 id="{{ $titleId }}" class="text-sm font-bold text-heading dark:text-white" x-text="title"></h2>
                <button type="button" x-on:click="hide()"
                        class="inline-flex shrink-0 cursor-pointer rounded-xs p-1 text-body transition-colors duration-150 hover:bg-neutral-secondary-soft hover:text-heading dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white"
                        aria-label="{{ $label }}">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- The scroll lives on the body, never on the panel. A dialog whose
                 frame scrolls takes the header away from the reader and lets the
                 close button leave the screen. --}}
            <div class="overflow-y-auto px-4 py-3">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>