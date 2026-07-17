@extends('app.layouts.master')

@section('title', __('messages.landing.title'))

@section('meta_description', __('messages.landing.meta_description'))

@section('meta_keywords', 'veterinary drugs, veterinary pharmaceuticals, animal health, veterinary medicine')

@section('css')
<style>
    .hero-grid-bg {
        background-image:
            linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
        background-size: 60px 60px;
    }
    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(-3deg);
    }
    .pulse-ring {
        animation: pulse-ring 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-ring {
        0%, 100% { opacity: .3; transform: scale(1); }
        50% { opacity: .1; transform: scale(1.05); }
    }
</style>
@endsection

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-slate-900 dark:bg-black rounded-base">
        <div class="hero-grid-bg absolute inset-0"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-slate-900/80 dark:to-black/80"></div>
        <div class="absolute -top-40 -end-40 w-80 h-80 rounded-full bg-brand/10 blur-3xl pulse-ring"></div>
        <div class="absolute -bottom-40 -start-40 w-80 h-80 rounded-full bg-sky-500/10 blur-3xl pulse-ring" style="animation-delay: 1.5s"></div>

        <div class="relative px-6 py-20 sm:py-28 md:py-36">
            <div class="max-w-6xl mx-auto">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-white/80 text-xs font-medium tracking-wider uppercase mb-8 border border-white/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ __('messages.landing.badge') }}
                    </div>

                    <h1 class="text-white font-bold leading-[1.05] tracking-tight mb-6"
                        style="font-size: clamp(2.2rem, 7vw, 5rem);">
                        {{ __('messages.landing.heading') }}
                    </h1>

                    <p class="text-white/60 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                        {{ __('messages.landing.subtitle') }}
                    </p>

                    <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto mb-10">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-brand/40 via-sky-500/40 to-brand/40 rounded-2xl blur-xl opacity-40 group-focus-within:opacity-70 transition-opacity duration-500"></div>
                            <div class="relative flex items-center bg-slate-800 dark:bg-slate-900 rounded-2xl border border-white/10 focus-within:border-brand/50 transition-all duration-300 shadow-2xl">
                                <div class="ps-5">
                                    <x-lucide-search class="w-5 h-5 text-white/40" />
                                </div>
                                <input type="text" name="q" autocomplete="off"
                                    class="w-full bg-transparent text-white text-base px-4 py-4 focus:outline-none placeholder:text-white/30 border-0 ring-0"
                                    placeholder="{{ __('messages.landing.search_placeholder') }}">
                                <div class="pe-2">
                                    <button type="submit"
                                        class="px-5 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong rounded-xl transition-all duration-200 shadow-lg shadow-brand/25">
                                        {{ __('messages.landing.search_button') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-3">
                        <div class="flex items-center gap-2 text-white/50">
                            <x-lucide-package class="w-4 h-4 text-brand" />
                            <span class="text-white font-bold tabular-nums text-lg">{{ $productsCount }}</span>
                            <span class="text-white/50 text-sm">{{ __('messages.landing.products_badge') }}</span>
                        </div>
                        <div class="w-px h-6 bg-white/10 hidden sm:block"></div>
                        <div class="flex items-center gap-2 text-white/50">
                            <x-lucide-activity class="w-4 h-4 text-emerald-400" />
                            <span class="text-white font-bold tabular-nums text-lg">{{ $diseasesCount }}</span>
                            <span class="text-white/50 text-sm">{{ __('messages.landing.diseases_badge') }}</span>
                        </div>
                        <div class="w-px h-6 bg-white/10 hidden sm:block"></div>
                        <div class="flex items-center gap-2 text-white/50">
                            <x-lucide-flask-conical class="w-4 h-4 text-violet-400" />
                            <span class="text-white font-bold tabular-nums text-lg">{{ $ingredientsCount }}</span>
                            <span class="text-white/50 text-sm">{{ __('messages.landing.ingredients_badge') }}</span>
                        </div>
                        <div class="w-px h-6 bg-white/10 hidden sm:block"></div>
                        <div class="flex items-center gap-2 text-white/50">
                            <x-lucide-building-2 class="w-4 h-4 text-amber-400" />
                            <span class="text-white font-bold tabular-nums text-lg">{{ $companiesCount }}</span>
                            <span class="text-white/50 text-sm">{{ __('messages.landing.companies_badge') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="mt-16 sm:mt-24 mb-6">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-xs font-semibold tracking-[.2em] uppercase text-fg-brand dark:text-brand">Platform</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-3 mb-3 tracking-tight">
                    {{ __('messages.landing.features_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 max-w-xl mx-auto">
                    {{ __('messages.landing.features_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <a href="{{ route('products.index') }}"
                    class="feature-card group relative p-8 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute -top-10 -end-10 w-32 h-32 rounded-full bg-brand/5 dark:bg-brand/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-brand to-brand-strong flex items-center justify-center mb-5 transition-transform duration-500 shadow-lg shadow-brand/20">
                            <x-lucide-package class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_products_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed">
                            {{ __('messages.landing.feature_products_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-fg-brand dark:text-brand opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('diseases.index') }}"
                    class="feature-card group relative p-8 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute -top-10 -end-10 w-32 h-32 rounded-full bg-emerald-500/5 dark:bg-emerald-500/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-5 transition-transform duration-500 shadow-lg shadow-emerald-500/20">
                            <x-lucide-activity class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_diseases_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed">
                            {{ __('messages.landing.feature_diseases_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-emerald-500 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('active-ingredients.index') }}"
                    class="feature-card group relative p-8 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute -top-10 -end-10 w-32 h-32 rounded-full bg-violet-500/5 dark:bg-violet-500/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mb-5 transition-transform duration-500 shadow-lg shadow-violet-500/20">
                            <x-lucide-flask-conical class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_ingredients_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed">
                            {{ __('messages.landing.feature_ingredients_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-violet-500 dark:text-violet-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('companies.index') }}"
                    class="feature-card group relative p-8 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl overflow-hidden transition-all duration-500 hover:shadow-xl hover:-translate-y-1">
                    <div class="absolute -top-10 -end-10 w-32 h-32 rounded-full bg-amber-500/5 dark:bg-amber-500/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="relative">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mb-5 transition-transform duration-500 shadow-lg shadow-amber-500/20">
                            <x-lucide-building-2 class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_companies_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed">
                            {{ __('messages.landing.feature_companies_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-amber-500 dark:text-amber-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="mb-6 mt-10">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="group relative bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 overflow-hidden hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                    <div class="h-1.5 bg-gradient-to-r from-brand to-sky-400"></div>
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 rounded-xl bg-brand/10 text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center mx-auto mb-3">
                            <x-lucide-package class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $productsCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-1.5 font-medium">
                            <span class="md:hidden">منتج</span>
                            <span class="hidden md:inline">{{ __('messages.landing.products_badge') }}</span>
                        </div>
                    </div>
                </div>
                <div class="group relative bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 overflow-hidden hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                    <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center mx-auto mb-3">
                            <x-lucide-activity class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $diseasesCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-1.5 font-medium">
                            <span class="md:hidden">مرض</span>
                            <span class="hidden md:inline">{{ __('messages.landing.diseases_badge') }}</span>
                        </div>
                    </div>
                </div>
                <div class="group relative bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 overflow-hidden hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                    <div class="h-1.5 bg-gradient-to-r from-violet-500 to-violet-300"></div>
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-500/20 dark:text-violet-400 flex items-center justify-center mx-auto mb-3">
                            <x-lucide-flask-conical class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $ingredientsCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-1.5 font-medium">
                            <span class="md:hidden">مكون</span>
                            <span class="hidden md:inline">{{ __('messages.landing.ingredients_badge') }}</span>
                        </div>
                    </div>
                </div>
                <div class="group relative bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 overflow-hidden hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
                    <div class="h-1.5 bg-gradient-to-r from-amber-500 to-amber-300"></div>
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center mx-auto mb-3">
                            <x-lucide-building-2 class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $companiesCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-1.5 font-medium">
                            <span class="md:hidden">شركة</span>
                            <span class="hidden md:inline">{{ __('messages.landing.companies_badge') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="mb-6 mt-16 sm:mt-24">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-xs font-semibold tracking-[.2em] uppercase text-fg-brand dark:text-brand">Process</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-3 mb-3 tracking-tight">
                    {{ __('messages.landing.how_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 max-w-xl mx-auto">
                    {{ __('messages.landing.how_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand to-brand-strong flex items-center justify-center mx-auto mb-5 shadow-lg shadow-brand/20">
                        <x-lucide-search class="w-7 h-7 text-white" />
                    </div>
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-brand/10 text-fg-brand dark:bg-brand/20 dark:text-brand text-sm font-bold mb-3">1</div>
                    <h3 class="text-xl font-bold text-heading dark:text-white mb-2">{{ __('messages.landing.step1_title') }}</h3>
                    <p class="text-body dark:text-slate-400 leading-relaxed text-sm max-w-xs mx-auto">{{ __('messages.landing.step1_desc') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/20">
                        <x-lucide-book-open class="w-7 h-7 text-white" />
                    </div>
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-500 dark:bg-emerald-500/20 dark:text-emerald-400 text-sm font-bold mb-3">2</div>
                    <h3 class="text-xl font-bold text-heading dark:text-white mb-2">{{ __('messages.landing.step2_title') }}</h3>
                    <p class="text-body dark:text-slate-400 leading-relaxed text-sm max-w-xs mx-auto">{{ __('messages.landing.step2_desc') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mx-auto mb-5 shadow-lg shadow-violet-500/20">
                        <x-lucide-check-circle class="w-7 h-7 text-white" />
                    </div>
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-violet-500/10 text-violet-500 dark:bg-violet-500/20 dark:text-violet-400 text-sm font-bold mb-3">3</div>
                    <h3 class="text-xl font-bold text-heading dark:text-white mb-2">{{ __('messages.landing.step3_title') }}</h3>
                    <p class="text-body dark:text-slate-400 leading-relaxed text-sm max-w-xs mx-auto">{{ __('messages.landing.step3_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Browse by Category --}}
    <section class="mb-6 mt-16 sm:mt-24">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="text-xs font-semibold tracking-[.2em] uppercase text-fg-brand dark:text-brand">Categories</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-3 mb-3 tracking-tight">
                    {{ __('messages.landing.browse_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 max-w-xl mx-auto">
                    {{ __('messages.landing.browse_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('products.index') }}"
                    class="group relative p-6 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 hover:border-brand/40 dark:hover:border-brand/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand to-brand-strong flex items-center justify-center mb-4 shadow-sm">
                        <x-lucide-package class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('messages.landing.browse_products') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('messages.landing.browse_products_desc') }}</p>
                </a>

                <a href="{{ route('diseases.index') }}"
                    class="group relative p-6 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 hover:border-emerald-400/40 dark:hover:border-emerald-400/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-4 shadow-sm">
                        <x-lucide-activity class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('messages.landing.browse_diseases') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('messages.landing.browse_diseases_desc') }}</p>
                </a>

                <a href="{{ route('active-ingredients.index') }}"
                    class="group relative p-6 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 hover:border-violet-400/40 dark:hover:border-violet-400/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mb-4 shadow-sm">
                        <x-lucide-flask-conical class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('messages.landing.browse_ingredients') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('messages.landing.browse_ingredients_desc') }}</p>
                </a>

                <a href="{{ route('companies.index') }}"
                    class="group relative p-6 bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 hover:border-amber-400/40 dark:hover:border-amber-400/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mb-4 shadow-sm">
                        <x-lucide-building-2 class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('messages.landing.browse_companies') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400">{{ __('messages.landing.browse_companies_desc') }}</p>
                </a>
            </div>
        </div>
    </section>

    {{-- Why VetPedia --}}
    <section class="mb-6 mt-16 sm:mt-24">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div>
                    <span class="text-xs font-semibold tracking-[.2em] uppercase text-fg-brand dark:text-brand">Why us</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-3 mb-3 tracking-tight">
                        {{ __('messages.landing.why_heading') }}
                    </h2>
                    <p class="text-body dark:text-slate-400 mb-8">
                        {{ __('messages.landing.why_subtitle') }}
                    </p>

                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-brand/10 text-fg-brand dark:bg-brand/20 dark:text-brand flex items-center justify-center shrink-0 mt-0.5">
                                <x-lucide-database class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item1_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-0.5">{{ __('messages.landing.why_item1_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-500 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <x-lucide-stethoscope class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item2_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-0.5">{{ __('messages.landing.why_item2_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-500 dark:bg-violet-500/20 dark:text-violet-400 flex items-center justify-center shrink-0 mt-0.5">
                                <x-lucide-file-text class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item3_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-0.5">{{ __('messages.landing.why_item3_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                                <x-lucide-languages class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item4_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-0.5">{{ __('messages.landing.why_item4_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 p-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 rounded-xl bg-neutral-secondary-soft dark:bg-slate-900">
                            <div class="text-3xl font-extrabold text-heading dark:text-white tabular-nums">{{ $dosageFormsCount }}</div>
                            <div class="text-xs text-body dark:text-slate-400 mt-1">{{ __('messages.landing.dosage_forms_badge') }}</div>
                        </div>
                        <div class="text-center p-4 rounded-xl bg-neutral-secondary-soft dark:bg-slate-900">
                            <div class="text-3xl font-extrabold text-heading dark:text-white tabular-nums">{{ $speciesCount }}</div>
                            <div class="text-xs text-body dark:text-slate-400 mt-1">{{ __('messages.landing.species_badge') }}</div>
                        </div>
                        <div class="text-center p-4 rounded-xl bg-neutral-secondary-soft dark:bg-slate-900">
                            <div class="text-3xl font-extrabold text-heading dark:text-white tabular-nums">{{ $ingredientsCount }}</div>
                            <div class="text-xs text-body dark:text-slate-400 mt-1">{{ __('messages.landing.ingredients_badge') }}</div>
                        </div>
                        <div class="text-center p-4 rounded-xl bg-neutral-secondary-soft dark:bg-slate-900">
                            <div class="text-3xl font-extrabold text-heading dark:text-white tabular-nums">{{ $productsCount }}</div>
                            <div class="text-xs text-body dark:text-slate-400 mt-1">{{ __('messages.landing.products_badge') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Blog --}}
    @if ($latestBlogs->count())
        <section class="mb-6 mt-16 sm:mt-24">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <span class="text-xs font-semibold tracking-[.2em] uppercase text-fg-brand dark:text-brand">Blog</span>
                        <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-3 tracking-tight">
                            {{ __('messages.landing.latest_blog') }}
                        </h2>
                    </div>
                    <a href="{{ route('blog.index') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-fg-brand dark:text-brand border border-brand/30 dark:border-brand/30 rounded-xl hover:bg-brand-soft dark:hover:bg-brand/10 transition-all duration-200">
                        {{ __('messages.landing.view_all') }}
                        <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($latestBlogs as $blog)
                        @php $locale = app()->getLocale(); @endphp
                        <a href="{{ route('blog.show', $blog) }}"
                            class="group bg-neutral-primary-soft dark:bg-slate-800 rounded-2xl border border-default-medium dark:border-slate-700 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div class="h-44 bg-neutral-secondary-soft dark:bg-slate-700 overflow-hidden">
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
                            <div class="p-5">
                                @if ($blog->category)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-xl bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand mb-3">
                                        {{ $locale === 'ar' && $blog->category->name_ar ? $blog->category->name_ar : $blog->category->name }}
                                    </span>
                                @endif
                                <h3 class="text-base font-bold text-heading dark:text-white group-hover:text-fg-brand dark:group-hover:text-brand line-clamp-2 transition-colors duration-150">
                                    {{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}
                                </h3>
                                <p class="text-xs text-body dark:text-slate-400 mt-2 flex items-center gap-1.5">
                                    <x-lucide-calendar class="w-3 h-3" />
                                    {{ $blog->published_at->format('M d, Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="text-center mt-8 sm:hidden">
                    <a href="{{ route('blog.index') }}"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium text-fg-brand dark:text-brand border border-brand/30 dark:border-brand/30 rounded-xl hover:bg-brand-soft dark:hover:bg-brand/10 transition-all duration-200">
                        {{ __('messages.landing.view_all') }}
                        <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="mb-6 mt-16 sm:mt-24">
        <div class="max-w-6xl mx-auto px-4">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 dark:from-black dark:via-slate-900 dark:to-black px-8 py-14 sm:py-16 text-center">
                <div class="absolute inset-0 hero-grid-bg opacity-30"></div>
                <div class="absolute -top-20 -start-20 w-60 h-60 rounded-full bg-brand/5 blur-3xl"></div>
                <div class="absolute -bottom-20 -end-20 w-60 h-60 rounded-full bg-sky-500/5 blur-3xl"></div>
                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-3 tracking-tight">
                        {{ __('messages.landing.cta_heading') }}
                    </h2>
                    <p class="text-white/60 max-w-md mx-auto mb-8">
                        {{ __('messages.landing.cta_subtitle') }}
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-brand hover:bg-brand-strong rounded-xl transition-all duration-200 shadow-lg shadow-brand/25">
                            <x-lucide-package class="w-4 h-4" />
                            {{ __('messages.landing.cta_browse') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white/80 bg-white/10 hover:bg-white/20 rounded-xl border border-white/10 transition-all duration-200">
                                <x-lucide-user-plus class="w-4 h-4" />
                                {{ __('messages.landing.cta_register') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
