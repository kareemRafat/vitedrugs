@extends('app.layouts.master')

@section('title', __('messages.pages.terms.title'))

@section('meta_description')
Terms of Service governing the use of the VetPedia veterinary medical knowledge platform.
@endsection

@section('content')
<div id="terms-page" class="max-w-7xl mx-auto space-y-12 py-8 sm:py-12">

  {{-- Hero --}}
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-100 dark:text-sky-200 border border-white/10 dark:border-sky-700 mb-5">
        <x-lucide-file-text class="w-4 h-4" />
        <span>{{ __('messages.pages.terms.heading') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.terms.heading') }}
      </h1>
      <div class="w-16 h-1 bg-cyan-300 dark:bg-sky-400 rounded-full mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-blue-50 dark:text-sky-100 max-w-xl leading-relaxed">
        {{ __('messages.pages.terms.last_updated') }}: {{ now()->format('F d, Y') }}
      </p>
    </div>
  </div>

  {{-- Sections --}}
  @php
    $sections = [
      ['icon' => 'file-text', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'tc' => 'text-blue-600 dark:text-blue-400', 'title' => __('messages.pages.terms.acceptance_title'), 'text' => __('messages.pages.terms.acceptance_text')],
      ['icon' => 'target', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'tc' => 'text-emerald-600 dark:text-emerald-400', 'title' => __('messages.pages.terms.purpose_title'), 'text' => __('messages.pages.terms.purpose_text')],
      ['icon' => 'alert-triangle', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'tc' => 'text-amber-600 dark:text-amber-400', 'title' => __('messages.pages.terms.no_advice_title'), 'text' => __('messages.pages.terms.no_advice_text')],
      ['icon' => 'check-circle', 'bg' => 'bg-cyan-50 dark:bg-cyan-900/20', 'tc' => 'text-cyan-600 dark:text-cyan-400', 'title' => __('messages.pages.terms.accuracy_title'), 'text' => __('messages.pages.terms.accuracy_text')],
      ['icon' => 'copyright', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'tc' => 'text-purple-600 dark:text-purple-400', 'title' => __('messages.pages.terms.ip_title'), 'text' => __('messages.pages.terms.ip_text')],
      ['icon' => 'shield', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'tc' => 'text-indigo-600 dark:text-indigo-400', 'title' => __('messages.pages.terms.conduct_title'), 'text' => __('messages.pages.terms.conduct_text')],
      ['icon' => 'external-link', 'bg' => 'bg-rose-50 dark:bg-rose-900/20', 'tc' => 'text-rose-600 dark:text-rose-400', 'title' => __('messages.pages.terms.third_party_title'), 'text' => __('messages.pages.terms.third_party_text')],
      ['icon' => 'scale', 'bg' => 'bg-orange-50 dark:bg-orange-900/20', 'tc' => 'text-orange-600 dark:text-orange-400', 'title' => __('messages.pages.terms.liability_title'), 'text' => __('messages.pages.terms.liability_text')],
      ['icon' => 'refresh-cw', 'bg' => 'bg-slate-50 dark:bg-slate-800/40', 'tc' => 'text-slate-600 dark:text-slate-400', 'title' => __('messages.pages.terms.changes_title'), 'text' => __('messages.pages.terms.changes_text')],
    ];
  @endphp

  <div class="space-y-6">
    @foreach ($sections as $section)
      <div class="p-6 sm:p-8 bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-start gap-4">
          <div class="shrink-0 w-10 h-10 {{ $section['bg'] }} rounded-lg flex items-center justify-center">
            @svg('lucide-' . $section['icon'], 'w-5 h-5 ' . $section['tc'])
          </div>
          <div class="min-w-0 flex-1">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $section['title'] }}</h2>
            <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $section['text'] }}</p>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Contact --}}
  <div class="bg-gradient-to-br from-slate-900 to-blue-900 rounded-2xl p-8 sm:p-12 text-white">
    <div class="flex flex-col sm:flex-row items-center gap-6 sm:gap-10">
      <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center shrink-0">
        <x-lucide-message-circle class="w-8 h-8 text-blue-300" />
      </div>
      <div class="flex-1 text-center sm:text-start">
        <h2 class="text-xl sm:text-2xl font-bold">{{ __('messages.pages.terms.contact_title') }}</h2>
        <p class="text-slate-300 text-sm sm:text-base mt-1 max-w-xl">
          {{ __('messages.pages.terms.contact_text') }}
        </p>
      </div>
      <a href="{{ route('contact') }}"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all shadow-lg shrink-0">
        <x-lucide-mail class="w-4 h-4" />
        {{ __('messages.pages.terms.contact_button') }}
      </a>
    </div>
  </div>

</div>
@endsection
