@extends('app.layouts.master')

@section('title', __('poultry.landing.heading'))

@section('meta_description', __('poultry.landing.subtitle'))

@section('content')
    @php
        // Only sections that exist today. A card appears here the phase that
        // builds it lands, so the grid never links to a 404.
        $serviceRoutes = [
            'diagnosis' => route('poultry.diagnosis'),
            'diseases' => route('poultry.diseases.index'),
            'filter' => route('poultry.filter'),
            'search' => route('poultry.search'),
            'compare' => route('poultry.comparison'),
            'products' => route('poultry.products.index'),
            'body_map' => route('poultry.body-map'),
            'atlas' => route('poultry.atlas'),
        ];

        $serviceIcons = [
            'diagnosis' => 'stethoscope',
            'diseases' => 'bug',
            'filter' => 'sliders-horizontal',
            'search' => 'search',
            'compare' => 'columns-3',
            'products' => 'pill',
            'body_map' => 'scan-search',
            'atlas' => 'book-open-text',
        ];

        $statIcons = [
            'diseases' => 'bug',
            'findings' => 'activity',
            'lesions' => 'scan-search',
            'species' => 'egg',
        ];
    @endphp

    <div class="space-y-6">

        {{-- Hero --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-brand to-brand-strong dark:from-brand-subtle dark:to-brand-soft rounded-base">
            <div class="absolute inset-0 opacity-10 dark:opacity-20 pointer-events-none">
                <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-white dark:bg-brand-soft"></div>
                <div class="absolute -bottom-32 -start-32 w-80 h-80 rounded-full bg-white dark:bg-brand-soft"></div>
            </div>
            <div class="relative px-6 py-14 sm:py-20 max-w-3xl mx-auto text-center">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/15 backdrop-blur-sm rounded-full text-xs font-semibold uppercase tracking-widest text-white border border-white/20 mb-5">
                    <x-lucide-egg class="w-4 h-4" />
                    {{ __('poultry.landing.badge') }}
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                    {{ __('poultry.landing.heading') }}
                </h1>
                <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed">
                    {{ __('poultry.landing.subtitle') }}
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('poultry.diagnosis') }}" wire:navigate
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 text-sm font-bold text-brand-strong bg-white rounded-base shadow-lg shadow-black/10 hover:bg-white/90 transition-all active:scale-[0.98]">
                        <x-lucide-stethoscope class="w-4 h-4" />
                        {{ __('poultry.landing.cta_primary') }}
                    </a>
                    <a href="{{ route('poultry.filter') }}" wire:navigate
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 text-sm font-semibold text-white border border-white/40 rounded-base hover:bg-white/10 transition-colors">
                        {{ __('poultry.landing.cta_secondary') }}
                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                    </a>
                </div>
            </div>
        </section>

        {{-- Stats band --}}
        <section aria-label="{{ __('poultry.landing.stats_heading') }}">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach ($stats as $key => $value)
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium p-4 text-center dark:bg-slate-800 dark:border-slate-700">
                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand mb-2">
                            <x-dynamic-component :component="'lucide-' . $statIcons[$key]" class="w-4 h-4" />
                        </div>
                        <p class="text-2xl font-extrabold text-heading dark:text-white tabular-nums leading-none">{{ number_format($value) }}</p>
                        <p class="mt-1.5 text-xs font-medium text-body dark:text-slate-400">{{ __('poultry.landing.stats.' . $key) }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Services grid --}}
        <section class="pt-2">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white tracking-tight">{{ __('poultry.landing.services_heading') }}</h2>
                <p class="mt-2 text-sm sm:text-base text-body dark:text-slate-400">{{ __('poultry.landing.services_subtitle') }}</p>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($serviceRoutes as $key => $url)
                    <a href="{{ $url }}" wire:navigate
                        class="group flex flex-col bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium hover:border-brand hover:shadow-md transition-all duration-300 dark:bg-slate-800 dark:border-slate-700 dark:hover:border-brand p-5">
                        <div class="w-11 h-11 rounded-xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                            <x-dynamic-component :component="'lucide-' . $serviceIcons[$key]" class="w-5 h-5" />
                        </div>
                        <h3 class="text-base font-bold text-heading dark:text-white">{{ __('poultry.landing.services.' . $key . '.title') }}</h3>
                        <p class="mt-1.5 text-sm text-body dark:text-slate-400 leading-relaxed">{{ __('poultry.landing.services.' . $key . '.description') }}</p>
                        <span class="mt-auto pt-4 inline-flex items-center gap-1 text-sm font-semibold text-fg-brand dark:text-brand group-hover:gap-2 transition-all">
                            {{ __('poultry.landing.open') }}
                            <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- The tools used to live under /large-animals and link out of this
                 page. They are poultry-only now, so the old "shared with Large
                 Animals" notice that stood here is gone. --}}
        </section>

        {{-- CTA strip --}}
        <section class="pb-2">
            <a href="{{ route('poultry.diagnosis') }}" wire:navigate
                class="group block relative overflow-hidden bg-gradient-to-r from-brand to-brand-strong dark:from-brand-subtle dark:to-brand-soft rounded-base">
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <div class="absolute -top-16 -end-16 w-64 h-64 rounded-full bg-white"></div>
                </div>
                <div class="relative px-6 py-8 sm:px-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-white">{{ __('poultry.landing.cta_title') }}</h2>
                        <p class="mt-1 text-sm text-white/85">{{ __('poultry.landing.cta_subtitle') }}</p>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-brand-strong bg-white rounded-base hover:bg-white/90 transition-all active:scale-[0.98]">
                        {{ __('poultry.landing.cta_button') }}
                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform" />
                    </span>
                </div>
            </a>
        </section>

    </div>
@endsection
