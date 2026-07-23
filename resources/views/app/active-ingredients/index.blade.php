@extends('app.layouts.master')

@section('title', __('messages.active_ingredients.index_title'))

@section('content')
    <div class="space-y-4">

        <x-page-hero
            :heading="__('messages.active_ingredients.index_heading')"
            :subtitle="__('messages.active_ingredients.index_subtitle')"
            :stats="[
                ['count' => number_format($ingredients->total()), 'label' => __('messages.active_ingredients.ingredients_label'), 'icon' => 'flask-conical'],
            ]"
        />

        {{-- Search / Filters --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-3">
                    <div class="lg:col-span-2 relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            placeholder="{{ __('messages.active_ingredients.search_placeholder') }}">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2 focus:outline-none">
                            <x-lucide-search class="w-4 h-4 inline -mt-0.5 me-1" />
                            {{ __('messages.active_ingredients.search_button') }}
                        </button>
                        <a href="{{ route('active-ingredients.index') }}" wire:navigate
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600">
                            <x-lucide-x class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Letter navigation --}}
        @if (!empty($availableLetters))
            @php $allLetters = range('A', 'Z'); $activeLetter = request('letter'); @endphp
            <div class="bg-neutral-primary-soft shadow-xs p-4 rounded-base dark:bg-slate-800">
                <div class="flex flex-nowrap md:flex-wrap items-center justify-start md:justify-center gap-4 md:gap-2 overflow-x-auto md:overflow-visible pb-1">
                    @foreach ($allLetters as $letter)
                        @if (in_array($letter, $availableLetters))
                            @if ($activeLetter === $letter)
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-brand dark:text-amber-400 text-base font-extrabold cursor-default">{{ $letter }}</span>
                            @else
                                <a href="{{ route('active-ingredients.index', array_merge(request()->except('letter', 'page'), ['letter' => $letter])) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-heading bg-neutral-secondary-soft max-md:bg-transparent dark:bg-transparent hover:bg-brand/10 hover:text-brand dark:text-white dark:hover:text-brand transition-colors">
                                    {{ $letter }}
                                </a>
                            @endif
                        @else
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-body/30 dark:text-slate-600 cursor-default">{{ $letter }}</span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Ingredients Directory --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800">
            <div class="px-5 py-4 border-b border-default-medium dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.directory_title') }}</h2>
                <span class="text-sm text-body dark:text-slate-400">
                    {{ __('messages.active_ingredients.showing') }}
                    {{ $ingredients->firstItem() ?? 0 }}–{{ $ingredients->lastItem() ?? 0 }}
                    {{ __('messages.active_ingredients.of') }}
                    {{ number_format($ingredients->total()) }}
                </span>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse ($ingredients as $ingredient)
                        <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-neutral-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-xl hover:shadow-brand/5 dark:hover:shadow-sky-500/5 hover:-translate-y-1 transition-all duration-300 flex flex-col relative overflow-hidden">
                            <div class="h-1.5 w-full bg-gradient-to-r from-brand via-brand-strong to-sky-400 dark:from-sky-400 dark:via-brand dark:to-sky-300"></div>

                            {{-- Card Header --}}
                            <div class="bg-gradient-to-br from-brand/5 to-sky-50 dark:from-brand/10 dark:to-slate-700/70 p-5 border-b border-neutral-200/60 dark:border-slate-700/60">
                                <a href="{{ route('active-ingredients.show', $ingredient) }}" wire:navigate class="block group/title">
                                    <h3 class="text-lg sm:text-xl font-bold text-heading dark:text-white group-hover/title:text-brand dark:group-hover/title:text-sky-400 transition-colors duration-200 leading-snug line-clamp-2">
                                        {{ $ingredient->name }}
                                    </h3>
                                    @if ($ingredient->name_ar && app()->getLocale() === 'ar')
                                        <span class="block text-xs font-medium text-body/70 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $ingredient->name_ar }}</span>
                                    @endif
                                </a>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-5 flex-1 space-y-3.5 bg-white dark:bg-slate-800">
                                <div class="flex items-center gap-3 text-sm text-body dark:text-slate-300 group/detail">
                                    <span class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-800/40 flex items-center justify-center shrink-0 group-hover/detail:bg-sky-100 dark:group-hover/detail:bg-sky-900/50 transition-colors">
                                        <x-lucide-flask-conical class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                                    </span>
                                    <span class="font-medium text-heading dark:text-slate-200 text-xs sm:text-sm">{{ $ingredient->products_count }} {{ __('messages.active_ingredients.products_count') }}</span>
                                </div>
                            </div>

                            {{-- Card Action Footer --}}
                            <div class="px-5 py-3.5 bg-neutral-50/70 dark:bg-slate-800/90 border-t border-neutral-100 dark:border-slate-700/80 flex items-center justify-between mt-auto">
                                <a href="{{ route('active-ingredients.show', $ingredient) }}" wire:navigate
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-brand hover:text-white bg-brand/10 hover:bg-brand dark:bg-sky-400/10 dark:text-sky-400 dark:hover:bg-sky-500 dark:hover:text-white transition-all duration-200 shadow-2xs group/action">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    <span>{{ __('messages.active_ingredients.details') }}</span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 group-hover/action:translate-x-0.5 transition-transform" />
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <x-lucide-flask-conical class="w-12 h-12 text-body dark:text-slate-400 mx-auto mb-4" />
                            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.active_ingredients.no_ingredients') }}</h3>
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.active_ingredients.try_another') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($ingredients->hasPages())
            @php
                $currentPage = $ingredients->currentPage();
                $lastPage = $ingredients->lastPage();
                $window = 2;
                $startPage = max(1, $currentPage - $window);
                $endPage = min($lastPage, $currentPage + $window);
                $showStartEllipsis = $startPage > 2;
                $showEndEllipsis = $endPage < $lastPage - 1;
            @endphp
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4">
                <div class="hidden sm:block text-sm text-body dark:text-slate-400">
                    {{ __('messages.active_ingredients.showing') }}
                    {{ $ingredients->firstItem() ?? 0 }}–{{ $ingredients->lastItem() ?? 0 }}
                    {{ __('messages.active_ingredients.of') }}
                    {{ number_format($ingredients->total()) }}
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ $ingredients->previousPageUrl() }}" wire:navigate rel="prev" @if($ingredients->onFirstPage()) aria-disabled="true" @endif
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft @if($ingredients->onFirstPage()) opacity-40 cursor-not-allowed pointer-events-none @else @endif dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                        <x-lucide-chevron-left class="w-4 h-4 rtl:rotate-180" />
                        <span>{{ __('messages.active_ingredients.previous_page') }}</span>
                    </a>

                    <div class="hidden sm:flex items-center gap-1">
                        @if ($startPage > 1)
                            <a href="{{ $ingredients->withQueryString()->url(1) }}" wire:navigate
                                class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                1
                            </a>
                        @endif

                        @if ($showStartEllipsis)
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-body dark:text-slate-400">...</span>
                        @endif

                        @for ($i = $startPage; $i <= $endPage; $i++)
                            <a href="{{ $ingredients->withQueryString()->url($i) }}" wire:navigate
                                class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium rounded-base transition-colors
                                @if ($i === $currentPage)
                                    text-white bg-brand shadow-xs
                                @else
                                    text-heading bg-neutral-primary-soft border border-default-medium hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600
                                @endif">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($showEndEllipsis)
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-body dark:text-slate-400">...</span>
                        @endif

                        @if ($endPage < $lastPage)
                            <a href="{{ $ingredients->withQueryString()->url($lastPage) }}" wire:navigate
                                class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                {{ $lastPage }}
                            </a>
                        @endif
                    </div>

                    <a href="{{ $ingredients->nextPageUrl() }}" wire:navigate rel="next" @if(!$ingredients->hasMorePages()) aria-disabled="true" @endif
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft @if(!$ingredients->hasMorePages()) opacity-40 cursor-not-allowed pointer-events-none @else @endif dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                        <span>{{ __('messages.active_ingredients.next_page') }}</span>
                        <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    </a>
                </div>
            </div>
        @endif

    </div>
@endsection
