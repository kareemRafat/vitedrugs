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

    In Arabic a separate photograph is served: `ARVETPEDIA RTL.png` is a
    redrawn asset, not the Latin one flipped in CSS. It puts the animals and the
    wordmark on the start side with clear sky opposite, so the right-aligned copy
    lands on empty sky — the Latin file flipped with `scaleX(-1)` would have
    mirrored the baked-in wordmark into unreadable text.

    Both files are now WebP + JPEG pairs at the same 1717x916, both encoded with
    the same settings as the Latin pair. The Arabic file arrived as a lossless
    PNG — 10.66 bits per pixel against the Latin JPEG's 0.92, which made it 2 MB
    for the same frame and 40% of the whole landing image folder. Re-encoding it
    to WebP brought it to 138 KB at an SSIM of 0.984, imperceptible on this
    image, and the `<picture>` no longer has to special-case the direction.

    Both files are 1717x916 and both keep the animals and the baked-in wordmark
    on the same side, with open dark field opposite. That is the whole reason
    `object-position` is set per direction below `lg`: a portrait phone box
    crops a 16:9 frame to its middle ~30% of the width, and the middle is
    exactly where the animals are — the copy landed on a buffalo's head, and
    with no scrim there was nothing underneath it to carry the contrast.
    Anchoring to the open side puts the copy back on empty dark sky, which is
    what it was composed for. `lg:object-center` restores the full frame on
    desktop, where the crop was already wide enough to show almost all of it.

    The heading is stacked over three lines via a `|` separator carried in the
    translation, so a translator controls the line composition instead of the
    template hard-coding English word order.
--}}
@php
    $heroIsRtl = app()->getLocale() === 'ar';

    // Both directions carry a full WebP + JPEG pair now. The Arabic file used to
    // be a 2 MB lossless PNG with no WebP counterpart, which is the only reason
    // the <source> below had to be conditional; it no longer does.
    $heroWebpFile = $heroIsRtl ? 'ARVETPEDIA RTL.webp' : 'hero.webp';
    $heroJpegFile = $heroIsRtl ? 'ARVETPEDIA RTL.jpg' : 'hero.jpg';

    // `asset()` does not encode spaces in the path, and the Arabic filenames
    // have one; browsers tolerate it, but a percent-encoded URL is correct.
    $heroWebp = str_replace(' ', '%20', asset("images/landing/{$heroWebpFile}"));
    $heroSrc = str_replace(' ', '%20', asset("images/landing/{$heroJpegFile}"));
@endphp

{{-- `pt-28` clears the floating pill, which sits between top-4 and top-16. --}}
<section class="relative pt-28 pb-16 lg:pt-32 lg:pb-24 overflow-hidden bg-slate-950 text-white min-h-svh flex items-center">
    <div class="absolute inset-0 z-0">
        <picture>
            <source srcset="{{ $heroWebp }}" type="image/webp">
            <img src="{{ $heroSrc }}"
                alt="{{ __('messages.landing.hero.image_alt') }}"
                width="1717" height="916" fetchpriority="high" decoding="async"
                class="w-full h-full object-cover {{ $heroIsRtl ? 'object-right' : 'object-left' }} lg:object-center">
        </picture>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        <div class="max-w-3xl flex flex-col items-start text-start">

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.12] mb-4 font-sans">
                @foreach (explode('|', __('messages.landing.hero.heading')) as $headingLine)
                    <span class="block">{{ $headingLine }}</span>
                @endforeach
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
                    class="absolute inset-y-1.5 end-1.5 px-5 py-2 rounded-full text-sm font-semibold text-white bg-[#1e4b7a] dark:bg-[#1e3a8a] hover:bg-[#163a5e] dark:hover:bg-[#172d6e] active:bg-[#112c47] dark:active:bg-[#132354] transition shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    {{ __('messages.landing.hero.search_button') }}
                </button>
            </form>

            {{-- No trailing margin: the metric strip that used to follow is gone, and the
                 section centres its content vertically, so a bottom margin would
                 only push the whole block off-centre. --}}
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <a href="{{ route('drugs.home') }}"
                    class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-semibold text-white bg-[#1e4b7a] dark:bg-[#1e3a8a] hover:bg-[#163a5e] dark:hover:bg-[#172d6e] active:bg-[#112c47] dark:active:bg-[#132354] shadow-lg shadow-[#0b1f38]/40 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
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