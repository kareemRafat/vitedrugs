@extends('app.layouts.master')

@section('title', $article->localized_title)

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($article->localized_summary ?? ''), 160) }}
@endsection

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="$article->localized_title"
            :subtitle="\Illuminate\Support\Str::limit(strip_tags($article->localized_summary ?? ''), 220)"
            :badge="__('large-animals.hero.badge.article')"
            badgeIcon="book-marked"
        />

        {{-- Breadcrumb --}}
        <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-body dark:text-slate-400 pt-4" aria-label="{{ __('messages.nav.breadcrumb') }}">
            <a href="{{ route('large-animals.home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('messages.parts.large_animals') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <a href="{{ route('large-animals.medical-articles.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.articles') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium">{{ $article->localized_title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Main --}}
            <div class="lg:col-span-9">
                <article class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden dark:bg-slate-800 dark:border dark:border-slate-700">
                    <div class="px-4 sm:px-8 py-8">
                        <div class="max-w-2xl">
                            {{-- Meta --}}
                            <div class="flex flex-wrap items-center gap-2 text-sm text-body dark:text-slate-400 mb-4">
                                @if ($article->article_type)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.medical_articles.types.' . $article->article_type) }}</span>
                                @endif
                                @if ($article->disease)
                                    <a href="{{ route('large-animals.diseases.show', $article->disease->slug) }}" wire:navigate
                                        class="inline-flex items-center gap-1.5 text-fg-brand hover:underline dark:text-brand">
                                        <x-lucide-activity class="w-3.5 h-3.5" />
                                        {{ app()->getLocale() === 'ar' && $article->disease->name_ar ? $article->disease->name_ar : $article->disease->name }}
                                    </a>
                                @endif
                                @if ($article->species)
                                    <span class="inline-flex items-center gap-1.5">
                                        <x-lucide-paw-print class="w-3.5 h-3.5" />
                                        {{ $article->localized_species }}
                                    </span>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="max-w-none prose-article text-body dark:text-slate-300 leading-relaxed
                                [&_h2]:text-heading dark:[&_h2]:text-white [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:mt-8 [&_h2]:mb-4
                                [&_h3]:text-heading dark:[&_h3]:text-white [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:mt-6 [&_h3]:mb-3
                                [&_p]:mb-4 [&_p]:leading-relaxed
                                [&_h2]:scroll-mt-24
                                [&_ul]:space-y-2 [&_ul]:mb-4
                                [&_ol]:space-y-2 [&_ol]:mb-4
                                [&_blockquote]:border-s-4 [&_blockquote]:border-brand [&_blockquote]:ps-4 [&_blockquote]:my-6 [&_blockquote]:italic
                                [&_a]:text-fg-brand dark:[&_a]:text-brand [&_a]:no-underline hover:[&_a]:underline
                                [&_hr]:border-default-medium dark:[&_hr]:border-slate-700 [&_hr]:my-8
                                [&_pre]:bg-neutral-secondary-soft dark:[&_pre]:bg-slate-700 [&_pre]:border [&_pre]:border-default-medium dark:[&_pre]:border-slate-600 [&_pre]:rounded-base [&_pre]:p-4 [&_pre]:overflow-x-auto
                                [&_code]:text-sm
                                [&_img]:rounded-base [&_img]:shadow-xs [&_img]:max-w-full [&_img]:h-auto">
                                {!! $content ?? '' !!}
                            </div>

                            <div class="mt-10 pt-6 border-t border-default-medium dark:border-slate-700">
                                <a href="{{ route('large-animals.medical-articles.index') }}" wire:navigate
                                    class="inline-flex items-center gap-2 text-sm font-medium text-body hover:text-heading dark:text-slate-400 dark:hover:text-white transition-colors">
                                    <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                                    {{ __('large-animals.medical_articles.back') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            {{-- TOC sidebar --}}
            <div class="lg:col-span-3">
                <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800 lg:sticky lg:top-20">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-body dark:text-slate-400 mb-3">{{ __('large-animals.medical_articles.toc') }}</h2>
                    @if (collect($tableOfContents)->isNotEmpty())
                        <ul class="space-y-1.5 text-sm">
                            @foreach ($tableOfContents as $index => $heading)
                                <li>
                                    <a href="#section-{{ $index }}" class="block py-1 text-body hover:text-fg-brand dark:text-slate-300 dark:hover:text-brand transition-colors">
                                        {{ strip_tags($heading) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.medical_articles.no_toc') }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@section('css')
    <style>
        html { scroll-behavior: smooth; }
    </style>
@endsection
