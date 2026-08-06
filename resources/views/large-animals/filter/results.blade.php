@extends('app.layouts.master')

@section('title', __('large-animals.filter.results_title'))

@section('content')
    <div class="space-y-4">
        <x-large-animals.page-hero
            :heading="__('large-animals.filter.results_heading')"
            :subtitle="__('large-animals.filter.results_subtitle')"
            :badge="__('large-animals.hero.badge.filter')"
            badgeIcon="sliders-horizontal"
        >
            <a href="{{ route('large-animals.filter') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white/15 text-white border border-white/30 rounded-base hover:bg-white/25"><x-lucide-rotate-ccw class="w-4 h-4" />{{ __('large-animals.filter.back') }}</a>
        </x-large-animals.page-hero>

        {{-- Criteria summary --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <h2 class="text-sm font-semibold text-heading dark:text-white mb-3">{{ __('large-animals.filter.selected_criteria') }}</h2>
            <div class="flex flex-wrap gap-2">
                @php
                    $appliedSpecies = $species->firstWhere('id', $criteria['host_species_id'] ?? null);
                    $appliedBodySystem = $bodySystems->firstWhere('id', $criteria['body_system_id'] ?? null);
                @endphp
                @if ($appliedSpecies)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-brand-soft text-fg-brand border border-brand-subtle dark:bg-brand/20 dark:text-brand dark:border-brand-subtle">
                        <x-lucide-paw-print class="w-3.5 h-3.5" />
                        {{ $appliedSpecies->display_name }}
                    </span>
                @endif
                @if (! empty($criteria['etiology_type']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-brand-soft text-fg-brand border border-brand-subtle dark:bg-brand/20 dark:text-brand dark:border-brand-subtle">
                        <x-lucide-bug class="w-3.5 h-3.5" />
                        {{ $criteria['etiology_type'] }}
                    </span>
                @endif
                @if ($appliedBodySystem)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-brand-soft text-fg-brand border border-brand-subtle dark:bg-brand/20 dark:text-brand dark:border-brand-subtle">
                        <x-lucide-stethoscope class="w-3.5 h-3.5" />
                        {{ $appliedBodySystem->localized_display_name }}
                    </span>
                @endif
                @if (! empty($criteria['zoonotic']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-danger-soft text-fg-danger-strong border border-danger-subtle dark:bg-danger/20 dark:text-red-400 dark:border-danger-subtle">
                        <x-lucide-biohazard class="w-3.5 h-3.5" />
                        {{ __('large-animals.filter.zoonotic_label') }}
                    </span>
                @endif
                @foreach ($clinicalSigns as $sign)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-brand-soft text-fg-brand border border-brand-subtle dark:bg-brand/20 dark:text-brand dark:border-brand-subtle">
                        <x-lucide-stethoscope class="w-3.5 h-3.5" />
                        {{ $sign->localized_display_name }}
                    </span>
                @endforeach
            </div>
        </div>

        @if ($diseases->isEmpty())
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-search-x class="w-12 h-12 text-body mx-auto mb-3" />
                <h2 class="text-lg font-semibold text-heading dark:text-white">{{ __('large-animals.filter.no_results') }}</h2>
                <p class="mt-1 text-sm text-body">{{ __('large-animals.filter.no_results_desc') }}</p>
                <a href="{{ route('large-animals.filter') }}" wire:navigate class="mt-6 inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors">
                    <x-lucide-arrow-left class="w-4 h-4" />{{ __('large-animals.filter.back') }}
                </a>
            </div>
        @else
            {{-- Summary --}}
            <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.filter.results_summary', ['count' => $diseases->count()]) }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($diseases as $result)
                    @php
                        $disease = $result['disease'];
                        $classification = $disease->diseaseClassification;
                        $bodySystems = $disease->clinicalSigns
                            ->map(fn ($sign) => $sign->anatomicalStructure?->bodySystem)
                            ->filter()
                            ->unique('id')
                            ->take(3);
                    @endphp
                    <article class="bg-neutral-primary-soft rounded-base shadow-sm border border-default-medium overflow-hidden flex flex-col dark:bg-slate-800 dark:border-slate-600">
                        <div class="p-5 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-base font-semibold text-heading dark:text-white leading-snug">
                                    <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate class="text-fg-brand hover:underline">{{ app()->getLocale() === 'ar' && $disease->name_ar ? $disease->name_ar : $disease->name }}</a>
                                </h3>
                                @if ($classification?->zoonotic)
                                    <span class="inline-flex shrink-0 items-center gap-1 text-xs font-medium px-2 py-1 rounded bg-danger-soft text-fg-danger-strong dark:bg-danger/20 dark:text-red-400">
                                        <x-lucide-biohazard class="w-3.5 h-3.5" />{{ __('large-animals.disease.zoonotic') }}
                                    </span>
                                @endif
                            </div>

                            @if ($classification?->etiology_type)
                                <p class="mt-2 text-sm text-body dark:text-slate-400">{{ $classification->etiology_type }}</p>
                            @endif

                            @if ($bodySystems->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ($bodySystems as $system)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded bg-neutral-secondary-soft text-body border border-default-medium dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600">
                                            <x-lucide-activity class="w-3 h-3" />
                                            {{ $system->localized_display_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if ($result['matched_signs']->isNotEmpty())
                                <div class="mt-4">
                                    <h4 class="text-xs font-semibold uppercase tracking-wide text-body dark:text-slate-400 mb-2">{{ __('large-animals.filter.matched_signs') }}</h4>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($result['matched_signs'] as $signName)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                                                <x-lucide-stethoscope class="w-3 h-3" />
                                                {{ $signName }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="px-5 py-4 border-t border-default-medium bg-neutral-secondary-soft/50 dark:bg-slate-700/30">
                            <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm font-semibold text-fg-brand hover:underline">
                                {{ __('large-animals.disease.overview') }}
                                <x-lucide-arrow-right class="w-4 h-4 rtl:hidden" />
                                <x-lucide-arrow-left class="w-4 h-4 ltr:hidden" />
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <p class="text-xs text-body dark:text-slate-400">{{ __('large-animals.filter.disclaimer') }}</p>
    </div>
@endsection