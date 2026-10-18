{{--
    Contact band. `id="contact"` is the CTA band's second target.

    The reference offered `clinical@` and `partners@` mailboxes, named Boston /
    London / Riyadh offices, promised a reply "within 24 clinical hours" and
    described ISO/HL7 encrypted transmission. None of those exist in this
    project, so they are replaced by what is real: the form stores the message
    in the database and it appears in the staff inbox.

    The map texture is drawn in CSS rather than loaded as an image — the
    reference pointed at a remote CDN photograph that cannot be reproduced.
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
        'indigo' => 'bg-indigo-50 dark:bg-indigo-950/60 border-indigo-100 dark:border-indigo-900/40 text-indigo-600 dark:text-indigo-400',
        'sky' => 'bg-sky-50 dark:bg-sky-950/60 border-sky-100 dark:border-sky-900/40 text-sky-600 dark:text-sky-400',
    ];
@endphp

<section class="py-20 lg:py-28 bg-[#f4f6f9] dark:bg-slate-950 text-slate-900 dark:text-white relative border-t border-slate-200/80 dark:border-slate-800/80 overflow-hidden scroll-mt-0" id="contact">
    {{-- CSS-only cartographic texture: a fine grid plus a soft radial wash. --}}
    <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.35] dark:opacity-[0.15] overflow-hidden"
        style="background-image:linear-gradient(to right,#94a3b8 1px,transparent 1px),linear-gradient(to bottom,#94a3b8 1px,transparent 1px);background-size:64px 64px;mask-image:radial-gradient(ellipse at 50% 40%,#000 30%,transparent 75%);-webkit-mask-image:radial-gradient(ellipse at 50% 40%,#000 30%,transparent 75%)">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-14 lg:mb-16 flex flex-col items-center">
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="text-indigo-600 dark:text-indigo-400 font-mono font-bold text-xs sm:text-sm tracking-wider">/ 05</span>
                <span class="text-xs font-bold tracking-widest text-slate-500 dark:text-slate-400 uppercase font-mono">{{ __('messages.landing.contact.eyebrow') }}</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.12] mb-4 font-sans">
                {{ __('messages.landing.contact.heading') }}
            </h2>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl font-normal">
                {{ __('messages.landing.contact.subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-stretch">
            <div class="lg:col-span-5 flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    @foreach ($channels as $channel)
                        <div class="p-6 rounded-3xl bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200/90 dark:border-slate-800 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl border flex items-center justify-center shrink-0 {{ $channelTones[$channel['tone']] }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        {!! $channelIcons[$channel['icon']] !!}
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">{{ $channel['title'] }}</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2 leading-relaxed">{{ $channel['body'] }}</p>
                                    <a href="#contact-form" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">{{ $channel['cta'] }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Replaces the reference's "replies within 24 clinical hours" card,
                     which promised a service level this project does not operate. --}}
                <div class="p-6 rounded-3xl bg-[#1e4b7a] dark:bg-[#1e3a8a] text-white shadow-lg shadow-indigo-950/10 flex flex-col justify-between">
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
                <div class="rounded-3xl bg-white dark:bg-slate-900 p-8 sm:p-10 border border-slate-200/90 dark:border-slate-800 shadow-xl dark:shadow-2xl h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2 font-sans">
                            {{ __('messages.landing.contact.form_title') }}
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6">
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