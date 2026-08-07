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
            :subtitle="$species->pluck('display_name')->implode(', ')"
            :stats="[
                ['value' => $diseases->count(), 'label' => __('large-animals.specializations.diseases'), 'icon' => 'activity'],
                ['value' => $products->count(), 'label' => __('large-animals.specializations.products'), 'icon' => 'package', 'labelClass' => 'text-amber-600 dark:text-amber-400'],
                ['value' => $articles->count(), 'label' => __('large-animals.specializations.articles'), 'icon' => 'file-text', 'labelClass' => 'text-rose-600 dark:text-rose-400'],
                ['value' => $microorganisms->count(), 'label' => __('large-animals.specializations.microorganisms'), 'icon' => 'microscope', 'labelClass' => 'text-emerald-600 dark:text-emerald-400'],
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
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.specializations.products') }}</h2>
            </div>
            <div class="p-5">
                @if ($products->isEmpty())
                    <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.specializations.no_products') }}</p>
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
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.specializations.articles') }}</h2>
                </div>
                <div class="p-5">
                    @if ($articles->isEmpty())
                        <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.specializations.no_articles') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($articles as $article)
                                <a href="{{ route('large-animals.medical-articles.show', $article->slug) }}" wire:navigate
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
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.specializations.microorganisms') }}</h2>
                </div>
                <div class="p-5">
                    @if ($microorganisms->isEmpty())
                        <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.specializations.no_microorganisms') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($microorganisms as $microorganism)
                                <a href="{{ route('large-animals.microorganisms.show', $microorganism->slug) }}" wire:navigate
                                    class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                    <span class="text-sm font-medium text-heading dark:text-white">{{ $microorganism->name }}</span>
                                    @if ($microorganism->microorganism_type)
                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.microorganisms.types.' . $microorganism->microorganism_type) }}</span>
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
