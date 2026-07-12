@extends('app.layouts.master')

@section('title', __('messages.pages.privacy.title'))

@section('meta_description')
Privacy Policy for VetPedia veterinary medical knowledge platform.
@endsection

@section('content')
<div id="privacy-page" class="max-w-7xl mx-auto space-y-12 py-8 sm:py-12">

  {{-- Hero --}}
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-100 border border-white/10 mb-5">
        <x-lucide-shield class="w-4 h-4" />
        <span>{{ __('messages.pages.privacy.heading') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.privacy.heading') }}
      </h1>
      <div class="w-16 h-1 bg-cyan-300 rounded-full mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-blue-50 max-w-xl leading-relaxed">
        {{ __('messages.pages.privacy.last_updated') }}: {{ now()->format('F d, Y') }}
      </p>
    </div>
  </div>

  {{-- Sections --}}
  @php
    $sections = [
      ['icon' => 'info', 'color' => 'blue', 'title' => __('messages.pages.privacy.intro_title'), 'text' => __('messages.pages.privacy.intro_text')],
      ['icon' => 'database', 'color' => 'emerald', 'title' => __('messages.pages.privacy.collect_title'), 'text' => __('messages.pages.privacy.collect_text')],
      ['icon' => 'search', 'color' => 'purple', 'title' => __('messages.pages.privacy.use_title'), 'list' => __('messages.pages.privacy.use_items')],
      ['icon' => 'cookie', 'color' => 'amber', 'title' => __('messages.pages.privacy.cookies_title'), 'text' => __('messages.pages.privacy.cookies_text')],
      ['icon' => 'share-2', 'color' => 'rose', 'title' => __('messages.pages.privacy.third_party_title'), 'text' => __('messages.pages.privacy.third_party_text')],
      ['icon' => 'shield', 'color' => 'indigo', 'title' => __('messages.pages.privacy.security_title'), 'text' => __('messages.pages.privacy.security_text')],
      ['icon' => 'check-circle', 'color' => 'cyan', 'title' => __('messages.pages.privacy.accuracy_title'), 'text' => __('messages.pages.privacy.accuracy_text')],
      ['icon' => 'refresh-cw', 'color' => 'orange', 'title' => __('messages.pages.privacy.changes_title'), 'text' => __('messages.pages.privacy.changes_text')],
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
            @if (isset($section['list']))
              <ul class="space-y-2">
                @foreach ($section['list'] as $item)
                  <li class="flex items-start gap-2 text-slate-600">
                    <x-lucide-check class="w-4 h-4 text-{{ $section['color'] }}-500 mt-1 shrink-0" />
                    {{ $item }}
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-slate-600 leading-relaxed">{{ $section['text'] }}</p>
            @endif
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
        <h2 class="text-xl sm:text-2xl font-bold">{{ __('messages.pages.privacy.contact_title') }}</h2>
        <p class="text-slate-300 text-sm sm:text-base mt-1 max-w-xl">
          {{ __('messages.pages.privacy.contact_text') }}
        </p>
      </div>
      <a href="{{ route('contact') }}"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-slate-900 text-sm font-semibold rounded-xl hover:bg-slate-100 transition-all shadow-lg shrink-0">
        <x-lucide-mail class="w-4 h-4" />
        {{ __('messages.pages.privacy.contact_button') }}
      </a>
    </div>
  </div>

</div>
@endsection
