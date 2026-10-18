{{--
    Three-part hub. `id="browse-hub"` is what the hero's primary CTA targets.
    The three cards sit in an asymmetric 2x2: Large Animals and Poultry stack as
    two equal halves down the start side, and Vet Drugs takes the end column,
    spanning both rows so it matches their combined height exactly. The split is
    built from a flex column rather than `grid-rows-2` on purpose: with an
    auto-height container the `1fr` rows of a grid resolve against content, so
    the two halves could differ. `flex-1` on each child splits them evenly
    whatever the copy length, and the end column inherits the stretched height.
    Each row links to a real, distinct part of the platform rather than an anchor.
--}}
@php
    // Intrinsic pixel dimensions sit on each card rather than in a side table,
    // so the browser reserves layout space before the photograph arrives. Keeping
    // them off the component call also avoids passing an array lookup as an
    // attribute, which Blade mis-compiles.
    //
    // `index` is written out rather than derived from a loop counter, because the
    // cards are rendered from two separate columns and `$loop->iteration` would
    // restart in each of them.
    $chapterColumns = [
        'start' => [
            [
                'index' => '01',
                'route' => 'large-animals.home',
                'image' => 'chapter-large-animals',
                'width' => 1600,
                'height' => 1071,
                'image_alt' => __('messages.landing.chapters.item1_image_alt'),
                'title' => __('messages.landing.chapters.item1_title'),
                'body' => __('messages.landing.chapters.item1_desc'),
                'link' => __('messages.landing.chapters.item1_cta'),
            ],
            [
                'index' => '02',
                'route' => 'poultry.home',
                'image' => 'chapter-poultry',
                'width' => 1600,
                'height' => 1067,
                'image_alt' => __('messages.landing.chapters.item3_image_alt'),
                'title' => __('messages.landing.chapters.item3_title'),
                'body' => __('messages.landing.chapters.item3_desc'),
                'link' => __('messages.landing.chapters.item3_cta'),
            ],
        ],
        'end' => [
            [
                'index' => '03',
                'route' => 'drugs.home',
                'image' => 'chapter-drugs',
                'width' => 1067,
                'height' => 1600,
                'image_alt' => __('messages.landing.chapters.item2_image_alt'),
                'title' => __('messages.landing.chapters.item2_title'),
                'body' => __('messages.landing.chapters.item2_desc'),
                'link' => __('messages.landing.chapters.item2_cta'),
            ],
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
            <div class="flex items-end justify-center gap-2 mb-3">
                <span class="text-indigo-400 font-mono font-bold text-xs sm:text-sm tracking-wider">/ 02</span>
                <span class="text-xs font-bold tracking-widest text-slate-400 uppercase font-mono">{{ __('messages.landing.chapters.eyebrow') }}</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.08] mb-4 font-sans">
                {{ __('messages.landing.chapters.heading') }}
            </h2>
            <p class="text-sm sm:text-base text-slate-400 leading-relaxed max-w-2xl mb-8">
                {{ __('messages.landing.chapters.subtitle') }}
            </p>
        </div>
        {{-- `items-stretch` is the default, and the end column relies on it: the
             grid row height comes from the taller start column, and the card
             inside is `h-full` to match it. --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5">
            <div class="flex flex-col gap-4 lg:gap-5">
                @foreach ($chapterColumns['start'] as $chapter)
                    <x-chapter.chapter-card :chapter="$chapter" />
                @endforeach
            </div>

            <div>
                @foreach ($chapterColumns['end'] as $chapter)
                    <x-chapter.chapter-card :chapter="$chapter" sizing="h-full min-h-[300px] lg:min-h-0" />
                @endforeach
            </div>
        </div>
    </div>
</section>
