{{--
    Latest-from-the-blog band. `id="blog"` is the section anchor.

    Real data only: the actual author, publication date, category and computed
    read time. The reference showed a fake "3 Comments" on every card, which is
    replaced by the Blog model's own read_time accessor.

    The section hides entirely when nothing is published, rather than rendering an
    empty shell.
--}}
@if ($latestBlogs->isNotEmpty())
    <section class="py-20 lg:py-24 bg-slate-50 dark:bg-slate-950 border-b border-slate-200/80 dark:border-slate-800/80 relative overflow-hidden scroll-mt-0" id="blog">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-14 flex flex-col items-center">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <span class="h-0.5 w-6 bg-amber-400"></span>
                    <span class="text-xs font-bold tracking-widest text-amber-600 dark:text-amber-400 uppercase font-mono">{{ __('messages.landing.blog.eyebrow') }}</span>
                    <span class="h-0.5 w-6 bg-amber-400"></span>
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight font-sans">
                    {{ __('messages.landing.blog.heading') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                @php
                    $featured = $latestBlogs->first();
                    $secondary = $latestBlogs->slice(1);
                    $isArabic = app()->getLocale() === 'ar';
                @endphp

                {{-- Featured card --}}
                <div class="lg:col-span-6 h-full">
                    <article class="group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between h-full">
                        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100 dark:bg-slate-800">
                            @if ($featured->cover_image)
                                <img src="{{ $featured->cover_image }}" alt="{{ $featured->title }}"
                                    width="640" height="400" loading="lazy" decoding="async"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                {{-- Placeholder: no cover image is a normal state here, and
                                     every current blog row is in it. --}}
                                <div data-placeholder-cover class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-50 to-slate-100 dark:from-amber-950/30 dark:to-slate-800">
                                    <svg class="w-12 h-12 text-amber-400/70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                    </svg>
                                </div>
                            @endif

                            @if ($featured->category)
                                <span class="absolute bottom-3 end-3 px-3 py-1 rounded-full text-xs font-bold text-white bg-amber-600 shadow-md tracking-wide">
                                    {{ $isArabic && $featured->category->name_ar ? $featured->category->name_ar : $featured->category->name }}
                                </span>
                            @endif
                        </div>

                        <div class="p-6 sm:p-8 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-4 text-xs font-medium text-slate-500 dark:text-slate-400 mb-3">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                            <line x1="16" x2="16" y1="2" y2="6"></line>
                                            <line x1="8" x2="8" y1="2" y2="6"></line>
                                            <line x1="3" x2="21" y1="10" y2="10"></line>
                                        </svg>
                                        {{ $featured->published_at->translatedFormat('M d, Y') }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                        </svg>
                                        {{ __('messages.blog.read_time', ['minutes' => $featured->read_time]) }}
                                    </span>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-6 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition leading-snug">
                                    <a href="{{ route('blog.show', $featured) }}" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 rounded">
                                        {{ $isArabic && $featured->title_ar ? $featured->title_ar : $featured->title }}
                                    </a>
                                </h3>
                            </div>

                            <div class="flex items-center justify-between pt-5 border-t border-slate-100 dark:border-slate-800">
                                @if ($featured->author)
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center font-bold text-amber-700 dark:text-amber-300 text-sm overflow-hidden shrink-0 shadow-sm">
                                            {{ mb_substr($featured->author->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white leading-tight">{{ $featured->author->name }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $featured->published_at->translatedFormat('M d, Y') }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span></span>
                                @endif

                                <a href="{{ route('blog.show', $featured) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 transition shadow-md shadow-amber-600/30">
                                    <span>{{ __('messages.landing.blog.read_more') }}</span>
                                    <span class="w-5 h-5 rounded-full bg-slate-900/20 flex items-center justify-center text-xs rtl:rotate-180" aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>

                {{-- Two smaller cards --}}
                <div class="lg:col-span-6 flex flex-col gap-6 h-full">
                    @foreach ($secondary as $post)
                        <article class="group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 grid grid-cols-1 sm:grid-cols-12 flex-1">
                            <div class="relative sm:col-span-5 aspect-[16/10] sm:aspect-auto overflow-hidden bg-slate-100 dark:bg-slate-800">
                                @if ($post->cover_image)
                                    <img src="{{ $post->cover_image }}" alt="{{ $post->title }}"
                                        width="320" height="320" loading="lazy" decoding="async"
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-50 to-slate-100 dark:from-amber-950/30 dark:to-slate-800">
                                        <svg class="w-9 h-9 text-amber-400/70" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                        </svg>
                                    </div>
                                @endif

                                @if ($post->category)
                                    <span class="absolute bottom-3 end-3 px-3 py-1 rounded-full text-xs font-bold text-white bg-amber-600 shadow-md tracking-wide">
                                        {{ $isArabic && $post->category->name_ar ? $post->category->name_ar : $post->category->name }}
                                    </span>
                                @endif
                            </div>

                            <div class="sm:col-span-7 p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-3 text-xs font-medium text-slate-500 dark:text-slate-400 mb-2">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                                <line x1="3" x2="21" y1="10" y2="10"></line>
                                            </svg>
                                            {{ $post->published_at->translatedFormat('M d, Y') }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                            </svg>
                                            {{ __('messages.blog.read_time', ['minutes' => $post->read_time]) }}
                                        </span>
                                    </div>

                                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition leading-snug">
                                        <a href="{{ route('blog.show', $post) }}" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 rounded">
                                            {{ $isArabic && $post->title_ar ? $post->title_ar : $post->title }}
                                        </a>
                                    </h3>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                                    @if ($post->author)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center font-bold text-amber-700 dark:text-amber-300 text-xs overflow-hidden shrink-0 shadow-sm">
                                                {{ mb_substr($post->author->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ $post->author->name }}</p>
                                                <p class="text-[11px] text-slate-500 dark:text-slate-400">{{ $post->published_at->translatedFormat('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span></span>
                                    @endif

                                    <a href="{{ route('blog.show', $post) }}" aria-label="{{ __('messages.landing.blog.read_more') }}"
                                        class="w-9 h-9 rounded-full bg-amber-600 hover:bg-amber-700 text-white flex items-center justify-center shadow-md shadow-amber-600/30 transition">
                                        <span class="text-sm leading-none rtl:rotate-180" aria-hidden="true">&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif