{{--
    Floating scroll-to-top control, landing page only.

    Mounted from app.layouts.landing INSIDE `.landing-scope`, so it wears the
    landing indigo palette. The layout's other fixed overlays - the toast and the
    cookie banner - are built from Flowbite tokens and therefore sit outside the
    scope; see the note at the top of that layout.

    `bottom-10 end-6` rather than a corner offset: the compare bar is a
    full-width `fixed bottom-0 z-50` strip and the cookie banner occupies
    `bottom-4` at the start side, so the control rides clear of both.
    `end` is logical, so it swaps sides in Arabic with no extra class.

    The off-state is baked into the markup so it cannot flash on first paint
    before the script has run; resources/js/back-to-top.js removes these classes.
--}}
<button type="button" id="backToTop"
    aria-label="{{ __('messages.common.back_to_top') }}"
    class="fixed bottom-10 end-6 z-[60] flex items-center justify-center w-12 h-12 rounded-full bg-[#1e4b7a] dark:bg-[#1e3a8a] hover:bg-[#163a5e] dark:hover:bg-[#172d6e] active:bg-[#112c47] dark:active:bg-[#132354] text-white shadow-lg shadow-[#0b1f38]/40 transition-all duration-300 opacity-0 translate-y-3 pointer-events-none focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950">
    <x-lucide-arrow-up class="w-5 h-5" />
</button>
