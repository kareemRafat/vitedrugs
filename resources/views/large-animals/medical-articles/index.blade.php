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
                    <article class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden border border-default-medium hover:shadow-md transition-all duration-300 dark:bg-slate-800 dark:border-slate-700 group flex flex-col">
                        <a href="{{ route('large-animals.medical-articles.show', $article->slug) }}" wire:navigate class="flex flex-col h-full">
                            <div class="h-36 bg-neutral-secondary-soft dark:bg-slate-700 flex items-center justify-center overflow-hidden shrink-0">
                                <x-lucide-file-text class="w-12 h-12 text-body dark:text-slate-500 group-hover:scale-110 transition-transform duration-300" />
                            </div>
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex flex-wrap items-center gap-2 text-xs text-body dark:text-slate-400 mb-2">
                                    @if ($article->article_type)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ $article->article_type }}</span>
                                    @endif
                                    @if ($article->disease)
                                        <a href="{{ route('large-animals.diseases.show', $article->disease->slug) }}" wire:navigate
                                            class="inline-flex items-center gap-1 text-fg-brand hover:underline dark:text-brand">
                                            <x-lucide-activity class="w-3 h-3" />
                                            {{ $article->disease->name }}
                                        </a>
                                    @endif
                                </div>

                                <h2 class="text-lg font-semibold text-heading dark:text-white mb-2 line-clamp-2 group-hover:text-fg-brand dark:group-hover:text-brand transition-colors duration-150">{{ $article->title }}</h2>

                                @if ($article->summary)
                                    <p class="text-sm text-body dark:text-slate-400 line-clamp-3">{{ $article->localized_summary }}</p>
                                @endif

                                @if ($article->species)
                                    <p class="mt-3 text-xs text-body dark:text-slate-400">
                                        <span class="font-semibold">{{ __('large-animals.medical_articles.species') }}:</span> {{ $article->localized_species }}
                                    </p>
                                @endif

                                <div class="flex items-center gap-1.5 text-sm font-medium text-fg-brand pt-4 mt-auto">
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
