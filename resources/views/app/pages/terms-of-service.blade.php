@extends('app.layouts.master')

@section('title', __('messages.pages.terms.title'))

@section('meta_description')
Terms of Service governing the use of the VetPedia veterinary medical knowledge platform.
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-4">

        {{-- Header --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-8 dark:bg-gray-800">
            <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-2">{{ __('messages.pages.terms.heading') }}</h1>
            <p class="text-base text-body dark:text-gray-400">{{ __('messages.pages.terms.last_updated') }}: {{ now()->format('F d, Y') }}</p>
        </div>

        {{-- Sections --}}
        @php
            $sections = [
                'acceptance' => ['icon' => 'file-text', 'title' => __('messages.pages.terms.acceptance_title'), 'text' => __('messages.pages.terms.acceptance_text')],
                'purpose' => ['icon' => 'target', 'title' => __('messages.pages.terms.purpose_title'), 'text' => __('messages.pages.terms.purpose_text')],
                'no_advice' => ['icon' => 'alert-triangle', 'title' => __('messages.pages.terms.no_advice_title'), 'text' => __('messages.pages.terms.no_advice_text')],
                'accuracy' => ['icon' => 'check-circle', 'title' => __('messages.pages.terms.accuracy_title'), 'text' => __('messages.pages.terms.accuracy_text')],
                'ip' => ['icon' => 'copyright', 'title' => __('messages.pages.terms.ip_title'), 'text' => __('messages.pages.terms.ip_text')],
                'conduct' => ['icon' => 'shield', 'title' => __('messages.pages.terms.conduct_title'), 'text' => __('messages.pages.terms.conduct_text')],
                'third_party' => ['icon' => 'external-link', 'title' => __('messages.pages.terms.third_party_title'), 'text' => __('messages.pages.terms.third_party_text')],
                'liability' => ['icon' => 'scale', 'title' => __('messages.pages.terms.liability_title'), 'text' => __('messages.pages.terms.liability_text')],
                'changes' => ['icon' => 'refresh-cw', 'title' => __('messages.pages.terms.changes_title'), 'text' => __('messages.pages.terms.changes_text')],
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
                        <p class="text-base text-body dark:text-gray-400">{{ $section['text'] }}</p>
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
                        <h2 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.pages.terms.contact_title') }}</h2>
                        <p class="text-base text-body dark:text-gray-400">{{ __('messages.pages.terms.contact_text') }}</p>
                    </div>
                </div>
                <a href="{{ route('contact') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm shrink-0">
                    {{ __('messages.pages.terms.contact_button') }}
                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                </a>
            </div>
        </div>

    </div>
@endsection
