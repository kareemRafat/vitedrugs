{{--
    Four-pillar band. `id="about"` is kept because the header nav links to it.

    Markup follows the reference card-for-card. Copy is not: the reference's
    originals claimed a direct EMA/FDA CVM/SFDA regulatory pipeline and automated
    pharmacovigilance surveillance, neither of which exists here. Each pillar now
    names a real feature set or a real route.

    Two deliberate deviations from the reference, both narrow:

      * The reference sets `writing-mode: vertical-rl` inline. That is kept, via
        the `.vertical-label` class instead — identical in LTR, plus an RTL
        variant that flips it to `vertical-lr` so the captions read top-to-bottom
        in both directions. An inline style cannot do that.
      * `border-r` → `border-e`. Physical right/left borders put the card
        dividers on the wrong side in Arabic. Tailwind v4's logical properties
        preserve the reference's exact LTR output and behave correctly in RTL.
--}}
@php
    // Intrinsic pixel dimensions per slot, so the browser reserves layout space
    // before the photograph arrives. Kept next to the filenames rather than
    // hardcoded into the markup, so the two cannot drift apart.
    $pillarImages = [
        'pillar-structured-data' => [900, 600],
        'pillar-bilingual' => [900, 600],
        'pillar-decision-tools' => [506, 900],
        'pillar-surveillance' => [600, 900],
    ];

    $pillars = [
        [
            'index' => '01',
            'image' => 'pillar-structured-data',
            'image_alt' => __('messages.landing.pillars.item1_image_alt'),
            'title' => __('messages.landing.pillars.item1_title'),
            'body' => __('messages.landing.pillars.item1_desc'),
            'tag' => __('messages.landing.pillars.item1_tag'),

            // Divider edges. At sm the grid is 2-up, so only the first card in
            // each row carries the inner border; at lg it is 4-up and cards 1-3
            // all need one. The reference encodes exactly this.
            'border' => 'border-b sm:border-b-0 sm:border-e',

            'image_filter' => 'filter brightness-[0.7] dark:brightness-[0.6] contrast-[1.1]',
            'scrim' => 'bg-gradient-to-t from-slate-950 via-slate-950/75 to-slate-950/50',
            'accent' => false,
        ],
        [
            'index' => '02',
            'image' => 'pillar-bilingual',
            'image_alt' => __('messages.landing.pillars.item2_image_alt'),
            'title' => __('messages.landing.pillars.item2_title'),
            'body' => __('messages.landing.pillars.item2_desc'),
            'tag' => __('messages.landing.pillars.item2_tag'),
            'border' => 'border-b sm:border-b-0 lg:border-e',
            'image_filter' => 'filter brightness-[0.7] dark:brightness-[0.6] contrast-[1.1]',
            'scrim' => 'bg-gradient-to-t from-slate-950 via-slate-950/75 to-slate-950/50',
            'accent' => false,
        ],

        // The reference highlights card 3 with a full sky treatment. Kept.
        [
            'index' => '03',
            'image' => 'pillar-decision-tools',
            'image_alt' => __('messages.landing.pillars.item3_image_alt'),
            'title' => __('messages.landing.pillars.item3_title'),
            'body' => __('messages.landing.pillars.item3_desc'),
            'tag' => __('messages.landing.pillars.item3_tag'),
            'border' => 'border-b sm:border-b-0 sm:border-e',
            'image_filter' => 'filter brightness-[0.7] contrast-[1.15]',
            'scrim' => 'bg-gradient-to-t from-sky-950 via-sky-900/60 to-sky-800/40',
            'accent' => true,
        ],
        [
            'index' => '04',
            'image' => 'pillar-surveillance',
            'image_alt' => __('messages.landing.pillars.item4_image_alt'),
            'title' => __('messages.landing.pillars.item4_title'),
            'body' => __('messages.landing.pillars.item4_desc'),
            'tag' => __('messages.landing.pillars.item4_tag'),
            'border' => '',
            'image_filter' => 'filter brightness-[0.7] dark:brightness-[0.6] contrast-[1.1]',
            'scrim' => 'bg-gradient-to-t from-slate-950 via-slate-950/75 to-slate-950/50',
            'accent' => false,
        ],
    ];
