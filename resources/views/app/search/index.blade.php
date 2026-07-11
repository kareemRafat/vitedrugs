@extends('app.layouts.master')

@section('title', __('messages.search.page_title'))

@section('content')
    <div class="space-y-4">

        {{-- Header --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-heading dark:text-white">{{ __('messages.search.heading') }}</h1>
                    <p class="text-body dark:text-gray-400 text-base font-semibold mt-1">{{ __('messages.search.subtitle') }}</p>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative sm:col-span-2">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-body" />
                        </div>
                        <input type="text" name="q" value="{{ $q }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="{{ __('messages.search.placeholder') }}">
                    </div>
                    <div class="flex justify-between gap-2">
                        <button type="submit"
                            class="w-50 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            {{ __('messages.search.button') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($q)

            {{-- Results heading --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs px-5 py-4 dark:bg-gray-800">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.results_for', ['q' => $q]) }}</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Products --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-package class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.products') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $products->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse($products as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('products.show', $item) }}" class="text-base font-medium text-fg-brand hover:underline">{{ $item->trade_name }}</a>
                                <a href="{{ route('products.show', $item) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-gray-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Companies --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-building-2 class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.companies') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $companies->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse($companies as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('companies.show', $item) }}" class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('companies.show', $item) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-gray-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Diseases --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-activity class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.diseases') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $diseases->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse($diseases as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('diseases.show', $item) }}" class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('diseases.show', $item) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-gray-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Active Ingredients --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-flask-conical class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.ingredients') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $ingredients->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse($ingredients as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('active-ingredients.show', $item) }}" class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('active-ingredients.show', $item) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-gray-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                        @endforelse
                    </div>
                </div>

            </div>

        @else

            {{-- No query yet --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-gray-800">
                <x-lucide-search class="w-12 h-12 text-body mx-auto mb-4" />
                <h2 class="text-base font-semibold text-heading dark:text-white mb-1">{{ __('messages.search.heading') }}</h2>
                <p class="text-base text-body dark:text-gray-400">{{ __('messages.search.no_query') }}</p>
            </div>

        @endif

    </div>
@endsection
