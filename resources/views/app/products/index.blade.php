@extends('app.layouts.master')

@section('title', __('messages.products.index_title'))

@section('content')
    <div class="space-y-4">

        {{-- Header --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-heading dark:text-white">{{ __('messages.products.index_heading') }}</h1>
                    <p class="text-body dark:text-gray-400 text-sm font-semibold mt-1">{{ __('messages.products.index_subtitle') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-base text-sm font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                        <x-lucide-package class="w-3.5 h-3.5" />
                        {{ number_format($products->total()) }} {{ __('messages.products.products_label') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-base text-sm font-medium bg-success-soft text-fg-success-strong dark:bg-success/20 dark:text-success">
                        <x-lucide-building-2 class="w-3.5 h-3.5" />
                        {{ $companies->count() }} {{ __('messages.products.companies_label') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-base text-sm font-medium bg-danger-soft text-fg-danger-strong dark:bg-danger/20 dark:text-danger">
                        <x-lucide-pill class="w-3.5 h-3.5" />
                        {{ $dosageForms->count() }} {{ __('messages.products.forms_label') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Search / Filters --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div class="lg:col-span-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-3 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="{{ __('messages.products.search_placeholder') }}">
                    </div>

                    <select name="company"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">{{ __('messages.products.all_companies') }}</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(request('company') == $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>

                    <select name="dosage_form"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">{{ __('messages.products.all_forms') }}</option>
                        @foreach ($dosageForms as $form)
                            <option value="{{ $form->id }}" @selected(request('dosage_form') == $form->id)>{{ $form->name }}</option>
                        @endforeach
                    </select>

                    <select name="sort"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="latest" @selected(request('sort') == 'latest')>{{ __('messages.products.sort_latest') }}</option>
                        <option value="name" @selected(request('sort') == 'name')>{{ __('messages.products.sort_name') }}</option>
                        <option value="oldest" @selected(request('sort') == 'oldest')>{{ __('messages.products.sort_oldest') }}</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            {{ __('messages.products.search_button') }}
                        </button>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                            <x-lucide-x class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Results Table --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-default-medium flex items-center justify-between">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.products.directory_title') }}</h2>
                <span class="text-sm text-body dark:text-gray-400">
                    {{ __('messages.products.showing') }}
                    {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                    {{ __('messages.products.of') }}
                    {{ number_format($products->total()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-heading dark:text-white">
                    <thead class="text-sm uppercase text-body bg-neutral-secondary-soft dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-5 py-3 w-16">#</th>
                            <th scope="col" class="px-5 py-3">{{ __('messages.products.trade_name') }}</th>
                            <th scope="col" class="px-5 py-3 hidden sm:table-cell">{{ __('messages.products.company') }}</th>
                            <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.products.dosage_form') }}</th>
                            <th scope="col" class="px-5 py-3 w-20">{{ __('messages.products.details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="border-b border-default-medium hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 dark:border-gray-700">
                                <td class="px-5 py-4 text-body dark:text-gray-400">
                                    {{ $products->firstItem() + $loop->index }}
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('products.show', $product) }}" class="font-semibold text-fg-brand hover:underline">
                                        {{ $product->trade_name }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 hidden sm:table-cell text-gray-800 font-semibold dark:text-gray-400">
                                    {{ $product->company?->name ?? __('messages.products.unknown') }}
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell text-body dark:text-gray-400">
                                    {{ $product->dosageForm?->name ?? __('messages.products.na') }}
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm">
                                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <x-lucide-package class="w-10 h-10 text-body mx-auto mb-3" />
                                    <h3 class="text-base font-semibold text-heading dark:text-white mb-1">{{ __('messages.products.no_products') }}</h3>
                                    <p class="text-sm text-body dark:text-gray-400">{{ __('messages.products.try_another') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="w-full">
                {{ $products->withQueryString()->links('pagination::tailwind') }}
            </div>
        @endif

    </div>
@endsection
