{{--
    Hero — full-bleed photograph at full viewport height.

    The search form posts to the real `search` route (`/drugs/search`) with a `q`
    parameter, which is exactly what SearchController reads, so the hero search
    is the site's own search rather than a decorative input.

    There is deliberately NO overlay on the photograph. The reference stacked
    three scrims over it (a flat multiply, a directional gradient and a vertical
    one); all three are gone, so the image is shown as shot. **Known trade-off:**
    the white copy now sits directly on the photo, so legibility depends on
    whatever is behind it at each viewport size. `min-h-svh` rather than
    `min-h-screen` is used for the same reason it is used on mobile — 100vh
    includes the collapsing URL bar, which would push the bottom of the section
    below the fold.
--}}

{{-- `pt-28` clears the floating pill, which sits between top-4 and top-16. --}}
<section class="relative pt-28 pb-16 lg:pt-32 lg:pb-24 overflow-hidden bg-slate-950 text-white min-h-svh flex items-center">
    <div class="absolute inset-0 z-0">
        <picture>
            <source srcset="{{ asset('images/landing/hero.webp') }}" type="image/webp">
            <img src="{{ asset('images/landing/hero.jpg') }}"
                alt="{{ __('messages.landing.hero.image_alt') }}"
                width="1717" height="916" fetchpriority="high" decoding="async"
                class="w-full h-full object-cover object-center">
        </picture>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="max-w-3xl flex flex-col items-start text-start">

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.12] mb-4 font-sans">
                {{ __('messages.landing.hero.heading') }}
            </h1>

            <p class="text-base sm:text-lg text-slate-200 leading-relaxed max-w-2xl mb-8 font-normal">
                {{ __('messages.landing.hero.subtitle') }}
            </p>

            {{-- The header's search button targets #hero-search-input; keep the id. --}}
            <form action="{{ route('search') }}" method="GET" class="w-full max-w-xl relative flex items-center mb-6">
                <div class="absolute inset-y-0 start-0 ps-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5 ms-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0z" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </div>
                <label for="hero-search-input" class="sr-only">{{ __('messages.landing.hero.search_placeholder') }}</label>
                <input id="hero-search-input" name="q" type="search"
                    placeholder="{{ __('messages.landing.hero.search_placeholder') }}"
                    class="w-full py-3.5 ps-12 pe-28 text-sm sm:text-base rounded-full bg-slate-900/90 backdrop-blur-md border border-slate-700/80 text-white placeholder:text-slate-400 shadow-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/30 transition-all">
                <button type="submit"
                    class="absolute inset-y-1.5 end-1.5 px-5 py-2 rounded-full text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 transition shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    {{ __('messages.landing.hero.search_button') }}
                </button>
            </form>

            {{-- No trailing margin: the metric strip that used to follow is gone, and the
                 section centres its content vertically, so a bottom margin would
                 only push the whole block off-centre. --}}
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <a href="{{ route('drugs.home') }}"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-lg shadow-indigo-600/30 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <span>{{ __('messages.landing.hero.cta_browse') }}</span>
                    <svg class="w-4 h-4 ms-2 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </a>
                <a href="{{ route('large-animals.diagnosis') }}"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold text-slate-100 bg-slate-900/80 hover:bg-slate-800/90 backdrop-blur-md border border-slate-700/80 shadow-sm transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <svg class="w-4 h-4 me-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span>{{ __('messages.landing.hero.cta_diagnose') }}</span>
                </a>
            </div>
        </div>
    </div>
</section>