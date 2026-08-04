@extends('app.layouts.master')

@section('title', __('messages.search.page_title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-page-hero
            :heading="__('messages.search.heading')"
            :subtitle="__('messages.search.subtitle')"
            :badge="__('messages.nav.search')"
            badgeIcon="search"
        >
            <form method="GET">
                <div class="relative w-full sm:w-1/2">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="text" name="q" value="{{ $q }}"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 pe-32 px-3 py-3 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        placeholder="{{ __('messages.search.placeholder') }}">
                    <button type="submit"
                        class="absolute inset-y-1 end-1 inline-flex items-center justify-center gap-1.5 px-4 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base focus:outline-none">
                        <x-lucide-search class="w-4 h-4" />
                        {{ __('messages.search.button') }}
                    </button>
                </div>
            </form>
        </x-page-hero>

        @if ($q)

            {{-- Results heading --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs px-5 py-4 dark:bg-slate-800">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.results_for', ['q' => $q]) }}</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Products --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-package class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.products') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $products->total() }}</span>
                    </div>
                    @if ($products->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right">
                                <thead class="text-xs uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700 dark:text-slate-400">
                                    <tr>
                                        <th scope="col" class="px-5 py-3">{{ __('messages.search.trade_name') }}</th>
                                        <th scope="col" class="px-5 py-3">{{ __('messages.search.manufacturer') }}</th>
                                        <th scope="col" class="px-5 py-3">{{ __('messages.search.type') }}</th>
                                        <th scope="col" class="px-5 py-3 w-20">{{ __('messages.search.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $item)
                                        <tr class="border-b border-default-medium dark:border-slate-700 last:border-0">
                                            <td class="px-5 py-3">
                                                <a href="{{ route('products.show', $item) }}" wire:navigate class="font-medium text-fg-brand hover:underline">
                                                    {{ $item->trade_name }}
                                                </a>
                                            </td>
                                            <td class="px-5 py-3 text-body dark:text-slate-400">
                                                {{ $item->manufacturer->first()?->name ?? '—' }}
                                            </td>
                                            <td class="px-5 py-3 text-body dark:text-slate-400">
                                                @if ($item->product_type)
                                                    {{ __('messages.products.types.' . $item->product_type) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-5 py-3">
                                                <a href="{{ route('products.show', $item) }}" wire:navigate
                                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm">
                                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <x-pagination :paginator="$products" translation-prefix="messages.search" simple border-top />
                    @else
                        <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                    @endif
                </div>

                {{-- Companies --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-building-2 class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.companies') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $companies->total() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse($companies as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('companies.show', $item) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('companies.show', $item) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                        @endforelse
                    </div>
                    <x-pagination :paginator="$companies" translation-prefix="messages.search" simple border-top />
                </div>

                {{-- Diseases --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-activity class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.diseases') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $diseases->total() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse($diseases as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('diseases.show', $item) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('diseases.show', $item) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                        @endforelse
                    </div>
                    <x-pagination :paginator="$diseases" translation-prefix="messages.search" simple border-top />
                </div>

                {{-- Active Ingredients --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-flask-conical class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.search.ingredients') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $ingredients->total() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse($ingredients as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('active-ingredients.show', $item) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('active-ingredients.show', $item) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.search.no_results') }}</p>
                        @endforelse
                    </div>
                    <x-pagination :paginator="$ingredients" translation-prefix="messages.search" simple border-top />
                </div>

            </div>

        @else

            {{-- No query yet --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-search class="w-12 h-12 text-body mx-auto mb-4" />
                <h2 class="text-base font-semibold text-heading dark:text-white mb-1">{{ __('messages.search.heading') }}</h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('messages.search.no_query') }}</p>
            </div>

        @endif

    </div>
@endsection
