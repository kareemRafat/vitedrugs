@extends('app.layouts.master')

@section('title', __('drugs.diagnosis.refinement_title'))

@section('content')
    <div class="space-y-4">
        <x-drugs.page-hero :heading="__('drugs.diagnosis.refinement_heading')" :subtitle="__('drugs.diagnosis.refinement_subtitle')" :badge="__('drugs.hero.badge.step_2')" badgeIcon="list-checks" />
        <x-drugs.stepper :current="2" />
        <form method="POST" action="{{ route('drugs.diagnosis.results.store') }}" class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            @csrf
            <input type="hidden" name="host_species_id" value="{{ $hostSpeciesId }}">
            @foreach ($selectedSigns as $signId)<input type="hidden" name="clinical_signs[]" value="{{ $signId }}">@endforeach
            <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.diagnosis.refinement_signs') }}</h2>
            <p class="mt-1 text-sm text-body dark:text-slate-400">{{ __('drugs.diagnosis.refinement_subtitle') }}</p>
            @if ($refinementSignsPool->isEmpty())
                <p class="py-8 text-center text-sm text-body dark:text-slate-400">{{ __('drugs.diagnosis.refinement_none') }}</p>
            @else
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach ($refinementSignsPool as $sign)
                        <label class="flex items-start gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft cursor-pointer hover:border-brand dark:bg-slate-700 dark:border-slate-600">
                            <input type="checkbox" name="refinement_signs[]" value="{{ $sign->id }}" class="mt-0.5 w-4 h-4 rounded-xs text-brand focus:ring-2 focus:ring-brand-soft">
                            <span class="text-sm font-medium text-heading dark:text-white">{{ $sign->localized_display_name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
            <div class="mt-6 pt-5 border-t border-default-medium flex items-center justify-between gap-3">
                <a href="{{ route('drugs.diagnosis') }}" wire:navigate class="text-sm font-semibold text-fg-brand hover:underline">{{ __('drugs.diagnosis.refinement_back') }}</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base"><x-lucide-check-circle class="w-4 h-4" />{{ __('drugs.diagnosis.refinement_continue') }}</button>
            </div>
        </form>
    </div>
@endsection
