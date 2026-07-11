@extends('app.layouts.master')

@section('title', __('messages.pages.about.title'))

@section('meta_description')
{{ __('messages.pages.about.subtitle') }}
@endsection

@section('content')
<div id="about-page" class="max-w-7xl mx-auto space-y-12 py-8 sm:py-12">

  {{-- Hero --}}
  <div class="relative text-center overflow-hidden rounded-2xl bg-gradient-to-br from-slate-50 to-white border border-slate-100 p-10 sm:p-14 lg:p-18">
    {{-- Decorative elements --}}
    <div class="absolute top-4 right-4 text-blue-100">
      <x-lucide-plus class="w-12 h-12" />
    </div>
    <div class="absolute bottom-4 left-4 text-blue-50">
      <x-lucide-activity class="w-10 h-10" />
    </div>
    <div class="absolute top-1/2 left-6 -translate-y-1/2 text-blue-50 hidden sm:block">
      <x-lucide-heart-pulse class="w-8 h-8" />
    </div>
    <div class="absolute top-1/3 right-12 text-blue-50 hidden sm:block">
      <x-lucide-stethoscope class="w-7 h-7" />
    </div>

    <div class="relative z-10 max-w-3xl mx-auto">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-5">
        <x-lucide-info class="w-4 h-4" />
        <span>{{ __('messages.nav.about') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] tracking-tight">
        {{ __('messages.pages.about.heading') }}
      </h1>
      <div class="w-16 h-1 bg-blue-600 rounded-full mx-auto mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed">
        {{ __('messages.pages.about.subtitle') }}
      </p>
    </div>
  </div>

  {{-- Mission / Vision --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm">
      <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
        <x-lucide-heart class="w-6 h-6 text-blue-600" />
      </div>
      <h2 class="text-xl font-bold text-slate-900 mb-2">{{ __('messages.pages.about.mission_title') }}</h2>
      <p class="text-slate-600 leading-relaxed">
        {{ __('messages.pages.about.mission_text') }}
      </p>
    </div>
    <div class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm">
      <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
        <x-lucide-eye class="w-6 h-6 text-blue-600" />
      </div>
      <h2 class="text-xl font-bold text-slate-900 mb-2">{{ __('messages.pages.about.vision_title') }}</h2>
      <p class="text-slate-600 leading-relaxed">
        {{ __('messages.pages.about.vision_text') }}
      </p>
    </div>
  </div>

  {{-- What VetPedia Provides --}}
  <div>
    <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ __('messages.pages.about.features_title') }}</h2>
    <p class="text-slate-500 mb-8">{{ __('messages.pages.about.features_subtitle') }}</p>
    @php
      $featuresList = [
        ['key' => 'disease_db', 'icon' => 'stethoscope', 'color' => 'emerald'],
        ['key' => 'product_db', 'icon' => 'pill', 'color' => 'blue'],
        ['key' => 'ingredient_lib', 'icon' => 'flask-conical', 'color' => 'purple'],
        ['key' => 'company_dir', 'icon' => 'building-2', 'color' => 'amber'],
        ['key' => 'disease_product', 'icon' => 'git-compare', 'color' => 'rose'],
        ['key' => 'ingredient_product', 'icon' => 'git-branch', 'color' => 'cyan'],
        ['key' => 'clinical_tools', 'icon' => 'activity', 'color' => 'indigo'],
        ['key' => 'search_nav', 'icon' => 'search', 'color' => 'orange'],
      ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      @foreach ($featuresList as $item)
        @php
          $feature = __('messages.pages.about.features.' . $item['key']);
          $bg = "bg-{$item['color']}-50";
          $tc = "text-{$item['color']}-600";
        @endphp
        <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="w-10 h-10 {{ $bg }} rounded-lg flex items-center justify-center mb-4">
            @svg('lucide-' . $item['icon'], 'w-5 h-5 ' . $tc)
          </div>
          <p class="text-sm font-medium text-slate-800">{{ $feature }}</p>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Data Sources --}}
  <div>
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
        <x-lucide-database class="w-5 h-5 text-blue-600" />
      </div>
      <h2 class="text-2xl font-bold text-slate-900">{{ __('messages.pages.about.sources_title') }}</h2>
    </div>
    <p class="text-slate-600 leading-relaxed">
      {{ __('messages.pages.about.sources_text') }}
    </p>
  </div>

  {{-- Disclaimer --}}
  <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 sm:p-8">
    <div class="flex items-start gap-4">
      <x-lucide-alert-triangle class="w-6 h-6 text-amber-600 shrink-0 mt-1" />
      <div>
        <h3 class="text-lg font-bold text-amber-800 mb-1">{{ __('messages.pages.about.disclaimer_title') }}</h3>
        <p class="text-amber-700">{{ __('messages.pages.about.disclaimer_text') }}</p>
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
        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 text-sm font-semibold rounded-xl hover:bg-slate-100 transition-all shadow-lg">
          <x-lucide-mail class="w-4 h-4" />
          {{ __('messages.pages.about.contact_button') }}
        </a>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-500 transition-all shadow-lg">
          <x-lucide-pill class="w-4 h-4" />
          {{ __('messages.nav.products') }}
        </a>
      </div>
    </div>
  </div>

</div>
@endsection
