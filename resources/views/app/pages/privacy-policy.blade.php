@extends('app.layouts.master')

@section('title', __('messages.pages.privacy.title'))

@section('meta_description')
Privacy Policy for VetPedia veterinary medical knowledge platform.
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-4">

        {{-- Header --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-8 dark:bg-gray-800">
            <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-2">{{ __('messages.pages.privacy.heading') }}</h1>
            <p class="text-base text-body dark:text-gray-400">{{ __('messages.pages.privacy.last_updated') }}: {{ now()->format('F d, Y') }}</p>
        </div>

        {{-- Sections --}}
        @php
            $sections = [
                'intro' => ['icon' => 'info', 'title' => __('messages.pages.privacy.intro_title'), 'text' => __('messages.pages.privacy.intro_text')],
                'collect' => ['icon' => 'database', 'title' => __('messages.pages.privacy.collect_title'), 'text' => __('messages.pages.privacy.collect_text')],
                'use' => ['icon' => 'search', 'title' => __('messages.pages.privacy.use_title'), 'list' => __('messages.pages.privacy.use_items')],
                'cookies' => ['icon' => 'cookie', 'title' => __('messages.pages.privacy.cookies_title'), 'text' => __('messages.pages.privacy.cookies_text')],
                'third_party' => ['icon' => 'share-2', 'title' => __('messages.pages.privacy.third_party_title'), 'text' => __('messages.pages.privacy.third_party_text')],
                'security' => ['icon' => 'shield', 'title' => __('messages.pages.privacy.security_title'), 'text' => __('messages.pages.privacy.security_text')],
                'accuracy' => ['icon' => 'check-circle', 'title' => __('messages.pages.privacy.accuracy_title'), 'text' => __('messages.pages.privacy.accuracy_text')],
                'changes' => ['icon' => 'refresh-cw', 'title' => __('messages.pages.privacy.changes_title'), 'text' => __('messages.pages.privacy.changes_text')],
            ];
        @endphp

        @foreach ($sections as $key => $section)
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-8 dark:bg-gray-800">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-10 h-10 bg-brand-soft rounded-base flex items-center justify-center">
                        <x-dynamic-component :component="'lucide-' . $section['icon']" class="w-5 h-5 text-fg-brand" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-heading dark:text-white mb-2">{{ $section['title'] }}</h2>
                        @if (isset($section['list']))
                            <ul class="space-y-1.5">
                                @foreach ($section['list'] as $item)
                                    <li class="flex items-start gap-2 text-base text-body dark:text-gray-400">
                                        <x-lucide-check class="w-4 h-4 text-fg-brand mt-1 shrink-0" />
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-base text-body dark:text-gray-400">{{ $section['text'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Contact --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-8 dark:bg-gray-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-10 h-10 bg-brand-soft rounded-base flex items-center justify-center">
                        <x-lucide-message-circle class="w-5 h-5 text-fg-brand" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.pages.privacy.contact_title') }}</h2>
                        <p class="text-base text-body dark:text-gray-400">{{ __('messages.pages.privacy.contact_text') }}</p>
                    </div>
                </div>
                <a href="{{ route('contact') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm shrink-0">
                    {{ __('messages.pages.privacy.contact_button') }}
                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                </a>
            </div>
        </div>

    </div>
@endsection
