@extends('app.layouts.master')

@section('title', __('messages.companies.index_title'))

@section('content')
    <div class="space-y-4">

        {{-- Header --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-heading dark:text-white">{{ __('messages.companies.index_heading') }}</h1>
                    <p class="text-body dark:text-gray-400 text-base font-semibold mt-1">{{ __('messages.companies.index_subtitle') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-base text-sm font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                        <x-lucide-building-2 class="w-3.5 h-3.5" />
                        {{ number_format($companies->total()) }} {{ __('messages.companies.companies_label') }}
                    </span>
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
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="{{ __('messages.companies.search_placeholder') }}">
                    </div>
                    <div class="flex justify-between gap-2">
                        <button type="submit"
                            class="text-white w-50 bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base  text-sm px-4 py-2.5 focus:outline-none">
                            {{ __('messages.companies.search_button') }}
                        </button>
                        <a href="{{ route('companies.index') }}"
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
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.directory_title') }}</h2>
                <span class="text-base text-body dark:text-gray-400">
                    {{ __('messages.companies.showing') }}
                    {{ $companies->firstItem() ?? 0 }}–{{ $companies->lastItem() ?? 0 }}
                    {{ __('messages.companies.of') }}
                    {{ number_format($companies->total()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-base text-left rtl:text-right text-heading dark:text-white">
                    <thead class="text-base uppercase text-body bg-neutral-secondary-soft dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-5 py-3">{{ __('messages.companies.name') }}</th>
                            <th scope="col" class="px-5 py-3 hidden sm:table-cell">{{ __('messages.companies.type') }}</th>
                            <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.companies.country') }}</th>
                            <th scope="col" class="px-5 py-3">{{ __('messages.companies.products_count') }}</th>
                            <th scope="col" class="px-5 py-3 w-20">{{ __('messages.companies.details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr class="border-b border-default-medium hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 dark:border-gray-700">
                                <td class="px-5 py-4">
                                    <a href="{{ route('companies.show', $company) }}" class="font-semibold text-fg-brand hover:underline">
                                        {{ $company->name }}
                                    </a>
                                    @if ($company->name_ar && app()->getLocale() === 'ar')
                                        <p class="text-sm text-body dark:text-gray-400 mt-0.5 font-semibold">{{ $company->name_ar }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 hidden sm:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded text-sm font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand capitalize">
                                        {{ __('messages.companies.types.' . $company->company_type) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 hidden md:table-cell text-body dark:text-gray-400 text-sm font-semibold">
                                    {{ $company->country ?? __('messages.companies.na') }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 text-sm font-semibold bg-neutral-secondary-soft text-heading border border-default-medium rounded-base dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                        {{ $company->products_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('companies.show', $company) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm">
                                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <x-lucide-building-2 class="w-10 h-10 text-body mx-auto mb-3" />
                                    <h3 class="text-base font-semibold text-heading dark:text-white mb-1">{{ __('messages.companies.no_companies') }}</h3>
                                    <p class="text-base text-body dark:text-gray-400">{{ __('messages.companies.try_another') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($companies->hasPages())
            <div class="w-full">
                {{ $companies->withQueryString()->links('pagination::tailwind') }}
            </div>
        @endif

    </div>
@endsection
