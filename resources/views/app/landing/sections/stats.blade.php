{{--
    Database-in-numbers band. `id="database-stats"` is what the header nav links to.

    Every figure here is computed by HomeController from live rows. Nothing is
    hardcoded, and the "+N this month" badge is omitted entirely when the real
    30-day delta is zero — an invented growth number is worse than no number.

    Layout follows the reference: one prominent card spanning 4 of 12 columns,
    with a 2x2 grid beside it. The reference's second card was a "peer-review
    score" of 9.4/10, which no data in this project supports; that slot is now
    the manufacturer and supplier count.
--}}
@php
    // Every counted entity, in the order the total is summed. Rendered as the
    // prominent card's breakdown so the headline figure is auditable rather than
    // a bare number.
    $breakdown = [
        ['key' => 'products', 'label' => __('messages.landing.stats.products_short'), 'value' => $counts['products']],
        ['key' => 'diseases', 'label' => __('messages.landing.stats.diseases_short'), 'value' => $counts['diseases']],
        ['key' => 'ingredients', 'label' => __('messages.landing.stats.ingredients_short'), 'value' => $counts['ingredients']],
        ['key' => 'companies', 'label' => __('messages.landing.stats.companies_short'), 'value' => $counts['companies']],
        ['key' => 'dosage-forms', 'label' => __('messages.landing.stats.dosage_forms_short'), 'value' => $counts['dosageForms']],
        ['key' => 'species', 'label' => __('messages.landing.stats.species_short'), 'value' => $counts['species']],
    ];

    $primaryStat = [
        'label' => __('messages.landing.stats.total_records'),
        'value' => $totalRecords,
        'body' => __('messages.landing.stats.total_records_desc'),
    ];

    $gridStats = [
        [
            'key' => 'diseases',
            'label' => __('messages.landing.stats.diseases'),
            'value' => $counts['diseases'],
            'recent' => $recent['diseases'],
            'body' => __('messages.landing.stats.diseases_desc'),
        ],
        [
            'key' => 'ingredients',
            'label' => __('messages.landing.stats.ingredients'),
            'value' => $counts['ingredients'],
            'recent' => $recent['ingredients'],
            'body' => __('messages.landing.stats.ingredients_desc'),
        ],
        [
            'key' => 'products',
            'label' => __('messages.landing.stats.products'),
            'value' => $counts['products'],
            'recent' => $recent['products'],
            'body' => __('messages.landing.stats.products_desc'),
        ],
        [
            'key' => 'companies',
            'label' => __('messages.landing.stats.companies'),
            'value' => $counts['companies'],
            'recent' => $recent['companies'],
            'body' => __('messages.landing.stats.companies_desc'),
        ],
    ];
@endphp

<section class="py-20 lg:py-28 bg-[#f4f6f9] dark:bg-slate-950 text-slate-900 dark:text-white relative border-y border-slate-200/80 dark:border-slate-800/80 scroll-mt-0" id="database-stats">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mb-12 sm:mb-14">
            <div class="flex items-center gap-2 mb-3 text-slate-500 dark:text-slate-400 font-mono text-xs sm:text-sm font-semibold tracking-wider uppercase">
                <span class="rtl:rotate-180" aria-hidden="true">&rarr;</span>
                <span>{{ __('messages.landing.stats.eyebrow') }}</span>
            </div>
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.08] font-sans mb-4">
                {{ __('messages.landing.stats.heading') }}
            </h2>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl font-normal">
                {{ __('messages.landing.stats.subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            <div class="lg:col-span-4 flex">
                <div class="w-full rounded-3xl bg-[#1e4b7a] dark:bg-[#1e3a8a] text-white p-7 sm:p-8 flex flex-col justify-between shadow-lg shadow-indigo-950/10 border border-transparent">
                    <div>
                        <p class="text-xs font-mono font-bold tracking-widest text-sky-200 uppercase mb-3 flex items-center gap-1.5">
                            <span class="rtl:rotate-180" aria-hidden="true">&rarr;</span>
                            {{ __('messages.landing.stats.primary_badge') }}
                        </p>
                        <h3 class="text-xl sm:text-2xl font-bold text-white mb-4">
                            {{ $primaryStat['label'] }}
                        </h3>
                        <div class="text-6xl sm:text-7xl font-extrabold tracking-tight text-white mb-4 font-sans tabular-nums"
                            data-entity="total-records" data-count="{{ $primaryStat['value'] }}">
                            {{ number_format($primaryStat['value']) }}
                        </div>

                        {{-- The breakdown is the honest version of the reference's
                             "+12 pts vs legacy SPC" chip: it names exactly which
                             entities were summed, instead of a growth claim that
                             nothing in this project can support. --}}
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($breakdown as $entry)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-white/15 text-white text-[11px] font-semibold backdrop-blur-sm"
                                    data-entity="{{ $entry['key'] }}" data-count="{{ $entry['value'] }}">
                                    {{ $entry['label'] }}
                                    <span class="tabular-nums opacity-80">{{ number_format($entry['value']) }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="pt-8 mt-8 border-t border-white/15">
                        <p class="text-xs sm:text-sm text-sky-100/90 leading-relaxed font-normal">{{ $primaryStat['body'] }}</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach ($gridStats as $stat)
                    <div class="rounded-3xl bg-white dark:bg-slate-900 p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                        <div>
                            <p class="text-xs font-mono font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase mb-2">
                                {{ $stat['label'] }}
                            </p>
                            <div class="text-5xl sm:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3 font-sans tabular-nums"
                                data-entity="{{ $stat['key'] }}" data-count="{{ $stat['value'] }}">
                                {{ number_format($stat['value']) }}
                            </div>

                            {{-- Hidden when the real delta is zero: showing "+0" is noise,
                                 and inventing a figure would be a lie. --}}
                            @if ($stat['recent'] > 0)
                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 text-xs font-bold"
                                    data-recent-badge="{{ $stat['key'] }}" data-recent="{{ $stat['recent'] }}">
                                    <span aria-hidden="true">&uarr;</span>
                                    {{ __('messages.landing.stats.added_this_month', ['count' => $stat['recent']]) }}
                                </div>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-6 leading-relaxed">{{ $stat['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>