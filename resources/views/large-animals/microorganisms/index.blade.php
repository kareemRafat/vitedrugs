@extends('app.layouts.master')

@section('title', __('large-animals.microorganisms.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="__('large-animals.microorganisms.heading')"
            :subtitle="__('large-animals.microorganisms.subtitle')"
            :badge="__('large-animals.hero.badge.microorganisms')"
            badgeIcon="bug"
            :stats="[
                ['count' => $grouped->flatten()->count(), 'label' => __('large-animals.hero.stats.microorganisms'), 'icon' => 'microscope'],
            ]"
        />

        @foreach ($grouped as $type => $items)
            <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    @php
                        $icon = match ($type) {
                            'bacteria' => 'bug',
                            'virus' => 'dna',
                            'fungus' => 'sprout',
                            'parasite' => 'worm',
                            default => 'microscope',
                        };
                    @endphp
                    <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 text-body dark:text-slate-400" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.microorganisms.types.' . $type) }}</h2>
                    <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $items->count() }}</span>
                </div>
                <div class="p-5">
                    @if ($items->isEmpty())
                        <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.microorganisms.no_data') }}</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($items as $microorganism)
                                <a href="{{ route('large-animals.microorganisms.show', $microorganism->slug) }}" wire:navigate
                                    class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 shrink-0 text-body dark:text-slate-400" />
                                        <span class="text-sm font-medium text-heading dark:text-white truncate">{{ $microorganism->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        @if ($microorganism->diseases_count > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-xs bg-danger-soft text-fg-danger-strong dark:bg-red-950 dark:text-red-300" title="{{ __('large-animals.microorganisms.disease_count') }}">
                                                <x-lucide-activity class="w-3 h-3" />
                                                {{ $microorganism->diseases_count }}
                                            </span>
                                        @endif
                                        @if ($microorganism->active_ingredients_count > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand" title="{{ __('large-animals.microorganisms.antibiotic_count') }}">
                                                <x-lucide-pill class="w-3 h-3" />
                                                {{ $microorganism->active_ingredients_count }}
                                            </span>
                                        @endif
                                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 text-body dark:text-slate-400" />
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
@endsection
