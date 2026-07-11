@extends('app.layouts.master')

@section('content')
    {{-- Hero / Search Section --}}
    <div class="p-6 mb-6 bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800">
        <div class="text-center py-4 sm:py-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mb-3">
                VetPedia
            </h1>
            <p class="text-body dark:text-gray-400 text-base sm:text-lg mb-6 max-w-xl mx-auto">
                {{ __('messages.home.hero_subtitle') }}
            </p>

            <form action="{{ route('search') }}" method="GET" class="max-w-lg mx-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-gray-400" />
                    </div>
                    <input type="text" name="q"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand"
                        placeholder="{{ __('messages.home.search_placeholder') }}">
                    <button type="submit"
                        class="absolute end-1.5 top-1/2 -translate-y-1/2 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-1.5 focus:outline-none">
                        {{ __('messages.home.search_button') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('products.index') }}"
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-gray-800 dark:hover:bg-gray-700 group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                    <x-lucide-package class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $productsCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.products_title') }}</h3>
            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.home.products_desc') }}</p>
        </a>

        <a href="{{ route('companies.index') }}"
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-gray-800 dark:hover:bg-gray-700 group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-success-soft text-fg-success-strong dark:bg-success/20 dark:text-success">
                    <x-lucide-building-2 class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $companiesCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.companies_title') }}</h3>
            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.home.companies_desc') }}</p>
        </a>

        <a href="{{ route('diseases.index') }}"
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-gray-800 dark:hover:bg-gray-700 group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-danger-soft text-fg-danger-strong dark:bg-danger/20 dark:text-danger">
                    <x-lucide-activity class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $diseasesCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.diseases_title') }}</h3>
            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.home.diseases_desc') }}</p>
        </a>

        <a href="{{ route('active-ingredients.index') }}"
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-gray-800 dark:hover:bg-gray-700 group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                    <x-lucide-flask-conical class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $ingredientsCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.ingredients_title') }}</h3>
            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.home.ingredients_desc') }}</p>
        </a>
    </div>

    {{-- Latest Products & Featured Companies --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Latest Products --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-default-medium dark:border-gray-700">
                <h4 class="text-lg font-semibold text-heading dark:text-white flex items-center gap-2">
                    <x-lucide-package class="w-5 h-5 text-fg-brand" />
                    {{ __('messages.home.latest_products') }}
                </h4>
                <a href="{{ route('products.index') }}"
                    class="text-sm font-medium text-fg-brand hover:underline">
                    {{ __('messages.home.view_all') }}
                </a>
            </div>
            <div class="p-5 space-y-3">
                @forelse($latestProducts as $product)
                    <a href="{{ route('products.show', $product) }}"
                        class="flex items-center gap-3 p-3 rounded-base border border-default-medium hover:bg-neutral-secondary-soft dark:border-gray-600 dark:hover:bg-gray-700 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-base shrink-0 bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                            <x-lucide-package class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-heading dark:text-white truncate">{{ $product->trade_name }}</p>
                            <p class="text-xs text-body dark:text-gray-400 truncate">{{ $product->dosageForm?->name ?? __('messages.home.dosage_form_na') }}</p>
                        </div>
                        <x-lucide-chevron-right class="w-4 h-4 text-body shrink-0 rtl:rotate-180" />
                    </a>
                @empty
                    <p class="text-center text-body dark:text-gray-400 py-4">{{ __('messages.home.no_products') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Featured Companies --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-default-medium dark:border-gray-700">
                <h4 class="text-lg font-semibold text-heading dark:text-white flex items-center gap-2">
                    <x-lucide-building-2 class="w-5 h-5 text-fg-brand" />
                    {{ __('messages.home.featured_companies') }}
                </h4>
                <a href="{{ route('companies.index') }}"
                    class="text-sm font-medium text-fg-brand hover:underline">
                    {{ __('messages.home.view_all') }}
                </a>
            </div>
            <div class="p-5 space-y-3">
                @forelse($latestCompanies as $company)
                    <a href="{{ route('companies.show', $company) }}"
                        class="flex items-center gap-3 p-3 rounded-base border border-default-medium hover:bg-neutral-secondary-soft dark:border-gray-600 dark:hover:bg-gray-700 transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-base shrink-0 bg-success-soft text-fg-success-strong dark:bg-success/20 dark:text-success">
                            <x-lucide-building-2 class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-heading dark:text-white truncate">{{ $company->name }}</p>
                            <p class="text-xs text-body dark:text-gray-400 truncate">{{ ucfirst($company->company_type ?? __('messages.home.company_type_fallback')) }}</p>
                        </div>
                        <x-lucide-chevron-right class="w-4 h-4 text-body shrink-0 rtl:rotate-180" />
                    </a>
                @empty
                    <p class="text-center text-body dark:text-gray-400 py-4">{{ __('messages.home.no_companies') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
