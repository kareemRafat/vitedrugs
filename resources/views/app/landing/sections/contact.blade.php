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
--}}
@php
    $channels = [
        [
            'tone' => 'indigo',
            'icon' => 'envelope',
            'title' => __('messages.landing.contact.channel1_title'),
            'body' => __('messages.landing.contact.channel1_desc'),
            'cta' => __('messages.landing.contact.channel1_cta'),
        ],
        [
            'tone' => 'sky',
            'icon' => 'building',
            'title' => __('messages.landing.contact.channel2_title'),
            'body' => __('messages.landing.contact.channel2_desc'),
            'cta' => __('messages.landing.contact.channel2_cta'),
        ],
    ];

    $channelIcons = [
        'envelope' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>',
        'building' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>',
    ];

    $channelTones = [
        'indigo' => 'bg-indigo-500/15 border-indigo-400/25 text-indigo-300',
        'sky' => 'bg-sky-500/15 border-sky-400/25 text-sky-300',
    ];
@endphp

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
        <div class="text-center max-w-3xl mx-auto mb-14 lg:mb-16 flex flex-col items-center">
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-stretch">
            <div class="lg:col-span-5 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    @foreach ($channels as $channel)
                        <div class="p-6 rounded-3xl bg-white/[0.06] backdrop-blur-md border border-white/10 hover:bg-white/[0.09] transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl border flex items-center justify-center shrink-0 {{ $channelTones[$channel['tone']] }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $channelIcons[$channel['icon']] !!}
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-1">{{ $channel['title'] }}</h3>
                                    <p class="text-xs text-slate-300 mb-2 leading-relaxed">{{ $channel['body'] }}</p>
                                    <a href="#contact-form" class="text-sm font-semibold text-indigo-300 hover:text-indigo-200 hover:underline">{{ $channel['cta'] }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Replaces the reference's "replies within 24 clinical hours" card,
                     which promised a service level this project does not operate. --}}
                <div class="p-6 rounded-3xl bg-gradient-to-br from-indigo-950/80 to-slate-900/80 backdrop-blur-md border border-white/10 shadow-lg shadow-black/30 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-mono font-bold tracking-wider text-sky-200 uppercase">{{ __('messages.landing.contact.triage_badge') }}</span>
                        </div>
                        <h4 class="text-lg font-bold text-white mb-1.5">{{ __('messages.landing.contact.triage_title') }}</h4>
                        <p class="text-xs sm:text-sm text-sky-100/90 leading-relaxed font-normal mb-5">
                            {{ __('messages.landing.contact.triage_desc') }}
                        </p>
                    </div>
                    <div class="pt-4 border-t border-white/15 flex flex-wrap items-center gap-2">
                        @foreach (['en' => 'English', 'ar' => 'العربية'] as $localeCode => $localeLabel)
                            <a href="{{ \LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                class="px-2.5 py-1 rounded-md bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-sm transition-colors">
                                {{ $localeLabel }}
                            </a>
                        @endforeach
                        <a href="{{ route('about') }}"
                            class="px-2.5 py-1 rounded-md bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-sm transition-colors">
                            {{ __('messages.landing.footer.link_about') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="rounded-3xl bg-slate-900/70 backdrop-blur-md p-8 sm:p-10 border border-white/10 shadow-2xl shadow-black/40 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-white mb-2 font-sans">
                            {{ __('messages.landing.contact.form_title') }}
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-400 mb-6">
                            {{ __('messages.landing.contact.form_subtitle') }}
                        </p>
                    </div>

                    <div id="contact-form">
                        @livewire('contact.contact-form', ['variant' => 'landing'])
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
