@extends('app.layouts.master')

@section('title', __('messages.pages.contact.title'))

@section('meta_description')
Contact VetPedia for support, feedback, partnerships, data contributions, and veterinary industry collaboration.
@endsection

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-8 dark:bg-gray-800">
            <div class="max-w-3xl">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-2">{{ __('messages.pages.contact.heading') }}</h1>
                <p class="text-base text-body dark:text-gray-400 font-medium">{{ __('messages.pages.contact.subtitle') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Left: Contact Info & Categories --}}
            <div class="lg:col-span-5 xl:col-span-4 space-y-4">

                {{-- Contact Information --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-6 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-heading dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-mail class="w-5 h-5 text-fg-brand" />
                        {{ __('messages.pages.contact.info_title') }}
                    </h2>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-brand-soft rounded-base flex items-center justify-center shrink-0">
                                <x-lucide-mail class="w-4 h-4 text-fg-brand" />
                            </div>
                            <div>
                                <span class="block text-sm uppercase text-body dark:text-gray-400">Email</span>
                                <a href="mailto:{{ __('messages.pages.contact.email') }}" class="text-base font-medium text-fg-brand hover:underline">
                                    {{ __('messages.pages.contact.email') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-6 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-heading dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-list class="w-5 h-5 text-fg-brand" />
                        {{ __('messages.pages.contact.categories_title') }}
                    </h2>
                    <ul class="space-y-2">
                        @foreach (__('messages.pages.contact.categories') as $category)
                            <li class="flex items-center gap-2 text-base text-body dark:text-gray-400">
                                <x-lucide-chevron-right class="w-4 h-4 text-fg-brand shrink-0 rtl:rotate-180" />
                                {{ $category }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Response Time --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 bg-brand-soft rounded-base flex items-center justify-center shrink-0">
                            <x-lucide-clock class="w-4 h-4 text-fg-brand" />
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-heading dark:text-white mb-1">{{ __('messages.pages.contact.response_title') }}</h3>
                            <p class="text-base text-body dark:text-gray-400">{{ __('messages.pages.contact.response_text') }}</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: Contact Form --}}
            <div class="lg:col-span-7 xl:col-span-8">

                <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 sm:p-8 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-heading dark:text-white mb-5">{{ __('messages.pages.contact.form_title') }}</h2>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-success-soft border border-success-subtle rounded-base text-sm text-fg-success-strong dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact') }}">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-heading dark:text-white mb-1">{{ __('messages.pages.contact.name_label') }}</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('name') border-danger-subtle @enderror"
                                    placeholder="{{ __('messages.pages.contact.name_placeholder') }}">
                                @error('name')
                                    <p class="mt-1 text-sm text-fg-danger-strong">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-heading dark:text-white mb-1">{{ __('messages.pages.contact.email_label') }}</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('email') border-danger-subtle @enderror"
                                    placeholder="{{ __('messages.pages.contact.email_placeholder') }}">
                                @error('email')
                                    <p class="mt-1 text-sm text-fg-danger-strong">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-heading dark:text-white mb-1">{{ __('messages.pages.contact.subject_label') }}</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('subject') border-danger-subtle @enderror"
                                placeholder="{{ __('messages.pages.contact.subject_placeholder') }}">
                            @error('subject')
                                <p class="mt-1 text-sm text-fg-danger-strong">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="message" class="block text-sm font-medium text-heading dark:text-white mb-1">{{ __('messages.pages.contact.message_label') }}</label>
                            <textarea name="message" id="message" rows="5"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('message') border-danger-subtle @enderror"
                                placeholder="{{ __('messages.pages.contact.message_placeholder') }}">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-fg-danger-strong">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm">
                            <x-lucide-send class="w-4 h-4" />
                            {{ __('messages.pages.contact.submit') }}
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>
@endsection
