@extends('app.layouts.master')

@section('title', __('drugs.diagnosis.results_title'))

@section('content')
    <div class="space-y-4">

        {{-- Header --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-heading dark:text-white">{{ __('drugs.diagnosis.results_heading') }}</h1>
                    <p class="text-body dark:text-slate-400 text-base mt-1">{{ __('drugs.diagnosis.results_subtitle') }}</p>
                </div>
                <a href="{{ route('drugs.diagnosis') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-fg-brand border border-brand rounded-base hover:bg-brand-soft transition-colors dark:text-brand dark:hover:bg-brand/20">
                    <x-lucide-rotate-ccw class="w-4 h-4" />
                    {{ __('drugs.diagnosis.restart') }}
                </a>
            </div>
        </div>

        @if (collect($primaryResults)->isEmpty() && collect($secondaryResults)->isEmpty() && collect($lowSupportResults)->isEmpty())

            {{-- Empty state --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-search-x class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h2 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ __('drugs.diagnosis.no_results') }}</h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('drugs.diagnosis.no_results_desc') }}</p>
            </div>

        @else

            @php
                $tiers = [
                    ['label' => __('drugs.diagnosis.primary_results'), 'results' => $primaryResults, 'icon' => 'star'],
                    ['label' => __('drugs.diagnosis.secondary_results'), 'results' => $secondaryResults, 'icon' => 'activity'],
                    ['label' => __('drugs.diagnosis.low_support_results'), 'results' => $lowSupportResults, 'icon' => 'minus-circle'],
                ];
            @endphp

            @foreach ($tiers as $tier)
                @if (collect($tier['results'])->isNotEmpty())
                    <section class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-dynamic-component :component="'lucide-' . $tier['icon']" class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $tier['label'] }}</h2>
                            <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ collect($tier['results'])->count() }}</span>
                        </div>
                        <div class="p-5 space-y-4">
                            @foreach ($tier['results'] as $result)
                                @php
                                    $confidence = $result['confidence'] ?? 'Low';
                                    $confidenceBadge = match ($confidence) {
                                        'High' => 'bg-success-soft border-success-subtle text-fg-success-strong dark:bg-green-950 dark:border-green-900 dark:text-green-300',
                                        'Moderate' => 'bg-brand-soft border-brand-subtle text-fg-brand-strong dark:bg-brand/20 dark:border-brand-subtle dark:text-brand',
                                        default => 'bg-neutral-secondary-soft border-default-medium text-body dark:bg-slate-700 dark:border-slate-600 dark:text-slate-300',
                                    };
                                @endphp
                                <article class="rounded-base border border-default-medium dark:border-slate-700 overflow-hidden">
                                    <div class="p-5">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <a href="{{ route('drugs.diseases.show', $result['disease']->slug) }}" wire:navigate
                                                class="text-lg font-bold text-heading dark:text-white hover:text-fg-brand dark:hover:text-brand transition-colors">{{ $result['disease']->name }}</a>
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-base border {{ $confidenceBadge }}">{{ $confidence }}</span>
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-base bg-neutral-secondary-soft text-body border border-default-medium dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600">{{ $result['match_type'] }}</span>
                                        </div>

                                        @if ($result['disease']->description)
                                            <p class="mt-3 text-sm text-body dark:text-slate-400 leading-relaxed">{{ $result['disease']->description }}</p>
                                        @endif

                                        {{-- Match strength --}}
                                        @php
                                            $strength = min(100, max(1, (int) ($result['relative_match_strength'] ?? 0)));
                                        @endphp
                                        <div class="mt-4">
                                            <div class="flex items-center justify-between text-xs font-medium text-body dark:text-slate-400 mb-1.5">
                                                <span>{{ __('drugs.diagnosis.match_strength') }}</span>
                                                <span>{{ $strength }}%</span>
                                            </div>
                                            <div class="h-2 rounded-full bg-neutral-secondary-medium dark:bg-slate-700 overflow-hidden">
                                                <div class="h-full rounded-full bg-brand" style="width: {{ $strength }}%"></div>
                                            </div>
                                        </div>

                                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {{-- Matched signs --}}
                                            <div>
                                                <h3 class="text-xs font-semibold uppercase tracking-wider text-body dark:text-slate-400 mb-2">{{ __('drugs.diagnosis.matched_signs') }}</h3>
                                                @if (collect($result['matched_signs'])->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach ($result['matched_signs'] as $signName)
                                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ $signName }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.diagnosis.no_results') }}</p>
                                                @endif
                                            </div>

                                            {{-- Missing key findings --}}
                                            @if (collect($result['missing_key_findings'])->isNotEmpty())
                                                <div>
                                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-body dark:text-slate-400 mb-2">{{ __('drugs.diagnosis.missing_key_findings') }}</h3>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        @foreach ($result['missing_key_findings'] as $finding)
                                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-base bg-danger-soft border border-danger-subtle text-fg-danger-strong dark:bg-red-950 dark:border-red-900 dark:text-red-300">{{ $finding }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Reasons --}}
                                        @if (collect($result['reasons'])->isNotEmpty())
                                            <div class="mt-5 pt-4 border-t border-default-medium dark:border-slate-700">
                                                <h3 class="text-xs font-semibold uppercase tracking-wider text-body dark:text-slate-400 mb-2">{{ __('drugs.diagnosis.reasons') }}</h3>
                                                <ul class="space-y-1.5">
                                                    @foreach ($result['reasons'] as $reason)
                                                        <li class="flex items-start gap-2 text-sm text-body dark:text-slate-300">
                                                            <x-lucide-check-circle class="w-4 h-4 shrink-0 mt-0.5 text-brand dark:text-brand" />
                                                            {{ $reason }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="mt-5 flex justify-end">
                                            <a href="{{ route('drugs.diseases.show', $result['disease']->slug) }}" wire:navigate
                                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors">
                                                {{ __('drugs.diagnosis.view_disease') }}
                                                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach

        @endif

    </div>
@endsection
