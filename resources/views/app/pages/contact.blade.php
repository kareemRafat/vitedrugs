@extends('app.layouts.master')

@section('title', __('messages.pages.contact.title'))

@section('meta_description')
Contact VetPedia for support, feedback, partnerships, data contributions, and veterinary industry collaboration.
@endsection

@section('content')
<x-toast id="contactToast" type="success" title="" message="" />
<div class="max-w-7xl mx-auto space-y-8 pb-8 sm:pb-12">

  {{-- Hero --}}
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand to-brand-strong dark:from-brand-subtle dark:to-slate-900 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm mt-4">

    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-white/80 border border-white/10 mb-5">
        <x-lucide-message-circle class="w-4 h-4" />
        <span>{{ __('messages.nav.contact') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.contact.heading') }}
      </h1>
      <div class="w-16 h-1 bg-white/60 rounded-full mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-white/90 max-w-xl leading-relaxed">
        {{ __('messages.pages.contact.subtitle') }}
      </p>
    </div>
  </div>

  {{-- Content --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Left: Contact Info --}}
    <div class="lg:col-span-5 xl:col-span-4 space-y-5">

      {{-- Email --}}
      <div class="p-6 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700  shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
          <x-lucide-mail class="w-5 h-5 text-brand" />
          {{ __('messages.pages.contact.info_title') }}
        </h2>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-brand-soft rounded-xl flex items-center justify-center shrink-0">
            <x-lucide-mail class="w-5 h-5 text-brand" />
          </div>
          <div>
            <span class="block text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wide">Email</span>
            <a href="mailto:{{ __('messages.pages.contact.email') }}" class="text-base font-semibold text-fg-brand hover:underline">
              {{ __('messages.pages.contact.email') }}
            </a>
          </div>
        </div>
      </div>

      {{-- Categories --}}
      <div class="p-6 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700  shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
          <x-lucide-list class="w-5 h-5 text-brand" />
          {{ __('messages.pages.contact.categories_title') }}
        </h2>
        <ul class="space-y-2.5">
          @foreach (__('messages.pages.contact.categories') as $category)
            <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
              <x-lucide-chevron-right class="w-4 h-4 text-brand-medium shrink-0 rtl:rotate-180" />
              {{ $category }}
            </li>
          @endforeach
        </ul>
      </div>

      {{-- Response Time --}}
      <div class="p-6 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700  shadow-sm">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 bg-brand-soft rounded-xl flex items-center justify-center shrink-0">
            <x-lucide-clock class="w-4 h-4 text-fg-brand" />
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
      <div class="p-6 sm:p-8 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700  shadow-sm">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6">{{ __('messages.pages.contact.form_title') }}</h2>

        @livewire('contact.contact-form', key('contact-form'))
      </div>
    </div>

  </div>

</div>
@endsection
