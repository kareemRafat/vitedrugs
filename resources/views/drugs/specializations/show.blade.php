@extends('app.layouts.master')

@section('title', __('drugs.specializations.groups.' . $group) . ' — ' . __('drugs.specializations.title'))

@section('content')
    @php
        $icon = match ($group) {
            'ruminant' => 'beef',
            'poultry' => 'egg',
            'fish' => 'fish',
            default => 'paw-print',
        };
    @endphp

    <div class="space-y-4">

        {{-- Hero --}}
        <x-drugs.page-hero
            :heading="__('drugs.specializations.groups.' . $group)"
            :subtitle="$species->pluck('display_name')->implode(', ')"
            :badge="__('drugs.hero.badge.specializations')"
            :badgeIcon="$icon"
            :stats="[
                ['count' => $diseases->count(), 'label' => __('drugs.hero.stats.diseases'), 'icon' => 'activity'],
                ['count' => $products->count(), 'label' => __('drugs.hero.stats.products'), 'icon' => 'package'],
                ['count' => $articles->count(), 'label' => __('drugs.hero.stats.articles'), 'icon' => 'file-text'],
                ['count' => $microorganisms->count(), 'label' => __('drugs.hero.stats.microorganisms'), 'icon' => 'bug'],
            ]"
        >
            <a href="{{ route('drugs.specializations.index') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white/15 text-white border border-white/30 rounded-base hover:bg-white/25 transition-colors">
                <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                {{ __('drugs.specializations.back') }}
            </a>
        </x-drugs.page-hero>

        {{-- Stat chips --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center shrink-0">
                    <x-lucide-activity class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-lg font-bold text-heading dark:text-white">{{ $diseases->count() }}</p>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('drugs.specializations.diseases') }}</p>
                </div>
            </div>
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center shrink-0">
                    <x-lucide-package class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-lg font-bold text-heading dark:text-white">{{ $products->count() }}</p>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('drugs.specializations.products') }}</p>
                </div>
            </div>
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center shrink-0">
                    <x-lucide-file-text class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-lg font-bold text-heading dark:text-white">{{ $articles->count() }}</p>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('drugs.specializations.articles') }}</p>
                </div>
            </div>
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center shrink-0">
                    <x-lucide-microscope class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-lg font-bold text-heading dark:text-white">{{ $microorganisms->count() }}</p>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('drugs.specializations.microorganisms') }}</p>
                </div>
            </div>
        </div>

        {{-- Diseases --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                <x-lucide-activity class="w-4 h-4 text-body dark:text-slate-400" />
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.specializations.diseases') }}</h2>
            </div>
            <div class="p-5">
                @if ($diseases->isEmpty())
                    <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.specializations.no_diseases') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($diseases as $disease)
                            <a href="{{ route('drugs.diseases.show', $disease->slug) }}" wire:navigate
                                class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                <span class="text-sm font-medium text-heading dark:text-white">{{ $disease->name }}</span>
                                <x-lucide-arrow-right class="w-4 h-4 shrink-0 text-body rtl:rotate-180 dark:text-slate-400" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Products --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                <x-lucide-package class="w-4 h-4 text-body dark:text-slate-400" />
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.specializations.products') }}</h2>
            </div>
            <div class="p-5">
                @if ($products->isEmpty())
                    <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.specializations.no_products') }}</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($products as $product)
                            <a href="{{ route('products.show', $product) }}" wire:navigate
                                class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                <span class="text-sm font-medium text-heading dark:text-white">{{ $product->trade_name }}</span>
                                <x-lucide-arrow-right class="w-4 h-4 shrink-0 text-body rtl:rotate-180 dark:text-slate-400" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Articles --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-file-text class="w-4 h-4 text-body dark:text-slate-400" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.specializations.articles') }}</h2>
                </div>
                <div class="p-5">
                    @if ($articles->isEmpty())
                        <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.specializations.no_articles') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($articles as $article)
                                <a href="{{ route('drugs.medical-articles.show', $article->slug) }}" wire:navigate
                                    class="block p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                    <span class="block text-sm font-medium text-heading dark:text-white">{{ $article->title }}</span>
                                    @if ($article->summary)
                                        <span class="block text-xs text-body dark:text-slate-400 mt-1 line-clamp-2">{{ $article->summary }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Microorganisms --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-microscope class="w-4 h-4 text-body dark:text-slate-400" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.specializations.microorganisms') }}</h2>
                </div>
                <div class="p-5">
                    @if ($microorganisms->isEmpty())
                        <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.specializations.no_microorganisms') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($microorganisms as $microorganism)
                                <a href="{{ route('drugs.microorganisms.show', $microorganism->slug) }}" wire:navigate
                                    class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                    <span class="text-sm font-medium text-heading dark:text-white">{{ $microorganism->name }}</span>
                                    @if ($microorganism->microorganism_type)
                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('drugs.microorganisms.types.' . $microorganism->microorganism_type) }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection
