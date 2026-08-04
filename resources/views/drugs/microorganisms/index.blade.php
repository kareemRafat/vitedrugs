@extends('app.layouts.master')

@section('title', __('drugs.microorganisms.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-drugs.page-hero
            :heading="__('drugs.microorganisms.heading')"
            :subtitle="__('drugs.microorganisms.subtitle')"
            :badge="__('drugs.hero.badge.microorganisms')"
            badgeIcon="bug"
            :stats="[
                ['count' => $grouped->flatten()->count(), 'label' => __('drugs.hero.stats.microorganisms'), 'icon' => 'microscope'],
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
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.microorganisms.types.' . $type) }}</h2>
                    <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $items->count() }}</span>
                </div>
                <div class="p-5">
                    @if ($items->isEmpty())
                        <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.microorganisms.no_data') }}</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($items as $microorganism)
                                <a href="{{ route('drugs.microorganisms.show', $microorganism->slug) }}" wire:navigate
                                    class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                    <span class="text-sm font-medium text-heading dark:text-white">{{ $microorganism->name }}</span>
                                    <x-lucide-arrow-right class="w-4 h-4 shrink-0 text-body rtl:rotate-180 dark:text-slate-400" />
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

    </div>
@endsection
