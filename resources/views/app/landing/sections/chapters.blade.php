{{--
    Three-part hub. `id="browse-hub"` is what the hero's primary CTA targets.

    Each row links to a real, distinct part of the platform rather than an anchor.
--}}
@php
    // Intrinsic pixel dimensions per slot, so the browser reserves layout space
    // before the photograph arrives.
    $chapterImages = [
        'chapter-large-animals' => [1600, 1071],
        'chapter-drugs' => [1067, 1600],
        'chapter-poultry' => [1600, 1067],
    ];

    $chapters = [
        [
            'route' => 'large-animals.home',
            'image' => 'chapter-large-animals',
            'image_alt' => __('messages.landing.chapters.item1_image_alt'),
            'title' => __('messages.landing.chapters.item1_title'),
            'body' => __('messages.landing.chapters.item1_desc'),
            'link' => __('messages.landing.chapters.item1_cta'),
        ],
        [
            'route' => 'drugs.home',
            'image' => 'chapter-drugs',
            'image_alt' => __('messages.landing.chapters.item2_image_alt'),
            'title' => __('messages.landing.chapters.item2_title'),
            'body' => __('messages.landing.chapters.item2_desc'),
            'link' => __('messages.landing.chapters.item2_cta'),
        ],
        [
            'route' => 'poultry.home',
            'image' => 'chapter-poultry',
            'image_alt' => __('messages.landing.chapters.item3_image_alt'),
            'title' => __('messages.landing.chapters.item3_title'),
            'body' => __('messages.landing.chapters.item3_desc'),
            'link' => __('messages.landing.chapters.item3_cta'),
        ],
    ];
@endphp

<section class="py-20 lg:py-28 bg-slate-900 relative overflow-hidden text-white scroll-mt-0" id="browse-hub">
    <div class="absolute inset-0 z-0 pointer-events-none opacity-40">
        <div class="absolute top-[-10%] start-[-5%] w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl animate-orb-1"></div>
        <div class="absolute bottom-[-15%] end-[-5%] w-[28rem] h-[28rem] bg-sky-500/15 rounded-full blur-3xl animate-orb-2"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-14 lg:mb-16 flex flex-col items-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="text-amber-400 font-mono font-bold text-sm tracking-wider">/ 02</span>
                <span class="text-xs font-bold tracking-widest text-slate-400 uppercase">{{ __('messages.landing.chapters.eyebrow') }}</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.08] mb-4 font-sans">
                {{ __('messages.landing.chapters.heading') }}
            </h2>
            <p class="text-sm sm:text-base text-slate-400 leading-relaxed max-w-2xl mb-8">
                {{ __('messages.landing.chapters.subtitle') }}
            </p>
            <a href="{{ route('drugs.home') }}"
                class="inline-flex items-center justify-between gap-3 px-5 py-3 border border-slate-700/80 bg-slate-900/60 text-xs font-bold tracking-wider uppercase text-slate-200 hover:bg-slate-800 hover:text-white transition-all rounded shadow-sm">
                <span>{{ __('messages.landing.chapters.cta_all') }}</span>
                <span class="text-sm leading-none rtl:rotate-180">&nearr;</span>
            </a>
        </div>

        <div class="relative bg-slate-950 rounded-2xl md:rounded-3xl border border-slate-800/80 overflow-hidden shadow-2xl">
            <div class="hidden md:block absolute start-8 top-0 bottom-0 w-px bg-slate-800 z-20 pointer-events-none"></div>

            <div class="divide-y divide-slate-800/80">
                @foreach ($chapters as $chapter)
                    @php [$imgW, $imgH] = $chapterImages[$chapter['image']]; @endphp
                    <article class="group relative flex flex-col md:flex-row items-stretch justify-between min-h-[220px] lg:min-h-[250px] overflow-hidden bg-slate-950 transition-all duration-500 hover:bg-slate-900/60">
                        <div class="absolute inset-0 z-0 overflow-hidden">
                            <picture>
                                <source srcset="{{ asset("images/landing/{$chapter['image']}.webp") }}" type="image/webp">
                                <img src="{{ asset("images/landing/{$chapter['image']}.jpg") }}"
                                    alt="{{ $chapter['image_alt'] }}" width="{{ $imgW }}" height="{{ $imgH }}"
                                    loading="lazy" decoding="async"
                                    class="w-full h-full object-cover object-center filter brightness-[0.85] contrast-[1.1] transition-transform duration-700 ease-out group-hover:scale-105 group-hover:brightness-95">
                            </picture>
                            <div class="absolute inset-0 bg-gradient-to-r rtl:bg-gradient-to-l from-slate-950/85 via-slate-950/40 to-transparent"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent md:hidden"></div>
                        </div>

                        <div class="relative z-10 flex items-start gap-5 lg:gap-8 p-6 sm:p-8 lg:p-10 flex-1 max-w-2xl">
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-bold tracking-widest text-amber-400 uppercase mb-2">/ 0{{ $loop->iteration }}</span>
                                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white uppercase mb-2 font-sans">{{ $chapter['title'] }}</h3>
                                <p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed max-w-md mb-4">{{ $chapter['body'] }}</p>
                                <a href="{{ route($chapter['route']) }}"
                                    class="inline-flex items-center gap-2 text-xs font-bold tracking-wider uppercase text-amber-400 hover:text-white transition-colors">
                                    <span>{{ $chapter['link'] }}</span>
                                    <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M5 12h14m0 0-6-6m6 6-6 6" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>