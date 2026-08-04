@extends('app.layouts.master')

@section('title', __('drugs.diagnosis.results_title'))

@section('content')
    @php $tiers = [['label' => __('drugs.diagnosis.primary_results'), 'results' => $primaryResults, 'tone' => 'text-fg-brand'], ['label' => __('drugs.diagnosis.secondary_results'), 'results' => $secondaryResults, 'tone' => 'text-body'], ['label' => __('drugs.diagnosis.low_support_results'), 'results' => $lowSupportResults, 'tone' => 'text-fg-danger-strong']]; @endphp
    <div class="space-y-4">
        <x-drugs.page-hero :heading="__('drugs.diagnosis.results_heading')" :subtitle="__('drugs.diagnosis.results_subtitle')" :badge="__('drugs.hero.badge.results')" badgeIcon="stethoscope">
            <a href="{{ route('drugs.diagnosis') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white/15 text-white border border-white/30 rounded-base hover:bg-white/25"><x-lucide-rotate-ccw class="w-4 h-4" />{{ __('drugs.diagnosis.restart') }}</a>
        </x-drugs.page-hero>
        <x-drugs.stepper :current="3" />
        @if (collect($primaryResults)->isEmpty() && collect($secondaryResults)->isEmpty() && collect($lowSupportResults)->isEmpty())
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800"><x-lucide-search-x class="w-12 h-12 text-body mx-auto mb-3" /><h2 class="text-lg font-semibold text-heading dark:text-white">{{ __('drugs.diagnosis.no_results') }}</h2><p class="mt-1 text-sm text-body">{{ __('drugs.diagnosis.no_results_desc') }}</p></div>
        @endif
        @foreach ($tiers as $tier)
            @if (collect($tier['results'])->isNotEmpty())
                <section class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden dark:bg-slate-800">
                    <h2 class="px-5 py-4 text-base font-semibold {{ $tier['tone'] }} border-b border-default-medium">{{ $tier['label'] }}</h2>
                    <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-neutral-secondary-soft text-body dark:bg-slate-700"><tr><th class="px-5 py-3 text-start font-semibold">{{ __('drugs.diagnosis.disease') }}</th><th class="px-5 py-3 text-start font-semibold">{{ __('drugs.diagnosis.probability') }}</th><th class="px-5 py-3 text-start font-semibold">{{ __('drugs.diagnosis.match_score') }}</th><th class="px-5 py-3 text-start font-semibold">{{ __('drugs.diagnosis.matched_signs') }}</th><th class="px-5 py-3 text-start font-semibold">{{ __('drugs.diagnosis.missing_key_findings') }}</th></tr></thead><tbody class="divide-y divide-default-medium">
                        @foreach ($tier['results'] as $result)
                            <tr class="align-top"><td class="px-5 py-4 font-semibold text-heading dark:text-white"><a href="{{ route('drugs.diseases.show', $result['disease']->slug) }}" wire:navigate class="text-fg-brand hover:underline">{{ app()->getLocale() === 'ar' && $result['disease']->name_ar ? $result['disease']->name_ar : $result['disease']->name }}</a></td><td class="px-5 py-4 text-heading dark:text-white">{{ $result['probability'] }}%</td><td class="px-5 py-4 text-heading dark:text-white">{{ $result['match_score'] }}%</td><td class="px-5 py-4 text-body">{{ implode(', ', $result['matched_signs']) }}</td><td class="px-5 py-4 text-body">{{ implode(', ', $result['missing_key_findings']) ?: '-' }}</td></tr>
                        @endforeach
                    </tbody></table></div>
                </section>
            @endif
        @endforeach
        <p class="text-xs text-body dark:text-slate-400">{{ __('drugs.diagnosis.disclaimer') }}</p>
    </div>
@endsection
