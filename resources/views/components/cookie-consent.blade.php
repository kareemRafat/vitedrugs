@php $isRtl = app()->getLocale() === 'ar'; @endphp

<div id="cookieConsent"
     data-accepted="false"
     class="fixed inset-x-4 bottom-4 z-50 max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-lg border border-default transition-all duration-500 ease-out translate-y-full opacity-0">
  <div class="flex items-start gap-3">
    <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-orange-600 bg-orange-50 rounded">
      <x-lucide-cookie class="w-5 h-5" />
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-base leading-relaxed">{{ __('messages.cookie_banner.message') }}</p>
    </div>
  </div>
  <div class="mt-3 flex justify-end gap-2">
    <button type="button" id="cookieAccept"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-all">
            <x-lucide-check class="w-4 h-4" />
      {{ __('messages.cookie_banner.accept') }}
    </button>
  </div>
</div>
