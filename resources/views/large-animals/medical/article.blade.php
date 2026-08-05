@extends('app.layouts.master')

@section('title', __('large-animals.medical.title', ['name' => $article['title']]))

@section('content')
    <div class="space-y-4">

        {{-- Breadcrumb --}}
        <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-body dark:text-slate-400 pt-4" aria-label="{{ __('messages.nav.breadcrumb') }}">
            <a href="{{ route('large-animals.home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.drugs') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.diseases') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium">{{ $article['title'] }}</span>
        </nav>

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="$article['title']"
            :subtitle="\Illuminate\Support\Str::limit(strip_tags($article['summary'] ?? ''), 220)"
            :badge="__('large-animals.hero.badge.article')"
            badgeIcon="book-marked"
        >
            <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white/15 text-white border border-white/30 rounded-base hover:bg-white/25 transition-colors">
                <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                {{ __('large-animals.medical.back_to_disease') }}
            </a>
        </x-large-animals.page-hero>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Main --}}
            <div class="lg:col-span-9">
                <article class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden dark:bg-slate-800 dark:border dark:border-slate-700">
                    <div class="px-4 sm:px-8 py-8">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-2 text-sm mb-4">
                                @if ($article['etiology_type'])
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ $article['etiology_type'] }}</span>
                                @endif
                                <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate
                                    class="inline-flex items-center gap-1.5 text-fg-brand hover:underline dark:text-brand">
                                    <x-lucide-activity class="w-3.5 h-3.5" />
                                    {{ $disease->name }}
                                </a>
                            </div>

                            {{-- Sections --}}
                            @forelse ($article['sections'] as $section)
                                <section id="section-{{ $section['id'] }}" class="scroll-mt-24 border-t border-default-medium dark:border-slate-700 first:border-t-0 py-6 first:pt-0">
                                    <h2 class="text-2xl font-bold text-heading dark:text-white mb-4">{{ $section['title'] }}</h2>

                                    @if ($section['id'] === 'references')
                                        <ul class="space-y-2">
                                            @foreach ($section['items'] as $item)
                                                <li>
                                                    <a href="{{ $item['url'] ?? '#' }}" target="_blank" rel="noopener"
                                                        class="inline-flex items-center gap-2 text-sm font-medium text-fg-brand hover:underline dark:text-brand">
                                                        <x-lucide-external-link class="w-3.5 h-3.5 shrink-0" />
                                                        {{ $item['label'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="space-y-4">
                                            @foreach ($section['items'] as $item)
                                                <div class="flex items-start gap-3">
                                                    <x-lucide-check-circle class="w-4 h-4 shrink-0 mt-0.5 text-brand dark:text-brand" />
                                                    <div>
                                                        <h3 class="text-base font-semibold text-heading dark:text-white">{{ $item['label'] }}</h3>
                                                        @if (filled($item['description'] ?? null))
                                                            <p class="text-sm text-body dark:text-slate-400 mt-0.5 leading-relaxed">{{ $item['description'] }}</p>
                                                        @endif
                                                        @if (! empty($item['meta']))
                                                            <div class="mt-2 flex flex-wrap gap-1.5">
                                                                @if (! empty($item['meta']['is_pathognomonic']))
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-xs bg-brand text-white">{{ __('large-animals.disease.pathognomonic') }}</span>
                                                                @endif
                                                                @if (! empty($item['meta']['is_required']))
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.disease.required') }}</span>
                                                                @endif
                                                                @if (! empty($item['meta']['is_specific']))
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.disease.specific') }}</span>
                                                                @endif
                                                                @if (! empty($item['meta']['weight']))
                                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-xs bg-neutral-secondary-soft border border-default-medium text-body dark:bg-slate-700 dark:border-slate-600 dark:text-slate-300">{{ __('large-animals.disease.weight') }}: {{ $item['meta']['weight'] }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </section>
                            @empty
                                <p class="text-base text-body dark:text-slate-400">{{ __('large-animals.medical.no_sections') }}</p>
                            @endforelse

                            <div class="mt-10 pt-6 border-t border-default-medium dark:border-slate-700">
                                <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate
                                    class="inline-flex items-center gap-2 text-sm font-medium text-body hover:text-heading dark:text-slate-400 dark:hover:text-white transition-colors">
                                    <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                                    {{ __('large-animals.medical.back_to_disease') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            {{-- TOC --}}
            <div class="lg:col-span-3">
                <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800 lg:sticky lg:top-20">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-body dark:text-slate-400 mb-3">{{ __('large-animals.medical.on_this_page') }}</h2>
                    @if (collect($article['sections'])->isNotEmpty())
                        <ul class="space-y-1.5 text-sm">
                            @foreach ($article['sections'] as $section)
                                <li>
                                    <a href="#section-{{ $section['id'] }}" class="block py-1 text-body hover:text-fg-brand dark:text-slate-300 dark:hover:text-brand transition-colors">
                                        {{ $section['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.medical.no_sections') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
