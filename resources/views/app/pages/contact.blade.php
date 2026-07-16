@extends('app.layouts.master')

@section('title', __('messages.pages.contact.title'))

@section('meta_description')
Contact VetPedia for support, feedback, partnerships, data contributions, and veterinary industry collaboration.
@endsection

@section('content')
<x-toast id="contactToast" type="success" title="Success" message="" />

<div class="max-w-7xl mx-auto space-y-8 py-8 sm:py-12">

  {{-- Hero --}}
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm">

    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-200 dark:text-sky-200 border border-white/10 dark:border-sky-700 mb-5">
        <x-lucide-message-circle class="w-4 h-4" />
        <span>{{ __('messages.nav.contact') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.contact.heading') }}
      </h1>
      <div class="w-16 h-1 bg-blue-400 dark:bg-sky-400 rounded-full mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-slate-300 dark:text-sky-200 max-w-xl leading-relaxed">
        {{ __('messages.pages.contact.subtitle') }}
      </p>
    </div>
  </div>

  {{-- Content --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Left: Contact Info --}}
    <div class="lg:col-span-5 xl:col-span-4 space-y-5">

      {{-- Email --}}
      <div class="p-6 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
          <x-lucide-mail class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          {{ __('messages.pages.contact.info_title') }}
        </h2>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shrink-0">
            <x-lucide-mail class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          </div>
          <div>
            <span class="block text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wide">Email</span>
            <a href="mailto:{{ __('messages.pages.contact.email') }}" class="text-base font-semibold text-blue-600 dark:text-blue-400 hover:underline">
              {{ __('messages.pages.contact.email') }}
            </a>
          </div>
        </div>
      </div>

      {{-- Categories --}}
      <div class="p-6 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
          <x-lucide-list class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          {{ __('messages.pages.contact.categories_title') }}
        </h2>
        <ul class="space-y-2.5">
          @foreach (__('messages.pages.contact.categories') as $category)
            <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
              <x-lucide-chevron-right class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0 rtl:rotate-180" />
              {{ $category }}
            </li>
          @endforeach
        </ul>
      </div>

      {{-- Response Time --}}
      <div class="p-6 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shrink-0">
            <x-lucide-clock class="w-4 h-4 text-blue-600 dark:text-blue-400" />
          </div>
          <div>
            <h3 class="font-semibold text-slate-900 dark:text-white mb-1">{{ __('messages.pages.contact.response_title') }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('messages.pages.contact.response_text') }}</p>
          </div>
        </div>
      </div>

    </div>

    {{-- Right: Contact Form --}}
    <div class="lg:col-span-7 xl:col-span-8">
      <div class="p-6 sm:p-8 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">{{ __('messages.pages.contact.form_title') }}</h2>

        <form id="contactForm" method="POST" action="{{ route('contact') }}">
          @csrf

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
            <div>
              <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('messages.pages.contact.name_label') }}</label>
              <input type="text" name="name" id="name" value="{{ old('name') }}"
                class="block w-full px-3.5 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500 @error('name') border-red-400 @enderror"
                placeholder="{{ __('messages.pages.contact.name_placeholder') }}">
            </div>
            <div>
              <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('messages.pages.contact.email_label') }}</label>
              <input type="email" name="email" id="email" value="{{ old('email') }}"
                class="block w-full px-3.5 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500 @error('email') border-red-400 @enderror"
                placeholder="{{ __('messages.pages.contact.email_placeholder') }}">
            </div>
          </div>

          <div class="mb-5">
            <label for="subject" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('messages.pages.contact.subject_label') }}</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
              class="block w-full px-3.5 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500 @error('subject') border-red-400 @enderror"
              placeholder="{{ __('messages.pages.contact.subject_placeholder') }}">
          </div>

          <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('messages.pages.contact.message_label') }}</label>
            <textarea name="message" id="message" rows="5"
              class="block w-full px-3.5 py-2.5 text-sm text-slate-900 dark:text-white bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-xs placeholder:text-slate-400 dark:placeholder:text-slate-500 resize-y @error('message') border-red-400 @enderror"
              placeholder="{{ __('messages.pages.contact.message_placeholder') }}">{{ old('message') }}</textarea>
          </div>

          <button type="submit"
            class="inline-flex items-center gap-2 px-6 py-3 text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-semibold rounded-xl text-sm transition-colors shadow-sm">
            <x-lucide-send class="w-4 h-4" />
            {{ __('messages.pages.contact.submit') }}
          </button>
        </form>
      </div>
    </div>

  </div>

</div>
@endsection
