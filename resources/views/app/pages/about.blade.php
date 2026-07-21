@extends('app.layouts.master')

@section('title', __('messages.pages.about.title'))

@section('meta_description')
{{ __('messages.pages.about.subtitle') }}
@endsection

@section('content')
<div id="about-page" class="max-w-7xl mx-auto space-y-12 py-8 sm:py-12">

  {{-- Hero --}}
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm mt-6">

    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-100 dark:text-sky-200 border border-white/10 dark:border-sky-700 mb-5">
        <x-lucide-info class="w-4 h-4" />
        <span>{{ __('messages.nav.about') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.about.heading') }}
      </h1>
      <div class="w-16 h-1 bg-cyan-300 dark:bg-sky-400 rounded-full mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-blue-50 dark:text-sky-100 max-w-xl leading-relaxed">
        {{ __('messages.pages.about.subtitle') }}
      </p>
    </div>
  </div>

  {{-- Mission / Vision --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="p-6 sm:p-8 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700  shadow-sm">
      <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
        <x-lucide-heart class="w-6 h-6 text-blue-600 dark:text-blue-400" />
      </div>
      <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('messages.pages.about.mission_title') }}</h2>
      <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
        {{ __('messages.pages.about.mission_text') }}
      </p>
    </div>
    <div class="p-6 sm:p-8 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700  shadow-sm">
      <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
        <x-lucide-eye class="w-6 h-6 text-blue-600 dark:text-blue-400" />
      </div>
      <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ __('messages.pages.about.vision_title') }}</h2>
      <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
        {{ __('messages.pages.about.vision_text') }}
      </p>
    </div>
  </div>

  {{-- What VetPedia Provides --}}
  <div>
    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ __('messages.pages.about.features_title') }}</h2>
    <p class="text-slate-500 dark:text-slate-400 mb-8">{{ __('messages.pages.about.features_subtitle') }}</p>
    @php
      $featuresList = [
        ['key' => 'disease_db', 'icon' => 'stethoscope', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'tc' => 'text-emerald-600 dark:text-emerald-400'],
        ['key' => 'product_db', 'icon' => 'pill', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'tc' => 'text-blue-600 dark:text-blue-400'],
        ['key' => 'ingredient_lib', 'icon' => 'flask-conical', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'tc' => 'text-purple-600 dark:text-purple-400'],
        ['key' => 'company_dir', 'icon' => 'building-2', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'tc' => 'text-amber-600 dark:text-amber-400'],
        ['key' => 'disease_product', 'icon' => 'git-compare', 'bg' => 'bg-rose-50 dark:bg-rose-900/20', 'tc' => 'text-rose-600 dark:text-rose-400'],
        ['key' => 'ingredient_product', 'icon' => 'git-branch', 'bg' => 'bg-cyan-50 dark:bg-cyan-900/20', 'tc' => 'text-cyan-600 dark:text-cyan-400'],
        ['key' => 'clinical_tools', 'icon' => 'activity', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'tc' => 'text-indigo-600 dark:text-indigo-400'],
        ['key' => 'search_nav', 'icon' => 'search', 'bg' => 'bg-orange-50 dark:bg-orange-900/20', 'tc' => 'text-orange-600 dark:text-orange-400'],
      ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      @foreach ($featuresList as $item)
        @php $feature = __('messages.pages.about.features.' . $item['key']); @endphp
        <div class="p-5 bg-white dark:bg-slate-800/90 rounded-xl border border-slate-200 dark:border-slate-700  shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="w-10 h-10 {{ $item['bg'] }} rounded-lg flex items-center justify-center mb-4">
            @svg('lucide-' . $item['icon'], 'w-5 h-5 ' . $item['tc'])
          </div>
          <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $feature }}</p>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Data Sources --}}
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
        <x-lucide-database class="w-5 h-5 text-blue-600 dark:text-blue-400" />
      </div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('messages.pages.about.sources_title') }}</h2>
    </div>
    <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
      {{ __('messages.pages.about.sources_text') }}
    </p>
  </div>

  {{-- Disclaimer --}}
  <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-6 sm:p-8">
    <div class="flex items-start gap-4">
      <x-lucide-alert-triangle class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0 mt-1" />
      <div>
        <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-1">{{ __('messages.pages.about.disclaimer_title') }}</h3>
        <p class="text-amber-700 dark:text-amber-300">{{ __('messages.pages.about.disclaimer_text') }}</p>
      </div>
    </div>
  </div>

  {{-- CTA --}}
  <div class="bg-gradient-to-br from-slate-900 to-blue-900 rounded-2xl p-8 sm:p-12 text-white">
    <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-10">
      <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center shrink-0">
        <x-lucide-message-circle class="w-8 h-8 text-blue-300" />
      </div>
      <div class="flex-1 text-center sm:text-start">
        <h2 class="text-xl sm:text-2xl font-bold">{{ __('messages.pages.about.contact_title') }}</h2>
        <p class="text-slate-300 text-sm sm:text-base mt-1 max-w-xl">
          {{ __('messages.pages.about.contact_text') }}
        </p>
      </div>
      <div class="flex gap-3 shrink-0">
        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all shadow-lg">
          <x-lucide-mail class="w-4 h-4" />
          {{ __('messages.pages.about.contact_button') }}
        </a>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 dark:bg-blue-700 text-white text-sm font-semibold rounded-xl hover:bg-blue-500 dark:hover:bg-blue-600 transition-all shadow-lg">
          <x-lucide-pill class="w-4 h-4" />
          {{ __('messages.nav.products') }}
        </a>
      </div>
    </div>
  </div>

</div>
@endsection
