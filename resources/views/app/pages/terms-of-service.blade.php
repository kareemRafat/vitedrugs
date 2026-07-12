@extends('app.layouts.master')

@section('title', __('messages.pages.terms.title'))

@section('meta_description')
Terms of Service governing the use of the VetPedia veterinary medical knowledge platform.
@endsection

@section('content')
<div id="terms-page" class="max-w-7xl mx-auto space-y-12 py-8 sm:py-12">

  {{-- Hero --}}
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-100 border border-white/10 mb-5">
        <x-lucide-file-text class="w-4 h-4" />
        <span>{{ __('messages.pages.terms.heading') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.terms.heading') }}
      </h1>
      <div class="w-16 h-1 bg-cyan-300 rounded-full mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-blue-50 max-w-xl leading-relaxed">
        {{ __('messages.pages.terms.last_updated') }}: {{ now()->format('F d, Y') }}
      </p>
    </div>
  </div>

  {{-- Sections --}}
  @php
    $sections = [
      ['icon' => 'file-text', 'color' => 'blue', 'title' => __('messages.pages.terms.acceptance_title'), 'text' => __('messages.pages.terms.acceptance_text')],
      ['icon' => 'target', 'color' => 'emerald', 'title' => __('messages.pages.terms.purpose_title'), 'text' => __('messages.pages.terms.purpose_text')],
      ['icon' => 'alert-triangle', 'color' => 'amber', 'title' => __('messages.pages.terms.no_advice_title'), 'text' => __('messages.pages.terms.no_advice_text')],
      ['icon' => 'check-circle', 'color' => 'cyan', 'title' => __('messages.pages.terms.accuracy_title'), 'text' => __('messages.pages.terms.accuracy_text')],
      ['icon' => 'copyright', 'color' => 'purple', 'title' => __('messages.pages.terms.ip_title'), 'text' => __('messages.pages.terms.ip_text')],
      ['icon' => 'shield', 'color' => 'indigo', 'title' => __('messages.pages.terms.conduct_title'), 'text' => __('messages.pages.terms.conduct_text')],
      ['icon' => 'external-link', 'color' => 'rose', 'title' => __('messages.pages.terms.third_party_title'), 'text' => __('messages.pages.terms.third_party_text')],
      ['icon' => 'scale', 'color' => 'orange', 'title' => __('messages.pages.terms.liability_title'), 'text' => __('messages.pages.terms.liability_text')],
      ['icon' => 'refresh-cw', 'color' => 'slate', 'title' => __('messages.pages.terms.changes_title'), 'text' => __('messages.pages.terms.changes_text')],
    ];
  @endphp

  <div class="space-y-6">
    @foreach ($sections as $section)
      <div class="p-6 sm:p-8 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-start gap-4">
          <div class="shrink-0 w-10 h-10 bg-{{ $section['color'] }}-50 rounded-lg flex items-center justify-center">
            @svg('lucide-' . $section['icon'], 'w-5 h-5 text-' . $section['color'] . '-600')
          </div>
          <div class="min-w-0 flex-1">
            <h2 class="text-lg font-bold text-slate-900 mb-2">{{ $section['title'] }}</h2>
            <p class="text-slate-600 leading-relaxed">{{ $section['text'] }}</p>
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
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 text-sm font-semibold rounded-xl hover:bg-slate-100 transition-all shadow-lg shrink-0">
        <x-lucide-mail class="w-4 h-4" />
        {{ __('messages.pages.terms.contact_button') }}
      </a>
    </div>
  </div>

</div>
@endsection
