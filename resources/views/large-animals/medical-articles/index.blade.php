@extends('app.layouts.master')

@section('title', __('large-animals.medical_articles.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="__('large-animals.medical_articles.heading')"
            :subtitle="__('large-animals.medical_articles.subtitle')"
            :badge="__('large-animals.hero.badge.articles')"
            badgeIcon="book-marked"
            :stats="[
                ['count' => $articles->total(), 'label' => __('large-animals.hero.stats.articles'), 'icon' => 'file-text'],
            ]"
        />

        @if ($articles->isEmpty())

            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-file-text class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h2 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ __('large-animals.medical_articles.empty_state') }}</h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('large-animals.medical_articles.no_articles_desc') }}</p>
            </div>

        @else

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($articles as $article)
                    <article class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden border border-default-medium hover:shadow-md hover:border-brand transition-all duration-300 dark:bg-slate-800 dark:border-slate-700 group flex flex-col">
                        <a href="{{ route('large-animals.medical-articles.show', $article->slug) }}" wire:navigate class="flex flex-col h-full">
                            {{-- Header --}}
                            <div class="relative h-32 bg-gradient-to-br from-brand to-brand-strong flex items-center justify-center overflow-hidden shrink-0">
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute -top-10 -end-10 w-40 h-40 rounded-full bg-white"></div>
                                </div>
                                <x-lucide-book-marked class="w-10 h-10 text-white relative" />
                                @if ($article->article_type)
                                    <span class="absolute bottom-3 start-3 inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-base bg-black/25 text-white backdrop-blur-sm">
                                        {{ __('large-animals.medical_articles.types.' . $article->article_type) }}
                                    </span>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col flex-1 justify-between gap-2">
                                    @if ($article->disease)
                                        <div class="flex flex-wrap items-center gap-2 text-xs text-body dark:text-slate-400">
                                            <span class="inline-flex items-center gap-1 text-fg-brand">
                                                <x-lucide-activity class="w-3 h-3" />
                                                {{ app()->getLocale() === 'ar' && $article->disease->name_ar ? $article->disease->name_ar : $article->disease->name }}
                                            </span>
                                        </div>
                                    @endif

                                    <h2 class="text-lg font-semibold text-heading dark:text-white line-clamp-2 group-hover:text-fg-brand dark:group-hover:text-brand transition-colors duration-150">{{ $article->localized_title }}</h2>

                                    @if ($article->localized_summary)
                                        <p class="text-sm text-body dark:text-slate-400 line-clamp-3">{{ $article->localized_summary }}</p>
                                    @endif

                                    @if ($article->species)
                                        <p class="text-xs text-body dark:text-slate-400">
                                            <span class="font-semibold">{{ __('large-animals.medical_articles.species') }}:</span> {{ $article->localized_species }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-1.5 text-sm font-medium text-fg-brand mt-1">
                                        {{ __('large-animals.medical_articles.read_more') }}
                                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform duration-150" />
                                    </div>
                                </div>
                            </a>
                        </article>
                @endforeach
            </div>

            <x-pagination :paginator="$articles" translation-prefix="large-animals.common" />

        @endif

    </div>
@endsection
