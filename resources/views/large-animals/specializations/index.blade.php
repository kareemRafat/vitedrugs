@extends('app.layouts.master')

@section('title', __('large-animals.specializations.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="__('large-animals.specializations.heading')"
            :subtitle="__('large-animals.specializations.subtitle')"
            :badge="__('large-animals.hero.badge.specializations')"
            badgeIcon="paw-print"
            :stats="[
                ['count' => $groups->sum('disease_count'), 'label' => __('large-animals.hero.stats.diseases'), 'icon' => 'activity'],
                ['count' => $groups->sum(fn ($g) => $g['species']->count()), 'label' => __('large-animals.hero.stats.species'), 'icon' => 'paw-print'],
            ]"
        />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($groups as $group)
                @php
                    $icon = match ($group['key']) {
                        'ruminant' => 'beef',
                        'poultry' => 'egg',
                        'fish' => 'fish',
                        default => 'paw-print',
                    };
                @endphp
                <a href="{{ route('large-animals.specializations.show', $group['key']) }}" wire:navigate
                    class="group bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium hover:shadow-md hover:border-brand transition-all duration-300 dark:bg-slate-800 dark:border-slate-700 dark:hover:border-brand p-6">
                    <div class="w-12 h-12 rounded-2xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center mb-4">
                        <x-dynamic-component :component="'lucide-' . $icon" class="w-6 h-6" />
                    </div>
                    <h2 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('large-animals.specializations.groups.' . $group['key']) }}</h2>

                    @if ($group['species']->isNotEmpty())
                        <p class="text-sm text-body dark:text-slate-400 mb-3">{{ $group['species']->pluck('display_name')->implode(', ') }}</p>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold rounded-base bg-brand-soft text-fg-brand px-3 py-1 dark:bg-brand/20 dark:text-brand">
                            <x-lucide-activity class="w-3.5 h-3.5" />
                            {{ $group['disease_count'] }} {{ __('large-animals.specializations.diseases') }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-fg-brand group-hover:gap-2 transition-all dark:text-brand">
                            {{ __('large-animals.specializations.explore') }}
                            <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
@endsection
