@extends('app.layouts.master')

@php $locale = app()->getLocale(); @endphp

@section('title', $blog->meta_title ?: ($locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title))
@section('meta_description', $blog->meta_description ?: strip_tags($locale === 'ar' && $blog->excerpt_ar ? $blog->excerpt_ar : ($blog->excerpt ?: '')))

@section('og_title', $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title)
@section('og_description', $blog->meta_description ?: strip_tags($locale === 'ar' && $blog->excerpt_ar ? $blog->excerpt_ar : ($blog->excerpt ?: '')))

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-body dark:text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-fg-brand transition-colors">{{ __('messages.nav.home') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <a href="{{ route('blog.index') }}" class="hover:text-fg-brand transition-colors">{{ __('messages.blog.title') }}</a>
            @if ($blog->category)
                <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
                <a href="{{ route('blog.index', ['category' => $blog->category->id]) }}"
                    class="hover:text-fg-brand transition-colors">{{ $locale === 'ar' && $blog->category->name_ar ? $blog->category->name_ar : $blog->category->name }}</a>
            @endif
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium truncate">{{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}</span>
        </nav>

        {{-- Article Card --}}
        <article class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden dark:bg-gray-800">

            {{-- Cover Image --}}
            @if ($blog->cover_image)
                <div class="relative h-64 sm:h-80 md:h-96 bg-neutral-secondary-soft dark:bg-gray-700">
                    <img src="{{ Storage::url($blog->cover_image) }}"
                        alt="{{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}"
                        class="w-full h-full object-cover">
                </div>
            @endif

            {{-- Article Body --}}
            <div class="px-4 sm:px-8 py-8">
                <div class="max-w-2xl">

                    {{-- Meta --}}
                    <div class="flex flex-wrap items-center gap-3 text-sm text-body dark:text-gray-400 mb-4">
                        @if ($blog->category)
                            <a href="{{ route('blog.index', ['category' => $blog->category->id]) }}"
                                class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand hover:bg-brand hover:text-white transition-colors duration-150">
                                {{ $locale === 'ar' && $blog->category->name_ar ? $blog->category->name_ar : $blog->category->name }}
                            </a>
                        @endif
                        @if ($blog->author)
                            <span class="flex items-center gap-1.5">
                                <x-lucide-user class="w-3.5 h-3.5" />
                                {{ $blog->author->name }}
                            </span>
                        @endif
                        <span class="flex items-center gap-1.5">
                            <x-lucide-calendar class="w-3.5 h-3.5" />
                            <time datetime="{{ $blog->published_at->format('Y-m-d') }}">
                                {{ $blog->published_at->format('F d, Y') }}
                            </time>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-lucide-clock class="w-3.5 h-3.5" />
                            {{ __('messages.blog.read_time', ['minutes' => $blog->read_time]) }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-6">
                        {{ $locale === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}
                    </h1>

                    {{-- Excerpt (if exists, as a lead paragraph) --}}
                    @php $excerpt = $locale === 'ar' && $blog->excerpt_ar ? $blog->excerpt_ar : $blog->excerpt; @endphp
                    @if ($excerpt)
                        <div class="text-base sm:text-lg text-body dark:text-gray-300 leading-relaxed mb-8 border-s-4 border-brand ps-4">
                            {{ $excerpt }}
                        </div>
                    @endif

                    {{-- Body Content --}}
                    <div class="prose prose-lg max-w-none dark:prose-invert prose-headings:text-heading prose-p:text-body prose-a:text-fg-brand prose-a:no-underline hover:prose-a:underline prose-strong:text-heading prose-code:text-body prose-pre:bg-neutral-secondary-soft dark:prose-pre:bg-gray-700 prose-pre:border prose-pre:border-default-medium prose-pre:rounded-base prose-img:rounded-base prose-img:shadow-xs
                        [&_h2]:text-heading [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:mt-8 [&_h2]:mb-4
                        [&_h3]:text-heading [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:mt-6 [&_h3]:mb-3
                        [&_p]:text-body [&_p]:leading-relaxed [&_p]:mb-4
                        [&_ul]:text-body [&_ul]:space-y-2 [&_ul]:mb-4
                        [&_ol]:text-body [&_ol]:space-y-2 [&_ol]:mb-4
                        [&_li]:text-body
                        [&_blockquote]:border-s-4 [&_blockquote]:border-brand [&_blockquote]:ps-4 [&_blockquote]:text-body [&_blockquote]:italic [&_blockquote]:my-6
                        [&_a]:text-fg-brand [&_a]:no-underline hover:[&_a]:underline
                        [&_hr]:border-default-medium [&_hr]:my-8">
                        {!! $locale === 'ar' && $blog->body_ar ? $blog->body_ar : $blog->body !!}
                    </div>

                    {{-- Share / Footer --}}
                    <div class="mt-10 pt-6 border-t border-default-medium dark:border-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <a href="{{ route('blog.index') }}"
                                class="inline-flex items-center gap-2 text-sm font-medium text-body hover:text-heading dark:text-gray-400 dark:hover:text-white transition-colors">
                                <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                                {{ __('messages.blog.back_to_blog') }}
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </article>

        {{-- Related Posts --}}
        @if ($related->count())
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-heading dark:text-white">{{ __('messages.blog.related_posts') }}</h2>
                    <a href="{{ route('blog.index') }}"
                        class="text-sm font-medium text-fg-brand hover:underline">
                        {{ __('messages.blog.view_all') }}
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($related as $relatedPost)
                        <a href="{{ route('blog.show', $relatedPost) }}"
                            class="group bg-neutral-primary-soft rounded-base border border-default-medium overflow-hidden hover:shadow-md transition-all duration-300 dark:bg-gray-800 dark:border-gray-700">
                            <div class="h-36 bg-neutral-secondary-soft dark:bg-gray-700 overflow-hidden">
                                @if ($relatedPost->cover_image)
                                    <img src="{{ Storage::url($relatedPost->cover_image) }}"
                                        alt="{{ $locale === 'ar' && $relatedPost->title_ar ? $relatedPost->title_ar : $relatedPost->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <x-lucide-newspaper class="w-8 h-8 text-body dark:text-gray-500" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <p class="text-xs text-body dark:text-gray-400 mb-1">
                                    {{ $relatedPost->published_at->format('M d, Y') }}
                                </p>
                                <h3 class="text-sm font-semibold text-heading dark:text-white group-hover:text-fg-brand line-clamp-2 transition-colors duration-150">
                                    {{ $locale === 'ar' && $relatedPost->title_ar ? $relatedPost->title_ar : $relatedPost->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection
