@extends('app.layouts.master')

@section('title', __('messages.blog.index_title'))

@section('content')
    <div class="space-y-4">

        <x-page-hero
            :heading="__('messages.blog.index_heading')"
            :subtitle="__('messages.blog.index_subtitle')"
            :stats="[
                ['count' => number_format($blogs->total()), 'label' => __('messages.blog.articles_label'), 'icon' => 'newspaper'],
            ]"
        />

        {{-- Search & Filters --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-3">
                    <div class="lg:col-span-1 relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            placeholder="{{ __('messages.blog.search_placeholder') }}">
                    </div>

                    <div id="advanced-filters" class="sm:contents max-sm:flex max-sm:flex-col max-sm:gap-4">
                        <select name="category"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">{{ __('messages.blog.all_categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                                    {{ app()->getLocale() === 'ar' && $category->name_ar ? $category->name_ar : $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="sort"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="latest" @selected(request('sort') == 'latest')>{{ __('messages.blog.sort_latest') }}</option>
                            <option value="oldest" @selected(request('sort') == 'oldest')>{{ __('messages.blog.sort_oldest') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2 focus:outline-none">
                            <x-lucide-search class="w-3.5 h-3.5 inline -mt-0.5 me-1" />
                            {{ __('messages.blog.search_button') }}
                        </button>
                        <a href="{{ route('blog.index') }}" wire:navigate
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600">
                            <x-lucide-x class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                <button type="button" onclick="var f=document.getElementById('advanced-filters');var s=f.style;s.display=s.display==='none'?'':'none';this.querySelector('.chevron').classList.toggle('rotate-180')"
                    class="max-sm:flex sm:hidden mt-3 w-full items-center justify-center gap-1.5 text-sm text-fg-brand hover:underline cursor-pointer">
                    <span>{{ __('messages.blog.filter_by') }}</span>
                    <x-lucide-chevron-down class="chevron w-4 h-4 transition-transform" />
                </button>
            </form>
        </div>

        {{-- Blog Cards Grid --}}
        @if ($blogs->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($blogs as $blog)
                    <article class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden hover:shadow-md transition-all duration-300 border border-default-medium dark:bg-slate-800 dark:border-slate-700 group flex flex-col">
                        <a href="{{ route('blog.show', $blog) }}" wire:navigate class="flex flex-col h-full">
                            {{-- Cover Image --}}
                            <div class="relative h-48 bg-neutral-secondary-soft dark:bg-slate-700 overflow-hidden shrink-0">
                                @if ($blog->cover_image)
                                    <img src="{{ Storage::url($blog->cover_image) }}"
                                        alt="{{ app()->getLocale() === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <x-lucide-newspaper class="w-12 h-12 text-body dark:text-slate-500" />
                                    </div>
                                @endif
                                {{-- Category Badge --}}
                                @if ($blog->category)
                                    <span class="absolute top-3 start-3 px-2.5 py-1 text-xs font-semibold rounded-base bg-brand text-white shadow-xs">
                                        {{ app()->getLocale() === 'ar' && $blog->category->name_ar ? $blog->category->name_ar : $blog->category->name }}
                                    </span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-5 flex flex-col flex-1">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 text-xs text-body dark:text-slate-400 mb-2">
                                        @if ($blog->author)
                                            <span>{{ $blog->author->name }}</span>
                                            <span>·</span>
                                        @endif
                                        <time datetime="{{ $blog->published_at->format('Y-m-d') }}">
                                            {{ $blog->published_at->format('M d, Y') }}
                                        </time>
                                        <span>·</span>
                                        <span class="flex items-center gap-1">
                                            <x-lucide-clock class="w-3 h-3" />
                                            {{ __('messages.blog.read_time', ['minutes' => $blog->read_time]) }}
                                        </span>
                                    </div>

                                    <h2 class="text-lg font-semibold text-heading dark:text-white mb-2 line-clamp-2 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors duration-150">
                                        {{ app()->getLocale() === 'ar' && $blog->title_ar ? $blog->title_ar : $blog->title }}
                                    </h2>

                                    @php $excerpt = app()->getLocale() === 'ar' && $blog->excerpt_ar ? $blog->excerpt_ar : $blog->excerpt; @endphp
                                    @if ($excerpt)
                                        <p class="text-sm text-body dark:text-slate-400 line-clamp-3">
                                            {{ $excerpt }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center text-sm font-medium text-fg-brand pt-3">
                                    {{ __('messages.blog.read_more') }}
                                    <x-lucide-arrow-right class="w-4 h-4 ms-1 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform duration-150" />
                                </div>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-newspaper class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h3 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ __('messages.blog.no_posts') }}</h3>
                <p class="text-body dark:text-slate-400 mb-6 max-w-md mx-auto">{{ __('messages.blog.no_posts_desc') }}</p>
                <a href="{{ route('blog.index') }}" wire:navigate
                    class="inline-flex items-center gap-2 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-5 py-2.5">
                    <x-lucide-refresh-cw class="w-4 h-4" />
                    {{ __('messages.blog.clear_filters') }}
                </a>
            </div>
        @endif

        <x-pagination :paginator="$blogs" translation-prefix="messages.blog" />

    </div>
@endsection
