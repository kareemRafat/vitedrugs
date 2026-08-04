@extends('app.layouts.master')

@section('title', __('drugs.comparison.results_heading'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-drugs.page-hero
            :heading="__('drugs.comparison.results_heading')"
            :subtitle="__('drugs.comparison.subtitle')"
            :badge="__('drugs.hero.badge.comparison_results')"
            badgeIcon="columns-3"
            :stats="[
                ['count' => $comparisonDiseases->count(), 'label' => __('drugs.hero.stats.compared'), 'icon' => 'columns-3'],
            ]"
        >
            <a href="{{ route('drugs.comparison') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white/15 text-white border border-white/30 rounded-base hover:bg-white/25 transition-colors">
                <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                {{ __('drugs.comparison.back') }}
            </a>
        </x-drugs.page-hero>

        @if ($comparisonDiseases->isEmpty())

            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-columns-3 class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h2 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ __('drugs.comparison.no_selection') }}</h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('drugs.comparison.no_selection_desc') }}</p>
            </div>

        @else

            @php
                $matrix = [
                    ['label' => __('drugs.comparison.clinical_signs'), 'icon' => 'stethoscope', 'items' => $clinicalSigns],
                    ['label' => __('drugs.comparison.postmortem_findings'), 'icon' => 'microscope', 'items' => $postmortemFindings],
                    ['label' => __('drugs.comparison.diagnosis'), 'icon' => 'test-tube-2', 'items' => $diagnosticMethods],
                    ['label' => __('drugs.comparison.treatment'), 'icon' => 'syringe', 'items' => $treatments],
                    ['label' => __('drugs.comparison.prevention'), 'icon' => 'shield-check', 'items' => $preventions],
                ];
            @endphp

            @foreach ($matrix as $category)
                @if ($category['items']->isNotEmpty())
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-dynamic-component :component="'lucide-' . $category['icon']" class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $category['label'] }}</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right">
                                <thead class="text-xs uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700 dark:text-slate-400">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 min-w-[14rem]">{{ $category['label'] }}</th>
                                        @foreach ($comparisonDiseases as $disease)
                                            <th scope="col" class="px-5 py-3 min-w-[8rem]">
                                                <a href="{{ route('drugs.diseases.show', $disease->slug) }}" wire:navigate class="font-semibold text-fg-brand hover:underline">{{ $disease->name }}</a>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category['items'] as $item)
                                        <tr class="border-b border-default-medium last:border-0 dark:border-slate-700">
                                            <td class="px-5 py-3 font-medium text-heading dark:text-white">{{ $item['name'] }}</td>
                                            @foreach ($comparisonDiseases as $disease)
                                                <td class="px-5 py-3">
                                                    @if ($item['presence'][$disease->slug] ?? false)
                                                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-success-strong dark:text-emerald-400" title="{{ __('drugs.comparison.present') }}">
                                                            <x-lucide-check-circle class="w-4 h-4" />
                                                            {{ __('drugs.comparison.present') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 text-sm text-body/60 dark:text-slate-500" title="{{ __('drugs.comparison.absent') }}">
                                                            <x-lucide-minus-circle class="w-4 h-4" />
                                                            {{ __('drugs.comparison.absent') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach

        @endif

    </div>
@endsection
