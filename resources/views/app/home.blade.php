@extends('app.layouts.master')

@section('content')
    {{-- Hero / Search Section --}}
    <div class="relative overflow-hidden mb-6 rounded-base shadow-xs bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 mt-8">
        <div class="absolute inset-0 opacity-10 dark:opacity-20">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white dark:bg-sky-200"></div>
            <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-white dark:bg-sky-200"></div>
        </div>
        <div class="relative px-6 py-16 sm:py-20 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold text-white mb-3 drop-shadow-sm">
                VetPedia
            </h1>
            <p class="text-white/90 text-base sm:text-lg mb-6 max-w-2xl mx-auto">
                {{ __('messages.home.hero_subtitle') }}
            </p>

            <form action="{{ route('search') }}" method="GET" class="max-w-lg mx-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="text" name="q"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand"
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
        <a href="{{ route('products.index') }}" wire:navigate
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-slate-800 dark:hover:bg-slate-700  group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                    <x-lucide-package class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $productsCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.products_title') }}</h3>
            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.home.products_desc') }}</p>
        </a>

        <a href="{{ route('companies.index') }}" wire:navigate
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-slate-800 dark:hover:bg-slate-700  group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-success-soft text-fg-success-strong dark:bg-success/20 dark:text-success">
                    <x-lucide-building-2 class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $companiesCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.companies_title') }}</h3>
            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.home.companies_desc') }}</p>
        </a>

        <a href="{{ route('diseases.index') }}" wire:navigate
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-slate-800 dark:hover:bg-slate-700  group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-danger-soft text-fg-danger-strong dark:bg-danger/20 dark:text-danger">
                    <x-lucide-activity class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $diseasesCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.diseases_title') }}</h3>
            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.home.diseases_desc') }}</p>
        </a>

        <a href="{{ route('active-ingredients.index') }}" wire:navigate
            class="block p-5 bg-neutral-primary-soft rounded-base shadow-xs hover:bg-neutral-secondary-soft transition-all duration-200 border border-transparent hover:border-default-medium dark:bg-slate-800 dark:hover:bg-slate-700  group">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                    <x-lucide-flask-conical class="w-5 h-5" />
                </div>
                <span class="text-2xl font-bold text-heading dark:text-white">{{ $ingredientsCount }}</span>
            </div>
            <h3 class="font-semibold text-heading dark:text-white mb-1">{{ __('messages.home.ingredients_title') }}</h3>
            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.home.ingredients_desc') }}</p>
        </a>
    </div>

    {{-- Latest Blog Posts --}}
            @if ($latestBlogs->count())
        <div class="mb-6">
            @php $locale = app()->getLocale(); @endphp
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-heading dark:text-white flex items-center gap-2">
                    <x-lucide-newspaper class="w-5 h-5 text-fg-brand" />
                    {{ __('messages.home.latest_blog') }}
                </h4>
                <a href="{{ route('blog.index') }}" wire:navigate
                    class="text-sm font-medium text-fg-brand hover:underline">
                    {{ __('messages.home.view_all') }}
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($latestBlogs as $blog)
                    <a href="{{ route('blog.show', $blog) }}" wire:navigate
                        class="group bg-neutral-primary-soft rounded-base border border-default-medium overflow-hidden hover:shadow-md transition-all duration-300 dark:bg-slate-800 dark:border-slate-700 ">
                        <div class="h-40 bg-neutral-secondary-soft dark:bg-slate-700 overflow-hidden">
                            @if ($blog->cover_image)
                                <img src="{{ Storage::url($blog->cover_image) }}"
                                    alt="{{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <x-lucide-newspaper class="w-10 h-10 text-body dark:text-slate-500" />
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            @if ($blog->category)
                                <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand mb-2">
                                    {{ $locale === 'ar' && $blog->category->name_ar ? $blog->category->name_ar : $blog->category->name }}
                                </span>
                            @endif
                            <h3 class="text-sm font-semibold text-heading dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 line-clamp-2 transition-colors duration-150">
                                {{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}
                            </h3>
                            <p class="text-xs text-body dark:text-slate-400 mt-1">
                                {{ $blog->published_at->format('M d, Y') }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Latest Products & Featured Companies --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Latest Products --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-default-medium dark:border-slate-700">
                <h4 class="text-lg font-semibold text-heading dark:text-white flex items-center gap-2">
                    <x-lucide-package class="w-5 h-5 text-fg-brand" />
                    {{ __('messages.home.latest_products') }}
                </h4>
                <a href="{{ route('products.index') }}" wire:navigate
                    class="text-sm font-medium text-fg-brand hover:underline">
                    {{ __('messages.home.view_all') }}
                </a>
            </div>
            <div class="p-5 space-y-3">
                @forelse($latestProducts as $product)
                    <a href="{{ route('products.show', $product) }}" wire:navigate
                        class="flex items-center gap-3 p-3 rounded-base border border-default-medium hover:bg-neutral-secondary-soft dark:border-slate-600 dark:hover:bg-slate-700  transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-base shrink-0 bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                            <x-lucide-package class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-heading dark:text-white truncate">{{ $product->trade_name }}</p>
                            <p class="text-xs text-body dark:text-slate-400 truncate">{{ $product->dosageForm?->name ?? __('messages.home.dosage_form_na') }}</p>
                        </div>
                        <x-lucide-chevron-right class="w-4 h-4 text-body shrink-0 rtl:rotate-180" />
                    </a>
                @empty
                    <p class="text-center text-body dark:text-slate-400 py-4">{{ __('messages.home.no_products') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Featured Companies --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-default-medium dark:border-slate-700">
                <h4 class="text-lg font-semibold text-heading dark:text-white flex items-center gap-2">
                    <x-lucide-building-2 class="w-5 h-5 text-fg-brand" />
                    {{ __('messages.home.featured_companies') }}
                </h4>
                <a href="{{ route('companies.index') }}" wire:navigate
                    class="text-sm font-medium text-fg-brand hover:underline">
                    {{ __('messages.home.view_all') }}
                </a>
            </div>
            <div class="p-5 space-y-3">
                @forelse($latestCompanies as $company)
                    <a href="{{ route('companies.show', $company) }}" wire:navigate
                        class="flex items-center gap-3 p-3 rounded-base border border-default-medium hover:bg-neutral-secondary-soft dark:border-slate-600 dark:hover:bg-slate-700  transition-all duration-200">
                        <div class="flex items-center justify-center w-10 h-10 rounded-base shrink-0 bg-success-soft text-fg-success-strong dark:bg-success/20 dark:text-success">
                            <x-lucide-building-2 class="w-5 h-5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-heading dark:text-white truncate">{{ $company->name }}</p>
                            <p class="text-xs text-body dark:text-slate-400 truncate">{{ $company->company_type ? __('messages.companies.types.' . $company->company_type) : __('messages.home.company_type_fallback') }}</p>
                        </div>
                        <x-lucide-chevron-right class="w-4 h-4 text-body shrink-0 rtl:rotate-180" />
                    </a>
                @empty
                    <p class="text-center text-body dark:text-slate-400 py-4">{{ __('messages.home.no_companies') }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