@endphp

{{--
    scroll-mt-0: an anchor lands the section flush with the top of the page,
    because the navbar floats rather than occupying a band of its own.

    That is safe because the floating pill ends 72px down and this section has
    py-20 (80px) of top padding — the heading starts below the pill either way.
--}}
<section class="py-20 lg:py-28 bg-slate-50 dark:bg-slate-950 border-y border-slate-200/80 dark:border-slate-800/80 relative overflow-hidden text-slate-900 dark:text-white scroll-mt-0" id="about">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 mb-12 sm:mb-16">
        <div class="text-center max-w-3xl mx-auto flex flex-col items-center">
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="text-indigo-600 dark:text-indigo-400 font-mono font-bold text-xs sm:text-sm tracking-wider">/ 01</span>
                <span class="text-xs font-bold tracking-widest text-slate-500 dark:text-slate-400 uppercase font-mono self-center">{{ __('messages.landing.pillars.eyebrow') }}</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight font-sans mb-4">
                {{ __('messages.landing.pillars.heading') }}
            </h2>
            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 leading-relaxed max-w-2xl mx-auto font-normal">
                {{ __('messages.landing.pillars.subtitle') }}
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-xl dark:shadow-2xl bg-white dark:bg-slate-900 items-stretch">

            @foreach ($pillars as $pillar)
                @php
                    [$imgW, $imgH] = $pillarImages[$pillar['image']];

                    // The accent card swaps four colours over the neutral set.
                    $cardTone = $pillar['accent'] ? ' border-sky-500/40 bg-sky-950/40' : '';
                    $numberShadow = $pillar['accent'] ? 'drop-shadow-lg' : 'drop-shadow-md';
                    $bodyTone = $pillar['accent'] ? 'text-sky-100' : 'text-slate-200 dark:text-slate-300';
                    $tagTone = $pillar['accent'] ? 'text-sky-200' : 'text-slate-300 dark:text-slate-400';
                    $ruleTone = $pillar['accent'] ? 'bg-sky-300/80' : 'bg-slate-400/80 dark:bg-slate-600/80';
                @endphp

                <article class="relative group min-h-[520px] lg:min-h-[640px] flex flex-col justify-between p-8 sm:p-10 pt-16 sm:pt-20 border-slate-200/80 dark:border-slate-800/80 overflow-hidden {{ $pillar['border'] }}{{ $cardTone }}">

                    <div class="absolute inset-0 z-0">
                        <picture>
                            <source srcset="{{ asset("images/landing/{$pillar['image']}.webp") }}" type="image/webp">
                            <img src="{{ asset("images/landing/{$pillar['image']}.jpg") }}"
                                alt="{{ $pillar['image_alt'] }}" width="{{ $imgW }}" height="{{ $imgH }}"
                                loading="lazy" decoding="async"
                                class="w-full h-full object-cover object-center {{ $pillar['image_filter'] }} group-hover:scale-105 transition-transform duration-700">
                        </picture>

                        @if ($pillar['accent'])
                            <div class="absolute inset-0 bg-sky-600/75 mix-blend-multiply"></div>
                        @endif

                        <div class="absolute inset-0 {{ $pillar['scrim'] }}"></div>
                    </div>

                    <div class="relative z-10 text-center">
                        <span class="block text-6xl sm:text-7xl font-extrabold text-white tracking-tight mb-4 font-sans {{ $numberShadow }} leading-none tabular-nums">
                            {{ $pillar['index'] }}
                        </span>
                        <h3 class="text-lg font-bold text-white mb-2 uppercase tracking-wide">{{ $pillar['title'] }}</h3>
                        <p class="text-xs sm:text-sm {{ $bodyTone }} font-normal leading-relaxed max-w-xs mx-auto">{{ $pillar['body'] }}</p>
                    </div>

                    <div class="relative z-10 flex flex-col items-center pt-8 mt-auto">
                        <span class="vertical-label text-[11px] font-mono tracking-widest {{ $tagTone }} uppercase select-none mb-3">{{ $pillar['tag'] }}</span>
                        <div class="w-px h-12 {{ $ruleTone }}"></div>
                    </div>
                </article>
            @endforeach

        </div>
    </div>
</section>
