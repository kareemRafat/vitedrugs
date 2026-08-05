@extends('app.layouts.master')

@section('title', __('large-animals.search.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="__('large-animals.search.heading')"
            :subtitle="__('large-animals.search.subtitle')"
            :badge="__('large-animals.hero.badge.knowledge_base')"
            badgeIcon="search"
        >
            <form method="GET" action="{{ route('large-animals.search') }}">
                <div class="relative w-full sm:w-1/2">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="text" name="q" value="{{ $query }}"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 pe-32 px-3 py-3 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        placeholder="{{ __('large-animals.search.placeholder') }}">
                    <button type="submit"
                        class="absolute inset-y-1 end-1 inline-flex items-center justify-center gap-1.5 px-4 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base focus:outline-none">
                        <x-lucide-search class="w-4 h-4" />
                        {{ __('large-animals.search.button') }}
                    </button>
                </div>
            </form>
        </x-large-animals.page-hero>

        @if (filled($query))

            {{-- Results heading --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs px-5 py-4 dark:bg-slate-800">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.search.results_for', ['q' => $query]) }}</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Diseases --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-activity class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.search.diseases') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $diseases->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse ($diseases as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('large-animals.diseases.show', $item->slug) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('large-animals.diseases.show', $item->slug) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('large-animals.search.no_section_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Clinical Signs --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-stethoscope class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.search.clinical_signs') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $clinicalSigns->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse ($clinicalSigns as $item)
                            <div class="py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('large-animals.diagnosis', ['sign' => $item->id]) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->display_name }}</a>
                                @if ($item->canonical_name && $item->canonical_name !== $item->display_name)
                                    <p class="text-sm text-body dark:text-slate-400">{{ $item->canonical_name }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('large-animals.search.no_section_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Products --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-package class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.search.products') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $products->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse ($products as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('products.show', $item) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->trade_name }}</a>
                                <a href="{{ route('products.show', $item) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('large-animals.search.no_section_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Blog Articles --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-newspaper class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.search.blog_articles') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $articles->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse ($articles as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('blog.show', $item) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->title }}</a>
                                <a href="{{ route('blog.show', $item) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('large-animals.search.no_section_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Microorganisms --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-microscope class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.search.microorganisms') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $microorganisms->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse ($microorganisms as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('large-animals.microorganisms.show', $item->slug) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->name }}</a>
                                <a href="{{ route('large-animals.microorganisms.show', $item->slug) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('large-animals.search.no_section_results') }}</p>
                        @endforelse
                    </div>
                </div>

                {{-- Medical Articles --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-file-text class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.search.medical_articles') }}</h2>
                        <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $medicalArticles->count() }}</span>
                    </div>
                    <div class="p-5">
                        @forelse ($medicalArticles as $item)
                            <div class="flex items-center justify-between py-2 border-b border-default-medium last:border-0">
                                <a href="{{ route('large-animals.medical-articles.show', $item->slug) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">{{ $item->title }}</a>
                                <a href="{{ route('large-animals.medical-articles.show', $item->slug) }}" wire:navigate
                                    class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </a>
                            </div>
                        @empty
                            <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('large-animals.search.no_section_results') }}</p>
                        @endforelse
                    </div>
                </div>

            </div>

        @else

            {{-- No query yet --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-search class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h2 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ __('large-animals.search.heading') }}</h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('large-animals.search.no_query') }}</p>
            </div>

        @endif

    </div>
@endsection
