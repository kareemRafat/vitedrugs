@php
    $localeFlags = [
        'en' => '🇺🇸',
        'ar' => '🇸🇦',
    ];
@endphp
<div class="space-y-1">
    <p class="pb-1 text-xs font-semibold text-body uppercase tracking-wider dark:text-slate-400">{{ __('messages.common.switch_language') }}</p>
    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
            onclick="toggleMobileMenu()"
            @class([
                'group flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150',
                'text-fg-brand font-semibold dark:text-white' => $localeCode === LaravelLocalization::getCurrentLocale(),
                'text-heading dark:text-slate-300 hover:text-fg-brand' => $localeCode !== LaravelLocalization::getCurrentLocale(),
            ])>
            <span class="text-base leading-none shrink-0">{{ $localeFlags[$localeCode] ?? '🌐' }}</span>
            <span class="flex-1">{{ $properties['native'] }}</span>
            <x-lucide-check class="w-4 h-4 shrink-0 transition-opacity duration-150 text-fg-brand dark:text-white opacity-0 group-hover:opacity-100" />
        </a>
    @endforeach
</div>
