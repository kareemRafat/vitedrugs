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
    .gradient-border-mask {
        position: relative;
    }
    .gradient-border-mask::before {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: inherit;
        background: linear-gradient(135deg, rgba(99,102,241,.3), transparent 40%, transparent 60%, rgba(34,211,238,.3));
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }
    .group:hover .gradient-border-mask::before {
        opacity: 1;
    }
</style>
@endsection

@section('content')
    {{-- ============================================ --}}
    {{-- HERO --}}
    {{-- ============================================ --}}
    <section class="relative overflow-hidden rounded-3xl bg-[#09090B] min-h-[90vh] flex items-center">
        <div class="grid-bg absolute inset-0 pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#09090B]/90 pointer-events-none"></div>

        <div class="absolute -top-48 -end-48 w-96 h-96 rounded-full bg-indigo-500/15 blur-3xl animate-float pointer-events-none"></div>
        <div class="absolute -bottom-48 -start-48 w-96 h-96 rounded-full bg-sky-500/10 blur-3xl animate-float-slow pointer-events-none" style="animation-delay: 2s"></div>
        <div class="absolute top-1/3 start-1/4 w-64 h-64 rounded-full bg-emerald-500/5 blur-3xl animate-pulse-glow pointer-events-none"></div>

        <div class="relative w-full px-6 py-24 sm:py-32 md:py-40">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-4xl mx-auto text-center animate-slide-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 text-white/70 text-xs font-medium tracking-widest uppercase mb-8 border border-white/10 backdrop-blur-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('messages.landing.badge') }}
                    </div>

                    <h1 class="text-white font-bold leading-[1.05] tracking-tight mb-6"
                        style="font-size: clamp(2.5rem, 6vw, 4.5rem);">
                        {{ __('messages.landing.heading') }}
                    </h1>

                    <p class="text-white/50 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-12 leading-relaxed">
                        {{ __('messages.landing.subtitle') }}
                    </p>

                    <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto mb-16">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500/30 via-sky-500/30 to-indigo-500/30 rounded-full blur-xl opacity-0 group-focus-within:opacity-100 transition-all duration-500"></div>
                            <div class="relative flex items-center bg-white/5 rounded-full border border-white/10 focus-within:border-indigo-400/50 transition-all duration-300 backdrop-blur-md">
                                <div class="ps-6">
                                    <x-lucide-search class="w-5 h-5 text-white/30" />
                                </div>
                                <input type="text" name="q" autocomplete="off"
                                    class="w-full bg-transparent text-white text-base px-4 py-4 focus:outline-none placeholder:text-white/20 border-0 ring-0"
                                    placeholder="{{ __('messages.landing.search_placeholder') }}">
                                <div class="pe-2">
                                    <button type="submit"
                                        class="px-6 py-2.5 text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-400 rounded-full transition-all duration-200 shadow-lg shadow-indigo-500/25 active:scale-95">
                                        {{ __('messages.landing.search_button') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-3xl mx-auto">
                        <div class="group backdrop-blur-xl bg-white/[0.03] border border-white/[0.06] rounded-2xl px-5 py-5 text-center hover:bg-white/[0.06] hover:-translate-y-0.5 transition-all duration-300">
                            <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-300 flex items-center justify-center mx-auto mb-3">
                                <x-lucide-package class="w-4 h-4" />
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold text-white tabular-nums tracking-tight">{{ $productsCount }}</div>
                            <div class="text-xs text-white/40 mt-1 font-medium">{{ __('messages.landing.products_badge') }}</div>
                        </div>
                        <div class="group backdrop-blur-xl bg-white/[0.03] border border-white/[0.06] rounded-2xl px-5 py-5 text-center hover:bg-white/[0.06] hover:-translate-y-0.5 transition-all duration-300">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-300 flex items-center justify-center mx-auto mb-3">
                                <x-lucide-activity class="w-4 h-4" />
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold text-white tabular-nums tracking-tight">{{ $diseasesCount }}</div>
                            <div class="text-xs text-white/40 mt-1 font-medium">{{ __('messages.landing.diseases_badge') }}</div>
                        </div>
                        <div class="group backdrop-blur-xl bg-white/[0.03] border border-white/[0.06] rounded-2xl px-5 py-5 text-center hover:bg-white/[0.06] hover:-translate-y-0.5 transition-all duration-300">
                            <div class="w-9 h-9 rounded-xl bg-violet-500/10 text-violet-300 flex items-center justify-center mx-auto mb-3">
                                <x-lucide-flask-conical class="w-4 h-4" />
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold text-white tabular-nums tracking-tight">{{ $ingredientsCount }}</div>
                            <div class="text-xs text-white/40 mt-1 font-medium">{{ __('messages.landing.ingredients_badge') }}</div>
                        </div>
                        <div class="group backdrop-blur-xl bg-white/[0.03] border border-white/[0.06] rounded-2xl px-5 py-5 text-center hover:bg-white/[0.06] hover:-translate-y-0.5 transition-all duration-300">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-300 flex items-center justify-center mx-auto mb-3">
                                <x-lucide-building-2 class="w-4 h-4" />
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold text-white tabular-nums tracking-tight">{{ $companiesCount }}</div>
                            <div class="text-xs text-white/40 mt-1 font-medium">{{ __('messages.landing.companies_badge') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FEATURES --}}
    {{-- ============================================ --}}
    <section class="mt-24 sm:mt-32 mb-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Platform</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-4 mb-4 tracking-tight">
                    {{ __('messages.landing.features_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 max-w-xl mx-auto">
                    {{ __('messages.landing.features_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <a href="{{ route('products.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10 hover:border-indigo-400/30">
                    <div class="absolute -top-16 -end-16 w-40 h-40 rounded-full bg-indigo-500/5 dark:bg-indigo-500/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center mb-5 shadow-lg shadow-indigo-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-package class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_products_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-sm sm:text-base">
                            {{ __('messages.landing.feature_products_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-indigo-500 dark:text-indigo-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('diseases.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-500/10 hover:border-emerald-400/30">
                    <div class="absolute -top-16 -end-16 w-40 h-40 rounded-full bg-emerald-500/5 dark:bg-emerald-500/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-5 shadow-lg shadow-emerald-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-activity class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_diseases_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-sm sm:text-base">
                            {{ __('messages.landing.feature_diseases_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-emerald-500 dark:text-emerald-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('active-ingredients.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-violet-500/10 hover:border-violet-400/30">
                    <div class="absolute -top-16 -end-16 w-40 h-40 rounded-full bg-violet-500/5 dark:bg-violet-500/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mb-5 shadow-lg shadow-violet-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-flask-conical class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_ingredients_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-sm sm:text-base">
                            {{ __('messages.landing.feature_ingredients_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-violet-500 dark:text-violet-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('messages.landing.cta_browse') }}
                            <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </div>
                </a>

                <a href="{{ route('companies.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-amber-500/10 hover:border-amber-400/30">
                    <div class="absolute -top-16 -end-16 w-40 h-40 rounded-full bg-amber-500/5 dark:bg-amber-500/10 transition-all duration-500 group-hover:scale-150"></div>
                    <div class="card-shine absolute inset-0 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mb-5 shadow-lg shadow-amber-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                            <x-lucide-building-2 class="w-7 h-7 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-heading dark:text-white mb-2">
                            {{ __('messages.landing.feature_companies_title') }}
                        </h3>
                        <p class="text-body dark:text-slate-400 leading-relaxed text-sm sm:text-base">
                            {{ __('messages.landing.feature_companies_desc') }}
                        </p>
                        <span class="inline-flex items-center gap-1.5 mt-5 text-sm font-medium text-amber-500 dark:text-amber-400 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
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
    <section class="mb-6 mt-20 sm:mt-28">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Numbers</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-4 tracking-tight">
                    Our Database in Numbers
                </h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-indigo-500/10">
                    <div class="h-1 bg-gradient-to-r from-indigo-500 to-sky-400"></div>
                    <div class="p-7 text-center">
                        <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center mx-auto mb-4">
                            <x-lucide-package class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $productsCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-2 font-medium">{{ __('messages.landing.products_badge') }}</div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-500/10">
                    <div class="h-1 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>
                    <div class="p-7 text-center">
                        <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center mx-auto mb-4">
                            <x-lucide-activity class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $diseasesCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-2 font-medium">{{ __('messages.landing.diseases_badge') }}</div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-violet-500/10">
                    <div class="h-1 bg-gradient-to-r from-violet-500 to-violet-300"></div>
                    <div class="p-7 text-center">
                        <div class="w-11 h-11 rounded-xl bg-violet-500/10 text-violet-600 dark:bg-violet-500/20 dark:text-violet-400 flex items-center justify-center mx-auto mb-4">
                            <x-lucide-flask-conical class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $ingredientsCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-2 font-medium">{{ __('messages.landing.ingredients_badge') }}</div>
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-amber-500/10">
                    <div class="h-1 bg-gradient-to-r from-amber-500 to-amber-300"></div>
                    <div class="p-7 text-center">
                        <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center mx-auto mb-4">
                            <x-lucide-building-2 class="w-5 h-5" />
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-heading dark:text-white tabular-nums tracking-tight">{{ $companiesCount }}</div>
                        <div class="text-sm text-body dark:text-slate-400 mt-2 font-medium">{{ __('messages.landing.companies_badge') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- HOW IT WORKS --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-20 sm:mt-28">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Process</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-4 mb-4 tracking-tight">
                    {{ __('messages.landing.how_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 max-w-xl mx-auto">
                    {{ __('messages.landing.how_subtitle') }}
                </p>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="hidden md:block absolute top-12 start-[16.66%] end-[16.66%] h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

                <div class="relative text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-500/20">
                        <x-lucide-search class="w-7 h-7 text-white" />
                    </div>
                    <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 text-sm font-bold mb-4">1</div>
                    <h3 class="text-xl font-bold text-heading dark:text-white mb-3">{{ __('messages.landing.step1_title') }}</h3>
                    <p class="text-body dark:text-slate-400 leading-relaxed text-sm max-w-xs mx-auto">{{ __('messages.landing.step1_desc') }}</p>
                </div>

                <div class="relative text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20">
                        <x-lucide-book-open class="w-7 h-7 text-white" />
                    </div>
                    <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 text-sm font-bold mb-4">2</div>
                    <h3 class="text-xl font-bold text-heading dark:text-white mb-3">{{ __('messages.landing.step2_title') }}</h3>
                    <p class="text-body dark:text-slate-400 leading-relaxed text-sm max-w-xs mx-auto">{{ __('messages.landing.step2_desc') }}</p>
                </div>

                <div class="relative text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-violet-500/20">
                        <x-lucide-check-circle class="w-7 h-7 text-white" />
                    </div>
                    <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-violet-500/10 text-violet-600 dark:bg-violet-500/20 dark:text-violet-400 text-sm font-bold mb-4">3</div>
                    <h3 class="text-xl font-bold text-heading dark:text-white mb-3">{{ __('messages.landing.step3_title') }}</h3>
                    <p class="text-body dark:text-slate-400 leading-relaxed text-sm max-w-xs mx-auto">{{ __('messages.landing.step3_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- BROWSE BY CATEGORY --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-20 sm:mt-28">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Categories</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-4 mb-4 tracking-tight">
                    {{ __('messages.landing.browse_heading') }}
                </h2>
                <p class="text-body dark:text-slate-400 max-w-xl mx-auto">
                    {{ __('messages.landing.browse_subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('products.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-indigo-400/30">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center mb-5 shadow-lg shadow-indigo-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-package class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1.5">{{ __('messages.landing.browse_products') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_products_desc') }}</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-medium text-indigo-500 dark:text-indigo-400 opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 transition-all duration-300">
                        {{ __('messages.landing.cta_browse') }}
                        <x-lucide-arrow-right class="w-3 h-3 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                    </div>
                </a>

                <a href="{{ route('diseases.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-emerald-400/30">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-5 shadow-lg shadow-emerald-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-activity class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1.5">{{ __('messages.landing.browse_diseases') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_diseases_desc') }}</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-medium text-emerald-500 dark:text-emerald-400 opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 transition-all duration-300">
                        {{ __('messages.landing.cta_browse') }}
                        <x-lucide-arrow-right class="w-3 h-3 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                    </div>
                </a>

                <a href="{{ route('active-ingredients.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-violet-400/30">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center mb-5 shadow-lg shadow-violet-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-flask-conical class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1.5">{{ __('messages.landing.browse_ingredients') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_ingredients_desc') }}</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-medium text-violet-500 dark:text-violet-400 opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 transition-all duration-300">
                        {{ __('messages.landing.cta_browse') }}
                        <x-lucide-arrow-right class="w-3 h-3 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                    </div>
                </a>

                <a href="{{ route('companies.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-7 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-amber-400/30">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center mb-5 shadow-lg shadow-amber-500/20 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                        <x-lucide-building-2 class="w-6 h-6 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-heading dark:text-white mb-1.5">{{ __('messages.landing.browse_companies') }}</h3>
                    <p class="text-xs text-body dark:text-slate-400 leading-relaxed">{{ __('messages.landing.browse_companies_desc') }}</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-medium text-amber-500 dark:text-amber-400 opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0 transition-all duration-300">
                        {{ __('messages.landing.cta_browse') }}
                        <x-lucide-arrow-right class="w-3 h-3 rtl:rotate-180 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- WHY VETPEDIA --}}
    {{-- ============================================ --}}
    <section class="mb-6 mt-20 sm:mt-28">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <span class="text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Why us</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-4 mb-4 tracking-tight">
                        {{ __('messages.landing.why_heading') }}
                    </h2>
                    <p class="text-body dark:text-slate-400 mb-10 max-w-md">
                        {{ __('messages.landing.why_subtitle') }}
                    </p>

                    <div class="space-y-6">
                        <div class="flex gap-5 group">
                            <div class="w-11 h-11 rounded-2xl bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-database class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item1_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-1 leading-relaxed">{{ __('messages.landing.why_item1_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-5 group">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-stethoscope class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item2_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-1 leading-relaxed">{{ __('messages.landing.why_item2_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-5 group">
                            <div class="w-11 h-11 rounded-2xl bg-violet-500/10 text-violet-600 dark:bg-violet-500/20 dark:text-violet-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-file-text class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item3_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-1 leading-relaxed">{{ __('messages.landing.why_item3_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-5 group">
                            <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5 group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-languages class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-heading dark:text-white">{{ __('messages.landing.why_item4_title') }}</h3>
                                <p class="text-sm text-body dark:text-slate-400 mt-1 leading-relaxed">{{ __('messages.landing.why_item4_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-indigo-500/5 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 -start-10 w-40 h-40 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>

                    <div class="relative overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl p-8">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="flex -space-x-2 rtl:space-x-reverse">
                                <div class="w-8 h-8 rounded-full bg-indigo-500/20 border-2 border-white dark:border-slate-800"></div>
                                <div class="w-8 h-8 rounded-full bg-emerald-500/20 border-2 border-white dark:border-slate-800"></div>
                                <div class="w-8 h-8 rounded-full bg-amber-500/20 border-2 border-white dark:border-slate-800"></div>
                            </div>
                            <span class="text-xs text-body dark:text-slate-400">Active users</span>
                            <span class="text-xs font-semibold text-emerald-500 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full">Live</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-5 rounded-2xl bg-white/50 dark:bg-slate-900/50 border border-white/10">
                                <div class="text-2xl font-extrabold text-heading dark:text-white tabular-nums">{{ $dosageFormsCount }}</div>
                                <div class="text-xs text-body dark:text-slate-400 mt-1.5 font-medium">{{ __('messages.landing.dosage_forms_badge') }}</div>
                            </div>
                            <div class="text-center p-5 rounded-2xl bg-white/50 dark:bg-slate-900/50 border border-white/10">
                                <div class="text-2xl font-extrabold text-heading dark:text-white tabular-nums">{{ $speciesCount }}</div>
                                <div class="text-xs text-body dark:text-slate-400 mt-1.5 font-medium">{{ __('messages.landing.species_badge') }}</div>
                            </div>
                            <div class="text-center p-5 rounded-2xl bg-white/50 dark:bg-slate-900/50 border border-white/10">
                                <div class="text-2xl font-extrabold text-heading dark:text-white tabular-nums">{{ $ingredientsCount }}</div>
                                <div class="text-xs text-body dark:text-slate-400 mt-1.5 font-medium">{{ __('messages.landing.ingredients_badge') }}</div>
                            </div>
                            <div class="text-center p-5 rounded-2xl bg-white/50 dark:bg-slate-900/50 border border-white/10">
                                <div class="text-2xl font-extrabold text-heading dark:text-white tabular-nums">{{ $productsCount }}</div>
                                <div class="text-xs text-body dark:text-slate-400 mt-1.5 font-medium">{{ __('messages.landing.products_badge') }}</div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-white/10 dark:border-slate-700/50">
                            <div class="flex items-center justify-between text-xs text-body dark:text-slate-400">
                                <span>Database updated regularly</span>
                                <span class="text-emerald-500 dark:text-emerald-400 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Synced
                                </span>
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
        <section class="mb-6 mt-20 sm:mt-28">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center justify-between mb-14">
                    <div>
                        <span class="text-xs font-semibold tracking-[.2em] uppercase text-indigo-400 dark:text-indigo-400">Blog</span>
                        <h2 class="text-3xl sm:text-4xl font-bold text-heading dark:text-white mt-4 tracking-tight">
                            {{ __('messages.landing.latest_blog') }}
                        </h2>
                    </div>
                    <a href="{{ route('blog.index') }}"
                        class="hidden sm:inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-medium text-indigo-500 dark:text-indigo-400 border border-indigo-500/30 dark:border-indigo-400/30 rounded-full hover:bg-indigo-500/10 transition-all duration-200">
                        {{ __('messages.landing.view_all') }}
                        <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($latestBlogs as $blog)
                        @php $locale = app()->getLocale(); @endphp
                        <a href="{{ route('blog.show', $blog) }}"
                            class="group overflow-hidden rounded-3xl border border-white/10 dark:border-slate-700/50 bg-white dark:bg-slate-800/50 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                            <div class="h-48 bg-white/50 dark:bg-slate-900/50 overflow-hidden">
                                @if ($blog->cover_image)
                                    <img src="{{ Storage::url($blog->cover_image) }}"
                                        alt="{{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <x-lucide-newspaper class="w-10 h-10 text-body/50 dark:text-slate-500" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                @if ($blog->category)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 mb-3">
                                        {{ $locale === 'ar' && $blog->category->name_ar ? $blog->category->name_ar : $blog->category->name }}
                                    </span>
                                @endif
                                <h3 class="text-base font-bold text-heading dark:text-white group-hover:text-indigo-500 dark:group-hover:text-indigo-400 line-clamp-2 transition-colors duration-150">
                                    {{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}
                                </h3>
                                <p class="text-xs text-body dark:text-slate-400 mt-3 flex items-center gap-1.5">
                                    <x-lucide-calendar class="w-3 h-3" />
                                    {{ $blog->published_at->format('M d, Y') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="text-center mt-8 sm:hidden">
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
    <section class="mb-6 mt-20 sm:mt-28">
        <div class="max-w-7xl mx-auto">
            <div class="relative overflow-hidden rounded-3xl bg-[#09090B] px-8 py-16 sm:py-20 text-center">
                <div class="grid-bg absolute inset-0 opacity-30 pointer-events-none"></div>
                <div class="absolute -top-24 -start-24 w-64 h-64 rounded-full bg-indigo-500/5 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -end-24 w-64 h-64 rounded-full bg-sky-500/5 blur-3xl pointer-events-none"></div>
                <div class="relative">
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4 tracking-tight">
                        {{ __('messages.landing.cta_heading') }}
                    </h2>
                    <p class="text-white/50 max-w-md mx-auto mb-10">
                        {{ __('messages.landing.cta_subtitle') }}
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center gap-2 px-7 py-3 text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-400 rounded-full transition-all duration-200 shadow-lg shadow-indigo-500/25 active:scale-95">
                            <x-lucide-package class="w-4 h-4" />
                            {{ __('messages.landing.cta_browse') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center gap-2 px-7 py-3 text-sm font-semibold text-white/80 bg-white/5 hover:bg-white/10 rounded-full border border-white/10 transition-all duration-200 active:scale-95">
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
