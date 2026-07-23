@extends('app.layouts.master')

@section('title', __('messages.companies.index_title'))

@section('content')
    <div class="space-y-4">

        <x-page-hero
            :heading="__('messages.companies.index_heading')"
            :subtitle="__('messages.companies.index_subtitle')"
            :stats="[
                ['count' => number_format($companies->total()), 'label' => __('messages.companies.companies_label'), 'icon' => 'building-2'],
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
                            placeholder="{{ __('messages.companies.search_placeholder') }}">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-3 py-2 focus:outline-none">
                            <x-lucide-search class="w-3.5 h-3.5 inline -mt-0.5 me-1" />
                            {{ __('messages.companies.search_button') }}
                        </button>
                        <a href="{{ route('companies.index') }}" wire:navigate
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600">
                            <x-lucide-x class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Type filter --}}
        {{-- Mobile: native select --}}
        <div class="md:hidden bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-slate-800">
            <label class="block text-sm font-medium text-heading dark:text-white mb-1.5">{{ __('messages.companies.filter_by_type') }}</label>
            <form method="GET">
                <div class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base block w-full">
                    <select name="type" onchange="this.form.submit()"
                        class="bg-transparent border-none text-heading text-sm w-full px-3 py-2.5 rounded-base focus:ring-0 focus:outline-none dark:text-white dark:bg-slate-700">
                        <option value="">{{ __('messages.companies.filter_all') }} ({{ number_format(array_sum($typeCounts)) }})</option>
                        @foreach (['manufacturer', 'agent', 'distributor', 'marketing'] as $t)
                            <option value="{{ $t }}" @selected(request('type') === $t)>{{ __('messages.companies.types.' . $t) }} ({{ $typeCounts[$t] }})</option>
                        @endforeach
                    </select>
                </div>
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
            </form>
        </div>

        {{-- Desktop: segmented control --}}
        <div class="hidden md:block">
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-slate-800">
                <form method="GET" class="bg-neutral-secondary-soft dark:bg-slate-700 p-1 rounded-full border border-default-medium dark:border-slate-600 inline-flex flex-wrap gap-0.5">
                    <button type="submit" name="type" value=""
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-full transition-colors @if(!request('type')) bg-brand text-white shadow-xs @else text-heading hover:bg-neutral-primary-soft dark:text-white dark:hover:bg-slate-700 @endif">
                        <x-lucide-layers class="w-4 h-4" />
                        {{ __('messages.companies.filter_all') }}
                        <span class="text-xs @if(!request('type')) text-white/80 @else text-body dark:text-slate-400 @endif">({{ number_format(array_sum($typeCounts)) }})</span>
                    </button>

                    @foreach (['manufacturer', 'agent', 'distributor', 'marketing'] as $t)
                        @php
                            $icons = ['manufacturer' => 'building-2', 'agent' => 'handshake', 'distributor' => 'truck', 'marketing' => 'megaphone'];
                        @endphp
                        <button type="submit" name="type" value="{{ $t }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-full transition-colors @if(request('type') === $t) bg-brand text-white shadow-xs @else text-heading hover:bg-neutral-primary-soft dark:text-white dark:hover:bg-slate-700 @endif">
                            <x-dynamic-component :component="'lucide-' . $icons[$t]" class="w-4 h-4" />
                            {{ __('messages.companies.types.' . $t) }}
                            <span class="text-xs @if(request('type') === $t) text-white/80 @else text-body dark:text-slate-400 @endif">({{ $typeCounts[$t] }})</span>
                        </button>
                    @endforeach

                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </form>
            </div>
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
                                <a href="{{ route('companies.index', array_merge(request()->except('letter', 'page'), ['letter' => $letter])) }}" wire:navigate
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

        {{-- Companies Directory --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800">
            <div class="px-5 py-4 border-b border-default-medium dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.directory_title') }}</h2>
                <span class="text-sm text-body dark:text-slate-400">
                    {{ __('messages.companies.showing') }}
                    {{ $companies->firstItem() ?? 0 }}–{{ $companies->lastItem() ?? 0 }}
                    {{ __('messages.companies.of') }}
                    {{ number_format($companies->total()) }}
                </span>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse ($companies as $company)
                        @php
                            $initial = strtoupper(substr($company->name, 0, 1));
                            $hasWebsite = $company->website !== null && $company->website !== '';
                        @endphp
                        <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-neutral-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-xl hover:shadow-brand/5 dark:hover:shadow-sky-500/5 hover:-translate-y-1 transition-all duration-300 flex flex-col relative overflow-hidden">
                            <div class="h-1.5 w-full bg-gradient-to-r from-brand via-brand-strong to-sky-400 dark:from-sky-400 dark:via-brand dark:to-sky-300"></div>

                            {{-- Card Header --}}
                            <div class="bg-gradient-to-br from-brand/5 to-sky-50 dark:from-brand/10 dark:to-slate-700/70 p-5 border-b border-neutral-200/60 dark:border-slate-700/60">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="shrink-0 w-10 h-10 rounded-full bg-brand/15 dark:bg-brand/25 flex items-center justify-center text-brand font-bold text-base shadow-sm">
                                        {{ $initial }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('companies.show', $company) }}" wire:navigate class="block group/title">
                                            <h3 class="text-lg sm:text-xl font-bold text-heading dark:text-white group-hover/title:text-brand dark:group-hover/title:text-sky-400 transition-colors duration-200 leading-snug line-clamp-2">
                                                {{ $company->name }}
                                            </h3>
                                            @if ($company->name_ar && app()->getLocale() === 'ar')
                                                <span class="block text-xs font-medium text-body/70 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $company->name_ar }}</span>
                                            @endif
                                        </a>
                                    </div>
                                </div>

                                {{-- Type badge --}}
                                @php
                                    $typeDotStyles = [
                                        'manufacturer' => 'bg-success',
                                        'agent' => 'bg-brand',
                                        'distributor' => 'bg-danger',
                                        'marketing' => 'bg-body',
                                    ];
                                @endphp
                                <div class="flex items-center gap-1.5 mt-2 pt-2 border-t border-brand/10 dark:border-slate-700/60">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $typeDotStyles[$company->company_type] ?? 'bg-brand' }}"></span>
                                    <span class="text-xs font-medium text-body/80 dark:text-slate-400">{{ __('messages.companies.types.' . $company->company_type) }}</span>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-5 flex-1 space-y-3.5 bg-white dark:bg-slate-800">
                                @if ($company->country)
                                    <div class="flex items-center gap-3 text-sm text-body dark:text-slate-300 group/detail">
                                        <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-800/40 flex items-center justify-center shrink-0 group-hover/detail:bg-amber-100 dark:group-hover/detail:bg-amber-900/50 transition-colors">
                                            <x-lucide-map-pin class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                        </span>
                                        <span class="font-medium text-heading dark:text-slate-200 text-xs sm:text-sm">{{ $company->country }}</span>
                                    </div>
                                @endif

                                <div class="flex items-center gap-3 text-sm text-body dark:text-slate-300 group/detail">
                                    <span class="w-8 h-8 rounded-xl bg-brand/10 dark:bg-brand/20 flex items-center justify-center shrink-0 group-hover/detail:bg-brand/20 dark:group-hover/detail:bg-brand/30 transition-colors">
                                        <x-lucide-package class="w-4 h-4 text-brand dark:text-sky-400" />
                                    </span>
                                    <span class="font-medium text-heading dark:text-slate-200 text-xs sm:text-sm">{{ $company->products_count }} {{ __('messages.companies.products_count') }}</span>
                                </div>

                                @if ($hasWebsite)
                                    <div class="flex items-center gap-3 text-sm text-body dark:text-slate-300 group/detail">
                                        <span class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-800/40 flex items-center justify-center shrink-0 group-hover/detail:bg-sky-100 dark:group-hover/detail:bg-sky-900/50 transition-colors">
                                            <x-lucide-globe class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                                        </span>
                                        <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="font-medium text-fg-brand hover:underline truncate text-xs sm:text-sm">
                                            {{ parse_url($company->website, PHP_URL_HOST) ?: $company->website }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Action Footer --}}
                            <div class="px-5 py-3.5 bg-neutral-50/70 dark:bg-slate-800/90 border-t border-neutral-100 dark:border-slate-700/80 flex items-center justify-between mt-auto">
                                <a href="{{ route('companies.show', $company) }}" wire:navigate
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-brand hover:text-white bg-brand/10 hover:bg-brand dark:bg-sky-400/10 dark:text-sky-400 dark:hover:bg-sky-500 dark:hover:text-white transition-all duration-200 shadow-2xs group/action">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    <span>{{ __('messages.companies.details') }}</span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 group-hover/action:translate-x-0.5 transition-transform" />
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <x-lucide-building-2 class="w-12 h-12 text-body dark:text-slate-400 mx-auto mb-4" />
                            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.companies.no_companies') }}</h3>
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.companies.try_another') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($companies->hasPages())
            @php
                $currentPage = $companies->currentPage();
                $lastPage = $companies->lastPage();
                $window = 2;
                $startPage = max(1, $currentPage - $window);
                $endPage = min($lastPage, $currentPage + $window);
                $showStartEllipsis = $startPage > 2;
                $showEndEllipsis = $endPage < $lastPage - 1;
            @endphp
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4">
                <div class="hidden sm:block text-sm text-body dark:text-slate-400">
                    {{ __('messages.companies.showing') }}
                    {{ $companies->firstItem() ?? 0 }}–{{ $companies->lastItem() ?? 0 }}
                    {{ __('messages.companies.of') }}
                    {{ number_format($companies->total()) }}
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ $companies->previousPageUrl() }}" wire:navigate rel="prev" @if($companies->onFirstPage()) aria-disabled="true" @endif
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft @if($companies->onFirstPage()) opacity-40 cursor-not-allowed pointer-events-none @else @endif dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                        <x-lucide-chevron-left class="w-4 h-4 rtl:rotate-180" />
                        <span>{{ __('messages.companies.previous_page') }}</span>
                    </a>

                    <div class="hidden sm:flex items-center gap-1">
                        @if ($startPage > 1)
                            <a href="{{ $companies->withQueryString()->url(1) }}" wire:navigate
                                class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                1
                            </a>
                        @endif

                        @if ($showStartEllipsis)
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-body dark:text-slate-400">...</span>
                        @endif

                        @for ($i = $startPage; $i <= $endPage; $i++)
                            <a href="{{ $companies->withQueryString()->url($i) }}" wire:navigate
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
                            <a href="{{ $companies->withQueryString()->url($lastPage) }}" wire:navigate
                                class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                                {{ $lastPage }}
                            </a>
                        @endif
                    </div>

                    <a href="{{ $companies->nextPageUrl() }}" wire:navigate rel="next" @if(!$companies->hasMorePages()) aria-disabled="true" @endif
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft @if(!$companies->hasMorePages()) opacity-40 cursor-not-allowed pointer-events-none @else @endif dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                        <span>{{ __('messages.companies.next_page') }}</span>
                        <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    </a>
                </div>
            </div>
        @endif

    </div>
@endsection
