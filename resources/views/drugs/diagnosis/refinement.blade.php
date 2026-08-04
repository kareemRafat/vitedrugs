@extends('app.layouts.master')

@section('title', __('drugs.diagnosis.refinement_title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-drugs.page-hero
            :heading="__('drugs.diagnosis.refinement_heading')"
            :subtitle="__('drugs.diagnosis.refinement_subtitle')"
            :badge="__('drugs.hero.badge.step_2')"
            badgeIcon="chevrons-right"
            :stats="[
                ['count' => count($selectedSigns), 'label' => __('drugs.hero.stats.selected_signs'), 'icon' => 'clipboard-list'],
                ['count' => count($results), 'label' => __('drugs.hero.stats.candidates'), 'icon' => 'star'],
            ]"
        >
            <a href="{{ route('drugs.diagnosis') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white/15 text-white border border-white/30 rounded-base hover:bg-white/25 transition-colors">
                <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                {{ __('drugs.diagnosis.refinement_back') }}
            </a>
        </x-drugs.page-hero>

        <form method="POST" action="{{ route('drugs.diagnosis.results') }}">
            @csrf

            @foreach ($selectedSigns as $signId)
                <input type="hidden" name="clinical_signs[]" value="{{ $signId }}">
            @endforeach

            @if (request('host_species_id'))
                <input type="hidden" name="host_species_id" value="{{ request('host_species_id') }}">
            @endif

            {{-- Top candidates preview --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
                <h2 class="text-base font-semibold text-heading dark:text-white mb-4">{{ __('drugs.diagnosis.candidates_preview') }}</h2>
                <div class="space-y-2">
                    @forelse (collect($results)->take(5) as $result)
                        <div class="flex flex-wrap items-center gap-3 p-3 rounded-base border border-default-medium dark:border-slate-700">
                            <a href="{{ route('drugs.diseases.show', $result['disease']->slug) }}" wire:navigate
                                class="text-sm font-medium text-fg-brand hover:underline">{{ app()->getLocale() === 'ar' && $result['disease']->name_ar ? $result['disease']->name_ar : $result['disease']->name }}</a>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('drugs.diagnosis.match_types.'.$result['match_type_key']) }}</span>
                            <span class="ms-auto text-sm text-body dark:text-slate-400">{{ __('drugs.diagnosis.score') }}: {{ $result['score'] }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.diagnosis.no_results') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Refinement pool --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
                <h2 class="text-base font-semibold text-heading dark:text-white mb-1">{{ __('drugs.diagnosis.refinement_signs') }}</h2>
                <p class="text-sm text-body dark:text-slate-400 mb-4">{{ __('drugs.diagnosis.refinement_subtitle') }}</p>

                @if ($refinementSignsPool->isEmpty())
                    <p class="text-sm text-body dark:text-slate-400 text-center py-6">{{ __('drugs.diagnosis.refinement_none') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($refinementSignsPool as $sign)
                            <label class="flex items-start gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft cursor-pointer hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                <input type="checkbox" name="refinement_signs[]" value="{{ $sign->id }}"
                                    class="mt-0.5 w-4 h-4 rounded-xs text-brand focus:ring-2 focus:ring-brand-soft dark:bg-slate-600 dark:border-slate-500">
                                <span>
                                    <span class="block text-sm font-medium text-heading dark:text-white">{{ $sign->localized_display_name }}</span>
                                    @if ($sign->pivot?->weight)
                                        <span class="block text-xs text-body dark:text-slate-400 mt-0.5">{{ __('drugs.disease.clinical_sign_weight') }}: {{ $sign->pivot->weight }}</span>
                                    @endif
                                    @if ($sign->pivot?->is_pathognomonic)
                                        <span class="inline-flex items-center px-2 py-0.5 mt-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('drugs.disease.pathognomonic') }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 pt-5 border-t border-default-medium dark:border-slate-700 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors">
                        <x-lucide-check-circle class="w-4 h-4" />
                        {{ __('drugs.diagnosis.refinement_continue') }}
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection
