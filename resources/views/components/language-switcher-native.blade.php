@php
    $localeFlags = [
        'en' => '🇺🇸',
        'ar' => '🇸🇦',
    ];
@endphp
<div class="relative">
    <button type="button" id="language-trigger"
        class="flex items-center gap-1 text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white p-2 rounded-base text-sm font-medium"
        title="{{ __('messages.common.switch_language') }}">
        <x-lucide-languages class="w-5 h-5" />
        <span class="hidden sm:inline">{{ LaravelLocalization::getCurrentLocaleNative() }}</span>
        <x-lucide-chevron-down id="language-chevron" class="w-3.5 h-3.5 transition-transform duration-200" />
    </button>
    <div id="language-dropdown" class="hidden z-50 absolute end-0 mt-2 w-44 bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-slate-700 dark:border-slate-600">
        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                @class([
                    'group flex items-center gap-3 px-4 py-2.5 text-sm rounded-base transition-colors duration-150',
                    'text-fg-brand font-semibold dark:text-white' => $localeCode === LaravelLocalization::getCurrentLocale(),
                    'text-heading dark:text-slate-300 hover:text-fg-brand' => $localeCode !== LaravelLocalization::getCurrentLocale(),
                ])>
                <span class="text-base leading-none shrink-0">{{ $localeFlags[$localeCode] ?? '🌐' }}</span>
                <span class="flex-1">{{ $properties['native'] }}</span>
                <x-lucide-check class="w-4 h-4 shrink-0 transition-opacity duration-150 text-fg-brand dark:text-white opacity-0 group-hover:opacity-100" />
            </a>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var trigger = document.getElementById('language-trigger');
    var target = document.getElementById('language-dropdown');
    var chevron = document.getElementById('language-chevron');
    if (trigger && target && typeof window.Dropdown !== 'undefined') {
        new window.Dropdown(target, trigger, {
            placement: 'bottom-end',
            onShow: function () { chevron.classList.add('rotate-180'); },
            onHide: function () { chevron.classList.remove('rotate-180'); },
        });
    }
});
</script>
