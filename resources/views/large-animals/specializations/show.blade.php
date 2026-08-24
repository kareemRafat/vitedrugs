@extends('app.layouts.master')

@section('title', __('large-animals.specializations.groups.' . $group) . ' — ' . __('large-animals.specializations.title'))

@section('content')

    <div class="space-y-4">

        {{-- Breadcrumb --}}
        <nav class="flex mb-4 pt-4 flex-wrap items-center gap-x-2 gap-y-1 text-sm text-body dark:text-slate-400" aria-label="{{ __('messages.nav.breadcrumb') }}">
            <a href="{{ route('large-animals.home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('messages.parts.large_animals') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <a href="{{ route('large-animals.specializations.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.specializations') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium">{{ __('large-animals.specializations.groups.' . $group) }}</span>
        </nav>

        {{-- Hero --}}
        <x-large-animals.card-hero
            :heading="__('large-animals.specializations.groups.' . $group)"
            :subtitle="__('large-animals.specializations.group_descriptions.' . $group)"
            :stats="[
                ['value' => $diseases->count(), 'label' => __('large-animals.specializations.diseases'), 'icon' => 'activity'],
            ]"
        >
            <x-slot name="action">
                <a href="{{ route('large-animals.specializations.index') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-base transition-all duration-200 text-xs font-medium whitespace-nowrap text-body hover:text-brand hover:bg-brand/10 dark:text-slate-400 dark:hover:text-brand dark:hover:bg-brand/20">
                    <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                    <span>{{ __('large-animals.specializations.back') }}</span>
                </a>
            </x-slot>
        </x-large-animals.card-hero>

        {{-- Static article --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                <x-lucide-file-text class="w-4 h-4 text-body dark:text-slate-400" />
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.specializations.articles.' . $group . '.title') }}</h2>
            </div>
            <div class="p-5 space-y-3">
                @foreach (__('large-animals.specializations.articles.' . $group . '.paragraphs') as $paragraph)
                    <p class="text-sm text-body dark:text-slate-300 leading-relaxed">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>

        {{-- Diseases --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                <x-lucide-activity class="w-4 h-4 text-body dark:text-slate-400" />
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.specializations.diseases') }}</h2>
            </div>
            <div class="p-5">
                @if ($diseases->isEmpty())
                    <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.specializations.no_diseases') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($diseases as $disease)
                            <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate
                                class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                <span class="text-sm font-medium text-heading dark:text-white">{{ $disease->localized_name }}</span>
                                <x-lucide-arrow-right class="w-4 h-4 shrink-0 text-body rtl:rotate-180 dark:text-slate-400" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
