@extends('app.layouts.master')

@section('title', __('messages.diseases.index_title'))

@section('content')
    <div class="space-y-4">

        <x-page-hero
            :heading="__('messages.diseases.index_heading')"
            :subtitle="__('messages.diseases.index_subtitle')"
            :stats="[
                ['count' => number_format($diseases->total()), 'label' => __('messages.diseases.diseases_label'), 'icon' => 'activity'],
            ]"
        />

        {{-- Search --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="relative sm:col-span-2">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-body" />
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="{{ __('messages.diseases.search_placeholder') }}">
                    </div>
                    <div class="flex justify-between gap-2">
                        <button type="submit"
                            class="w-50 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            {{ __('messages.diseases.search_button') }}
                        </button>
                        <a href="{{ route('diseases.index') }}"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                            <x-lucide-x class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Results Table --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 overflow-hidden">
            <div class="px-5 py-4 border-b border-default-medium flex items-center justify-between">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.directory_title') }}</h2>
                <span class="text-base text-body dark:text-gray-400">
                    {{ __('messages.diseases.showing') }}
                    {{ $diseases->firstItem() ?? 0 }}–{{ $diseases->lastItem() ?? 0 }}
                    {{ __('messages.diseases.of') }}
                    {{ number_format($diseases->total()) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-base text-left rtl:text-right text-heading dark:text-white">
                    <thead class="text-base uppercase text-body bg-neutral-secondary-soft dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-5 py-3">{{ __('messages.diseases.name') }}</th>
                            <th scope="col" class="px-5 py-3">{{ __('messages.diseases.products_count') }}</th>
                            <th scope="col" class="px-5 py-3 w-20">{{ __('messages.diseases.details') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($diseases as $disease)
                            <tr class="border-b border-default-medium hover:bg-neutral-secondary-soft dark:hover:bg-gray-700 dark:border-gray-700">
                                <td class="px-5 py-4">
                                    <a href="{{ route('diseases.show', $disease) }}" class="font-semibold text-fg-brand hover:underline">
                                        {{ $disease->name }}
                                    </a>
                                    @if ($disease->name_ar && app()->getLocale() === 'ar')
                                        <p class="text-sm text-body dark:text-gray-400 mt-0.5">{{ $disease->name_ar }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 text-sm font-semibold bg-neutral-secondary-soft text-heading border border-default-medium rounded-base dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                        {{ $disease->products_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('diseases.show', $disease) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm">
                                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center">
                                    <x-lucide-activity class="w-10 h-10 text-body mx-auto mb-3" />
                                    <h3 class="text-base font-semibold text-heading dark:text-white mb-1">{{ __('messages.diseases.no_diseases') }}</h3>
                                    <p class="text-base text-body dark:text-gray-400">{{ __('messages.diseases.try_another') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($diseases->hasPages())
            <div class="w-full">
                {{ $diseases->withQueryString()->links('pagination::tailwind') }}
            </div>
        @endif

    </div>
@endsection
