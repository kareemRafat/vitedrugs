@extends('app.layouts.master')

@section('title', __('messages.landing.title'))

@section('meta_description', __('messages.landing.meta_description'))

@section('meta_keywords', 'veterinary drugs, veterinary pharmaceuticals, animal health, veterinary medicine')

@section('css')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.02); }
    }
    @keyframes float-slow {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-12px) scale(1.01); }
    }
    @keyframes pulse-glow {
        0%, 100% { opacity: .3; }
        50% { opacity: .6; }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (prefers-reduced-motion: reduce) {
        * { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
    }
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-float-slow { animation: float-slow 8s ease-in-out infinite; }
    .animate-pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }
    .animate-shimmer { background-size: 200% 100%; animation: shimmer 3s ease-in-out infinite; }
    .animate-slide-up { animation: slide-up 0.6s ease-out both; }
    .grid-bg {
        background-image:
            linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
        background-size: 48px 48px;
    }
    .card-shine {
        background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,.03) 50%, transparent 60%);
        background-size: 200% 200%;
    }
    .group:hover .card-shine {
        background-position: 100% 100%;
    }
</style>
@endsection

@section('content')
    {{-- ============================================ --}}
    {{-- HERO --}}
    {{-- ============================================ --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900 min-h-[60vh] sm:min-h-[90vh] flex items-center">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/bright-squares.png')] opacity-30 dark:opacity-10 pointer-events-none"></div>

        <div class="hidden sm:block absolute -top-48 -end-48 w-96 h-96 rounded-full bg-indigo-300/20 dark:bg-indigo-500/15 blur-3xl animate-float pointer-events-none"></div>
        <div class="hidden sm:block absolute -bottom-48 -start-48 w-96 h-96 rounded-full bg-sky-300/20 dark:bg-sky-500/15 blur-3xl animate-float-slow pointer-events-none" style="animation-delay: 2s"></div>
        <div class="hidden sm:block absolute top-1/3 start-1/4 w-64 h-64 rounded-full bg-emerald-300/15 dark:bg-emerald-500/10 blur-3xl animate-pulse-glow pointer-events-none"></div>
        <div class="hidden sm:block absolute top-1/4 end-1/3 w-72 h-72 rounded-full bg-violet-300/15 dark:bg-violet-500/10 blur-3xl animate-float pointer-events-none" style="animation-delay: 4s"></div>

        <div class="relative w-full px-4 sm:px-6 py-12 sm:py-32 md:py-40">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300 text-[10px] sm:text-xs font-medium tracking-widest uppercase mb-5 sm:mb-8 border border-indigo-200 dark:border-indigo-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                        {{ __('messages.landing.badge') }}
                    </div>

                    <h1 class="text-heading dark:text-white font-bold leading-[1.1] sm:leading-[1.05] tracking-tight mb-4 sm:mb-6"
                        style="font-size: clamp(1.75rem, 7vw, 4.5rem);">
                        {{ __('messages.landing.heading') }}
                    </h1>

                    <p class="text-body dark:text-slate-400 text-sm sm:text-lg md:text-xl max-w-2xl mx-auto mb-8 sm:mb-12 leading-relaxed px-2 sm:px-0">
                        {{ __('messages.landing.subtitle') }}
                    </p>

                    <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto mb-12 sm:mb-16">
                        <div class="relative flex items-center bg-white dark:bg-slate-800 rounded-full border border-default-medium dark:border-slate-600 focus-within:border-indigo-400 dark:focus-within:border-indigo-500 transition-all duration-300 shadow-sm">
                            <div class="ps-4 sm:ps-6">
                                <x-lucide-search class="w-4 h-4 sm:w-5 sm:h-5 text-body dark:text-slate-400" />
                            </div>
                            <input type="text" name="q" autocomplete="off"
                                class="w-full bg-transparent text-heading dark:text-white text-sm sm:text-base px-3 sm:px-4 py-3 sm:py-4 focus:outline-none placeholder:text-body dark:placeholder:text-slate-400 border-0 ring-0"
                                placeholder="{{ __('messages.landing.search_placeholder') }}">
                            <div class="pe-1 sm:pe-2">
                                <button type="submit"
                                    class="px-4 sm:px-6 py-1.5 sm:py-2.5 text-xs sm:text-sm font-semibold text-white bg-brand hover:bg-brand-strong dark:bg-sky-500 dark:hover:bg-sky-400 rounded-full transition-all duration-200 shadow-lg shadow-brand/25 dark:shadow-sky-500/25 active:scale-95">
                                    {{ __('messages.landing.search_button') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 sm:gap-3 max-w-3xl mx-auto px-0 sm:px-4">
                        <div class="group bg-white dark:bg-slate-800 rounded-xl sm:rounded-2xl px-3 sm:px-5 py-3 sm:py-5 text-center border border-default-medium dark:border-slate-700 shadow-xs">
                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <x-lucide-package class="w-3 h-3 sm:w-4 sm:h-4" />
                            </div>
                            <div class="text-xl sm:text-3xl font-bold text-heading dark:text-white tabular-nums tracking-tight">{{ $productsCount }}</div>
                            <div class="text-[10px] sm:text-xs text-body dark:text-slate-400 mt-0.5 sm:mt-1 font-medium">{{ __('messages.landing.products_badge') }}</div>
                        </div>
                        <div class="group bg-white dark:bg-slate-800 rounded-xl sm:rounded-2xl px-3 sm:px-5 py-3 sm:py-5 text-center border border-default-medium dark:border-slate-700 shadow-xs">
                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <x-lucide-activity class="w-3 h-3 sm:w-4 sm:h-4" />
                            </div>
                            <div class="text-xl sm:text-3xl font-bold text-heading dark:text-white tabular-nums tracking-tight">{{ $diseasesCount }}</div>
                            <div class="text-[10px] sm:text-xs text-body dark:text-slate-400 mt-0.5 sm:mt-1 font-medium">{{ __('messages.landing.diseases_badge') }}</div>
                        </div>
                        <div class="group bg-white dark:bg-slate-800 rounded-xl sm:rounded-2xl px-3 sm:px-5 py-3 sm:py-5 text-center border border-default-medium dark:border-slate-700 shadow-xs">
                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/50 dark:text-violet-400 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <x-lucide-flask-conical class="w-3 h-3 sm:w-4 sm:h-4" />
                            </div>
                            <div class="text-xl sm:text-3xl font-bold text-heading dark:text-white tabular-nums tracking-tight">{{ $ingredientsCount }}</div>
                            <div class="text-[10px] sm:text-xs text-body dark:text-slate-400 mt-0.5 sm:mt-1 font-medium">{{ __('messages.landing.ingredients_badge') }}</div>
                        </div>
                        <div class="group bg-white dark:bg-slate-800 rounded-xl sm:rounded-2xl px-3 sm:px-5 py-3 sm:py-5 text-center border border-default-medium dark:border-slate-700 shadow-xs">
                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400 flex items-center justify-center mx-auto mb-2 sm:mb-3">
                                <x-lucide-building-2 class="w-3 h-3 sm:w-4 sm:h-4" />
                            </div>
                            <div class="text-xl sm:text-3xl font-bold text-heading dark:text-white tabular-nums tracking-tight">{{ $companiesCount }}</div>
                            <div class="text-[10px] sm:text-xs text-body dark:text-slate-400 mt-0.5 sm:mt-1 font-medium">{{ __('messages.landing.companies_badge') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FEATURES --}}
    {{-- ============================================ --}}
    <section class="mt-16 sm:mt-32 mb-6 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10 sm:mb-14">
                <span class="text-[10px] sm:text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Platform</span>
                <h2 class="text-2xl sm:text-4xl font-bold text-heading dark:text-white mt-3 sm:mt-4 mb-3 sm:mb-4 tracking-tight">
                    {{ __('messages.landing.features_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 text-sm sm:text-base max-w-xl mx-auto px-2 sm:px-0">
                    {{ __('messages.landing.features_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">
                <a href="{{ route('products.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-indigo-400/30">
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-indigo-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-package class="w-5 h-5 sm:w-7 sm:h-7 text-white" />
                        </div>
                        <h3 class="text-base sm:text-xl font-bold text-heading dark:text-white mb-1 sm:mb-2">
                            {{ __('messages.landing.feature_products_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-xs sm:text-sm">
                            {{ __('messages.landing.feature_products_desc') }}
                        </p>
                        <span class="hidden sm:inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-indigo-500 dark:text-indigo-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('diseases.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-500/10 hover:border-emerald-400/30">
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-emerald-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-activity class="w-5 h-5 sm:w-7 sm:h-7 text-white" />
                        </div>
                        <h3 class="text-base sm:text-xl font-bold text-heading dark:text-white mb-1 sm:mb-2">
                            {{ __('messages.landing.feature_diseases_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-xs sm:text-sm">
                            {{ __('messages.landing.feature_diseases_desc') }}
                        </p>
                        <span class="hidden sm:inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-emerald-500 dark:text-emerald-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('active-ingredients.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-violet-500/10 hover:border-violet-400/30">
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-violet-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-flask-conical class="w-5 h-5 sm:w-7 sm:h-7 text-white" />
                        </div>
                        <h3 class="text-base sm:text-xl font-bold text-heading dark:text-white mb-1 sm:mb-2">
                            {{ __('messages.landing.feature_ingredients_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-xs sm:text-sm">
                            {{ __('messages.landing.feature_ingredients_desc') }}
                        </p>
                        <span class="hidden sm:inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-violet-500 dark:text-violet-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('companies.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-amber-500/10 hover:border-amber-400/30">
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-amber-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-building-2 class="w-5 h-5 sm:w-7 sm:h-7 text-white" />
                        </div>
                        <h3 class="text-base sm:text-xl font-bold text-heading dark:text-white mb-1 sm:mb-2">
                            {{ __('messages.landing.feature_companies_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-xs sm:text-sm">
                            {{ __('messages.landing.feature_companies_desc') }}
                        </p>
                        <span class="hidden sm:inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-amber-500 dark:text-amber-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- STATS --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-16 sm:mt-28 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10 sm:mb-14">
                <span class="text-[10px] sm:text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Numbers</span>
                <h2 class="text-2xl sm:text-4xl font-bold text-heading dark:text-white mt-3 sm:mt-4 tracking-tight">
                    Our Database in Numbers
                </h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                <div class="group relative overflow-hidden rounded-xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10">
                    <div class="h-1 bg-gradient-to-r from-indigo-500 to-sky-400"></div>
                    <div class="p-4 sm:p-7 text-center">
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center mx-auto mb-2 sm:mb-4">
                            <x-lucide-package class="w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <div class="text-xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $productsCount }}</div>
                        <div class="text-[10px] sm:text-sm text-body dark:text-slate-400 mt-1 sm:mt-2 font-medium">{{ __('messages.landing.products_badge') }}</div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-500/10">
                    <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>
                    <div class="p-4 sm:p-7 text-center">
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center mx-auto mb-2 sm:mb-4">
                            <x-lucide-activity class="w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <div class="text-xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $diseasesCount }}</div>
                        <div class="text-[10px] sm:text-sm text-body dark:text-slate-400 mt-1 sm:mt-2 font-medium">{{ __('messages.landing.diseases_badge') }}</div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-violet-500/10">
                    <div class="h-1 bg-gradient-to-r from-violet-500 to-violet-300"></div>
                    <div class="p-4 sm:p-7 text-center">
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-500/20 dark:text-violet-400 flex items-center justify-center mx-auto mb-2 sm:mb-4">
                            <x-lucide-flask-conical class="w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <div class="text-xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $ingredientsCount }}</div>
                        <div class="text-[10px] sm:text-sm text-body dark:text-slate-400 mt-1 sm:mt-2 font-medium">{{ __('messages.landing.ingredients_badge') }}</div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-amber-500/10">
                    <div class="h-1 bg-gradient-to-r from-amber-500 to-amber-300"></div>
                    <div class="p-4 sm:p-7 text-center">
                        <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center mx-auto mb-2 sm:mb-4">
                            <x-lucide-building-2 class="w-4 h-4 sm:w-5 sm:h-5" />
                        </div>
                        <div class="text-xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $companiesCount }}</div>
                        <div class="text-[10px] sm:text-sm text-body dark:text-slate-400 mt-1 sm:mt-2 font-medium">{{ __('messages.landing.companies_badge') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- HOW IT WORKS --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-16 sm:mt-28 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10 sm:mb-14">
                <span class="text-[10px] sm:text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Process</span>
                <h2 class="text-2xl sm:text-4xl font-bold text-heading dark:text-white mt-3 sm:mt-4 mb-3 sm:mb-4 tracking-tight">
                    {{ __('messages.landing.how_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 text-sm sm:text-base max-w-xl mx-auto px-2 sm:px-0">
                    {{ __('messages.landing.how_subtitle') }}
                </p>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
                {{-- Step 1 --}}
                <div class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-6 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/5">
                    {{-- Top accent bar --}}
                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-indigo-500 to-indigo-400"></div>

                    {{-- Decorative number watermark --}}
                    <div class="absolute -top-3 -end-2 sm:-top-4 sm:-end-3 text-7xl sm:text-8xl font-black text-indigo-500/5 dark:text-indigo-400/5 select-none pointer-events-none leading-none">01</div>

                    <div class="relative">
                        {{-- Icon + step badge row --}}
                        <div class="flex items-center justify-between mb-5 sm:mb-6">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/25 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                                <x-lucide-search class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                            </div>
                            <span class="inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-xs font-bold border-2 border-white dark:border-slate-800">1</span>
                        </div>

                        <h3 class="text-base sm:text-xl font-bold text-heading dark:text-white mb-2 sm:mb-3">{{ __('messages.landing.step1_title') }}</h3>
                        <p class="text-xs sm:text-sm text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.step1_desc') }}</p>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-6 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/5">
                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-500 to-emerald-400"></div>
                    <div class="absolute -top-3 -end-2 sm:-top-4 sm:-end-3 text-7xl sm:text-8xl font-black text-emerald-500/5 dark:text-emerald-400/5 select-none pointer-events-none leading-none">02</div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-5 sm:mb-6">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/25 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                                <x-lucide-book-open class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                            </div>
                            <span class="inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 text-xs font-bold border-2 border-white dark:border-slate-800">2</span>
                        </div>

                        <h3 class="text-base sm:text-xl font-bold text-heading dark:text-white mb-2 sm:mb-3">{{ __('messages.landing.step2_title') }}</h3>
                        <p class="text-xs sm:text-sm text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.step2_desc') }}</p>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-6 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-500/5">
                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-violet-500 to-violet-400"></div>
                    <div class="absolute -top-3 -end-2 sm:-top-4 sm:-end-3 text-7xl sm:text-8xl font-black text-violet-500/5 dark:text-violet-400/5 select-none pointer-events-none leading-none">03</div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-5 sm:mb-6">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shadow-lg shadow-violet-500/25 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                                <x-lucide-check-circle class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                            </div>
                            <span class="inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-violet-100 dark:bg-violet-900/50 text-violet-600 dark:text-violet-400 text-xs font-bold border-2 border-white dark:border-slate-800">3</span>
                        </div>

                        <h3 class="text-base sm:text-xl font-bold text-heading dark:text-white mb-2 sm:mb-3">{{ __('messages.landing.step3_title') }}</h3>
                        <p class="text-xs sm:text-sm text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.step3_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- BROWSE BY CATEGORY --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-16 sm:mt-28 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-10 sm:mb-14">
                <span class="text-[10px] sm:text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Categories</span>
                <h2 class="text-2xl sm:text-4xl font-bold text-heading dark:text-white mt-3 sm:mt-4 mb-3 sm:mb-4 tracking-tight">
                    {{ __('messages.landing.browse_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 text-sm sm:text-base max-w-xl mx-auto px-2 sm:px-0">
                    {{ __('messages.landing.browse_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <a href="{{ route('products.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-indigo-400/30">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-indigo-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-package class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h3 class="text-sm sm:text-lg font-bold text-heading dark:text-white mb-1 sm:mb-1.5">{{ __('messages.landing.browse_products') }}</h3>
                    <p class="text-[11px] sm:text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_products_desc') }}</p>
                </a>

                <a href="{{ route('diseases.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-emerald-400/30">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-emerald-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-activity class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h3 class="text-sm sm:text-lg font-bold text-heading dark:text-white mb-1 sm:mb-1.5">{{ __('messages.landing.browse_diseases') }}</h3>
                    <p class="text-[11px] sm:text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_diseases_desc') }}</p>
                </a>

                <a href="{{ route('active-ingredients.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-violet-400/30">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-violet-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-flask-conical class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h3 class="text-sm sm:text-lg font-bold text-heading dark:text-white mb-1 sm:mb-1.5">{{ __('messages.landing.browse_ingredients') }}</h3>
                    <p class="text-[11px] sm:text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_ingredients_desc') }}</p>
                </a>

                <a href="{{ route('companies.index') }}"
                    class="group relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-5 sm:p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-amber-400/30">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mb-3 sm:mb-5 shadow-lg shadow-amber-500/20 sm:group-hover:scale-110 sm:group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-building-2 class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                    </div>
                    <h3 class="text-sm sm:text-lg font-bold text-heading dark:text-white mb-1 sm:mb-1.5">{{ __('messages.landing.browse_companies') }}</h3>
                    <p class="text-[11px] sm:text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_companies_desc') }}</p>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- WHY VETPEDIA --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-16 sm:mt-28 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12 lg:gap-16 items-center">
                <div>
                    <span class="text-[10px] sm:text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Why us</span>
                    <h2 class="text-2xl sm:text-4xl font-bold text-heading dark:text-white mt-3 sm:mt-4 mb-3 sm:mb-4 tracking-tight">
                        {{ __('messages.landing.why_heading') }}
                    </h2>
                    <p class="text-body dark:text-slate-400 text-sm sm:text-base mb-8 sm:mb-10 max-w-md">
                        {{ __('messages.landing.why_subtitle') }}
                    </p>

                    <div class="space-y-5 sm:space-y-6">
                        <div class="flex gap-4 sm:gap-5 group">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center shrink-0 mt-0.5 sm:group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-database class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item1_title') }}</h3>
                                <p class="text-xs sm:text-sm text-body dark:text-slate-400 mt-0.5 sm:mt-1 leading-relaxed">{{ __('messages.landing.why_item1_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4 sm:gap-5 group">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 sm:group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-stethoscope class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item2_title') }}</h3>
                                <p class="text-xs sm:text-sm text-body dark:text-slate-400 mt-0.5 sm:mt-1 leading-relaxed">{{ __('messages.landing.why_item2_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4 sm:gap-5 group">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-violet-500/10 text-violet-600 dark:bg-violet-500/20 dark:text-violet-400 flex items-center justify-center shrink-0 mt-0.5 sm:group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-file-text class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item3_title') }}</h3>
                                <p class="text-xs sm:text-sm text-body dark:text-slate-400 mt-0.5 sm:mt-1 leading-relaxed">{{ __('messages.landing.why_item3_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-4 sm:gap-5 group">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5 sm:group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-languages class="w-4 h-4 sm:w-5 sm:h-5" />
                            </div>
                            <div>
                                <h3 class="text-sm sm:font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item4_title') }}</h3>
                                <p class="text-xs sm:text-sm text-body dark:text-slate-400 mt-0.5 sm:mt-1 leading-relaxed">{{ __('messages.landing.why_item4_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    @php
                        $total = $dosageFormsCount + $speciesCount + $ingredientsCount + $productsCount;
                        $max = max($dosageFormsCount, $speciesCount, $ingredientsCount, $productsCount) ?: 1;
                    @endphp
                    <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl">
                        {{-- Grid pattern overlay --}}
                        <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.03] pointer-events-none"
                             style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 20px 20px;"></div>

                        {{-- Top accent bar --}}
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-indigo-500 via-emerald-500 to-amber-500"></div>

                        <div class="p-6 sm:p-8">
                            {{-- Header row --}}
                            <div class="flex items-center justify-between mb-6 sm:mb-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-md shadow-indigo-500/20">
                                        <x-lucide-database class="w-4 h-4 text-white" />
                                    </div>
                                    <span class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.landing.database') }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-full border border-emerald-200/50 dark:border-emerald-700/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Live
                                </span>
                            </div>

                            {{-- Total hero --}}
                            <div class="text-center mb-6 sm:mb-8 pb-6 sm:pb-8 border-b border-white/10 dark:border-slate-700/50">
                                <div class="text-4xl sm:text-5xl font-black text-heading dark:text-white tabular-nums tracking-tight">{{ number_format($total) }}</div>
                                <div class="text-[11px] sm:text-xs text-body dark:text-slate-400 mt-1.5 font-medium uppercase tracking-wider">{{ __('messages.landing.total_records') }}</div>
                            </div>

                            {{-- Individual stats as rows --}}
                            <div class="space-y-4 sm:space-y-5">
                                <div class="flex items-center gap-4 group cursor-default">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20">
                                        <x-lucide-pill class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs sm:text-sm font-medium text-heading dark:text-white">{{ __('messages.landing.dosage_forms_badge') }}</span>
                                            <span class="text-base sm:text-lg font-bold text-heading dark:text-white tabular-nums">{{ $dosageFormsCount }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-indigo-400 transition-all duration-700" style="width: {{ ($dosageFormsCount / $max) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 group cursor-default">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shrink-0 shadow-sm shadow-emerald-500/20">
                                        <x-lucide-paw-print class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs sm:text-sm font-medium text-heading dark:text-white">{{ __('messages.landing.species_badge') }}</span>
                                            <span class="text-base sm:text-lg font-bold text-heading dark:text-white tabular-nums">{{ $speciesCount }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-700" style="width: {{ ($speciesCount / $max) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 group cursor-default">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center shrink-0 shadow-sm shadow-violet-500/20">
                                        <x-lucide-flask-conical class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs sm:text-sm font-medium text-heading dark:text-white">{{ __('messages.landing.ingredients_badge') }}</span>
                                            <span class="text-base sm:text-lg font-bold text-heading dark:text-white tabular-nums">{{ $ingredientsCount }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-violet-100 dark:bg-violet-900/40 overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-violet-400 transition-all duration-700" style="width: {{ ($ingredientsCount / $max) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 group cursor-default">
                                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shrink-0 shadow-sm shadow-amber-500/20">
                                        <x-lucide-building-2 class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs sm:text-sm font-medium text-heading dark:text-white">{{ __('messages.landing.products_badge') }}</span>
                                            <span class="text-base sm:text-lg font-bold text-heading dark:text-white tabular-nums">{{ $productsCount }}</span>
                                        </div>
                                        <div class="h-1.5 rounded-full bg-amber-100 dark:bg-amber-900/40 overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-amber-400 transition-all duration-700" style="width: {{ ($productsCount / $max) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- BLOG --}}
    {{-- ============================================ --}}
    @if ($latestBlogs->count())
        <section class="mb-6 mt-16 sm:mt-28 px-4 sm:px-0">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-10 sm:mb-14">
                    <div>
                        <span class="text-[10px] sm:text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Blog</span>
                        <h2 class="text-2xl sm:text-4xl font-bold text-heading dark:text-white mt-3 sm:mt-4 tracking-tight">
                            {{ __('messages.landing.latest_blog') }}
                        </h2>
                    </div>
                    <a href="{{ route('blog.index') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium text-indigo-500 dark:text-indigo-400 border border-indigo-500/30 dark:border-indigo-400/30 rounded-full hover:bg-indigo-500/10 transition-all duration-200">
                        {{ __('messages.landing.view_all') }}
                        <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    @foreach ($latestBlogs as $blog)
                        @php $locale = app()->getLocale(); @endphp
                        <a href="{{ route('blog.show', $blog) }}"
                            class="group overflow-hidden rounded-2xl sm:rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="h-36 sm:h-48 bg-white/50 dark:bg-slate-900/50 overflow-hidden">
                                @if ($blog->cover_image)
                                    <img src="{{ Storage::url($blog->cover_image) }}"
                                        alt="{{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <x-lucide-newspaper class="w-8 h-8 sm:w-10 sm:h-10 text-body/50 dark:text-slate-500" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 sm:p-6">
                                @if ($blog->category)
                                    <span class="inline-block px-2 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-semibold rounded-full bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 mb-2 sm:mb-3">
                                        {{ $locale === 'ar' && $blog->category->name_ar ? $blog->category->name_ar : $blog->category->name }}
                                    </span>
                                @endif
                                <h3 class="text-sm sm:text-base font-bold text-heading dark:text-white group-hover:text-indigo-500 dark:group-hover:text-indigo-400 line-clamp-2 transition-colors duration-150">
                                    {{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}
                                </h3>
                                <p class="text-[11px] sm:text-xs text-body dark:text-slate-400 mt-2 sm:mt-3 flex items-center gap-1.5">
                                    <x-lucide-calendar class="w-3 h-3" />
                                    {{ $blog->published_at->format('M d, Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="text-center mt-6 sm:hidden">
                    <a href="{{ route('blog.index') }}"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium text-indigo-500 dark:text-indigo-400 border border-indigo-500/30 dark:border-indigo-400/30 rounded-full hover:bg-indigo-500/10 transition-all duration-200">
                        {{ __('messages.landing.view_all') }}
                        <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-16 sm:mt-28 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto">
            <div class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-[#09090B] px-5 sm:px-8 py-12 sm:py-20 text-center">
                <div class="grid-bg absolute inset-0 opacity-30 pointer-events-none"></div>
                <div class="relative">
                    <h2 class="text-xl sm:text-4xl font-bold text-white mb-3 sm:mb-4 tracking-tight">
                        {{ __('messages.landing.cta_heading') }}
                    </h2>
                    <p class="text-white/50 text-sm sm:text-base max-w-md mx-auto mb-8 sm:mb-10 px-2 sm:px-0">
                        {{ __('messages.landing.cta_subtitle') }}
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center gap-2 px-6 sm:px-7 py-2.5 sm:py-3 text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-400 rounded-full transition-all duration-200 shadow-lg shadow-indigo-500/25 active:scale-95">
                            <x-lucide-package class="w-4 h-4" />
                            {{ __('messages.landing.cta_browse') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 px-6 sm:px-7 py-2.5 sm:py-3 text-sm font-semibold text-white/80 bg-white/5 hover:bg-white/10 rounded-full border border-white/10 transition-all duration-200 active:scale-95">
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
