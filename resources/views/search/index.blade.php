@extends('app.layouts.master')

@section('title', __('search.title'))

@section('content')
    <div class="space-y-4">

        <x-large-animals.page-hero
            :heading="__('search.heading')"
            :subtitle="__('search.subtitle')"
            :badge="__('search.badge')"
            badgeIcon="search"
        >
            <form method="GET" action="{{ route($part . '.search') }}">
                <div class="relative w-full sm:w-1/2">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="text" name="q" value="{{ $term }}"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 pe-32 px-3 py-3 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        placeholder="{{ __('search.placeholder') }}">
                    <button type="submit"
                        class="absolute inset-y-1 end-1 inline-flex items-center justify-center gap-1.5 px-4 text-sm font-semibold text-white bg-brand hover:bg-brand-strong rounded-base focus:outline-none">
                        <x-lucide-search class="w-4 h-4" />
                        {{ __('search.button') }}
                    </button>
                </div>
            </form>
        </x-large-animals.page-hero>

        @if (! $searched)

            {{-- Nothing typed yet --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-search class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h2 class="text-xl font-semibold text-heading dark:text-white mb-2">{{ __('search.heading') }}</h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('search.no_query') }}</p>
            </div>

        @elseif ($total === 0)

            {{-- Typed, matched nothing --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-12 text-center dark:bg-slate-800">
                <x-lucide-search-x class="w-16 h-16 text-body mx-auto mb-4 dark:text-slate-500" />
                <h2 class="text-xl font-semibold text-heading dark:text-white mb-2">
                    {{ __('search.no_results_title', ['q' => $term]) }}
                </h2>
                <p class="text-base text-body dark:text-slate-400">{{ __('search.no_results') }}</p>

                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    <a href="{{ route($part . '.filter') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-fg-brand border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:text-white dark:hover:bg-slate-700">
                        <x-lucide-filter class="w-4 h-4" />
                        {{ __('search.browse_by_sign') }}
                    </a>
                    <a href="{{ route($part . '.diagnosis') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-fg-brand border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:text-white dark:hover:bg-slate-700">
                        <x-lucide-stethoscope class="w-4 h-4" />
                        {{ __('search.run_diagnosis') }}
                    </a>
                </div>
            </div>

        @else

            {{-- Results --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs px-5 py-4 dark:bg-slate-800 flex flex-wrap items-center gap-3">
                <h2 class="text-base font-semibold text-heading dark:text-white">
                    {{ __('search.results_for', ['q' => $term]) }}
                </h2>
                <span class="inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">
                    {{ $total }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($groups as $group)
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-dynamic-component :component="'lucide-' . $group['icon']" class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $group['label'] }}</h2>
                            <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">
                                {{ count($group['items']) }}
                            </span>
                        </div>

                        <div class="p-5">
                            @forelse ($group['items'] as $item)
                                <div class="flex items-center justify-between gap-3 py-2 border-b border-default-medium last:border-0">
                                    <span class="min-w-0">
                                        @if ($item['url'])
                                            <a href="{{ $item['url'] }}" wire:navigate
                                                class="block text-base font-medium text-fg-brand hover:underline truncate">{{ $item['title'] }}</a>
                                        @else
                                            <span class="block text-base font-medium text-heading dark:text-white truncate">{{ $item['title'] }}</span>
                                        @endif

                                        @if ($item['secondary'] && $item['secondary'] !== $item['title'])
                                            <span class="block text-sm text-body dark:text-slate-400 truncate">{{ $item['secondary'] }}</span>
                                        @endif
                                    </span>

                                    @if ($item['url'])
                                        <a href="{{ $item['url'] }}" wire:navigate
                                            class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                                            <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                        </a>
                                    @endif
                                </div>
                            @empty
                                <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('search.no_section_results') }}</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

        @endif

</div>
@endsection