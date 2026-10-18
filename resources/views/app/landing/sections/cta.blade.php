{{--
    Final CTA band. Gradient band with the texture photograph at low opacity.

    Copy is honest: it does not claim the data is free, complete or regulator
    approved. It says what the visitor can actually do next.
--}}
<section class="relative py-20 lg:py-24 bg-gradient-to-r rtl:bg-gradient-to-l from-[#1e4b7a] via-[#26628f] to-[#1e3a8a] dark:from-[#1e3a8a] dark:via-[#2563a8] dark:to-[#1e40af] text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10 mix-blend-overlay pointer-events-none">
        <picture>
            <source srcset="{{ asset('images/landing/cta-texture.webp') }}" type="image/webp">
            <img src="{{ asset('images/landing/cta-texture.jpg') }}" alt="" aria-hidden="true"
                width="1600" height="1280" loading="lazy" decoding="async"
                class="w-full h-full object-cover blur-sm">
        </picture>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4">
            {{ __('messages.landing.cta.heading') }}
        </h2>
        <p class="text-lg sm:text-xl text-indigo-100 max-w-2xl mx-auto mb-10">
            {{ __('messages.landing.cta.subtitle') }}
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('drugs.home') }}"
                class="px-8 py-4 rounded-xl text-base font-bold text-[#1e3a8a] bg-white hover:bg-slate-100 shadow-xl shadow-indigo-950/20 transition-all focus:outline-none focus:ring-2 focus:ring-white">
                {{ __('messages.landing.cta.browse') }}
            </a>
        </div>
    </div>
</section>
