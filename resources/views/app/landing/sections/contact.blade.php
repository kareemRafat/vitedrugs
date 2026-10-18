{{--
    Contact band. `id="contact"` is the CTA band's second target.

    The reference offered `clinical@` and `partners@` mailboxes, named Boston /
    London / Riyadh offices, promised a reply "within 24 clinical hours" and
    described ISO/HL7 encrypted transmission. None of those exist in this
    project, so they are replaced by what is real: the form stores the message
    in the database and it appears in the staff inbox.

    The band is permanently dark (`bg-slate-950`, no `dark:` variant), matching
    the `/03` browse hub: the theme toggle flips the page around it, not this
    band. It therefore renders identically in light and dark mode, and none of
    its children carry `dark:` variants.

    The texture is drawn in CSS rather than loaded as an image — the reference
    pointed at a remote CDN photograph that cannot be reproduced. It replaces
    the original hard 64px cartographic grid, which read as hard engineering
    chrome on a dark surface: a fine radial dot pattern plus two ambient glows,
    the same glow treatment the browse hub uses, left static so this band stays
    the calm counterpart to that section's motion.

    What the three cards went for: two "channel" cards and a triage card used to
    stack down the start side in a `lg:col-span-5`, leaving the form squeezed
    into `lg:col-span-7` — about 620px of actual input width on a 1280px
    canvas. Neither card linked anywhere useful: both channels just jumped to
    `#contact-form`, which is the form beside them, and the triage card repeated
    the language switcher and About link the footer already carries.

    So the band is now one object. The form takes the full measure instead, at
    `max-w-5xl` rather than the full `max-w-7xl` — pushing it to 1280px would
    make each paired input ~590px wide, which reads as a broken form rather
    than a generous one. Every field below is now roughly 1.5x wider than it
    was.
--}}

{{-- Permanently dark band: `bg-slate-950` with no `dark:` variant, so it reads the same in both themes. --}}
<section class="py-20 lg:py-28 bg-slate-950 text-white relative border-t border-white/10 overflow-hidden scroll-mt-0" id="contact">
    {{-- Ambient texture: a fine dot field over two soft brand glows, faded out at the edges. --}}
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute inset-0 opacity-40"
            style="background-image:radial-gradient(circle,rgba(148,163,184,.30) 1px,transparent 1px);background-size:26px 26px;mask-image:radial-gradient(ellipse at 50% 35%,#000 25%,transparent 72%);-webkit-mask-image:radial-gradient(ellipse at 50% 35%,#000 25%,transparent 72%)">
        </div>
        <div class="absolute top-[-10%] start-[-8%] w-[32rem] h-[32rem] bg-indigo-600/25 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-15%] end-[-8%] w-[28rem] h-[28rem] bg-sky-500/20 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-12 lg:mb-14 flex flex-col items-center">
            <div class="flex items-end justify-center gap-2 mb-3">
                <span class="text-indigo-400 font-mono font-bold text-xs sm:text-sm tracking-wider">/ 04</span>
                <span class="text-xs font-bold tracking-widest text-slate-400 uppercase font-mono">{{ __('messages.landing.contact.eyebrow') }}</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.12] mb-4 font-sans">
                {{ __('messages.landing.contact.heading') }}
            </h2>
            <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl font-normal">
                {{ __('messages.landing.contact.subtitle') }}
            </p>
        </div>

        {{--
            One card, centred, at the width a form should actually be read at.
            The heading moved inside it: with the side column gone there is
            nothing to balance it against, and a form with its own header reads
            as a single object rather than as a panel bolted next to copy.
        --}}
        <div class="max-w-5xl mx-auto">
            <div class="rounded-3xl bg-slate-900/70 backdrop-blur-md border border-white/10 shadow-2xl shadow-black/40 overflow-hidden">
                {{-- Header strip. The green pulse is the one live signal on the
                     page: this form really does reach the staff inbox. --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5 px-8 sm:px-10 py-7 bg-white/[0.03] border-b border-white/10">
                    <span class="shrink-0 inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-500/15 border border-indigo-400/25 text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>
                    </span>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-xl sm:text-2xl font-bold text-white font-sans">
                                {{ __('messages.landing.contact.form_title') }}
                            </h3>
                        </div>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            {{ __('messages.landing.contact.form_subtitle') }}
                        </p>
                    </div>
                </div>

                <div id="contact-form" class="px-8 sm:px-10 py-8 sm:py-10">
                    @livewire('contact.contact-form', ['variant' => 'landing'])
                </div>
            </div>
        </div>
    </div>
</section>