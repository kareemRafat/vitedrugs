<div class="relative">
    <button id="languageDropdown" data-dropdown-toggle="languageDropdownMenu"
        class="flex items-center gap-1 text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white p-2 rounded-base text-sm font-medium"
        title="{{ __('messages.common.switch_language') }}">
        <x-lucide-languages class="w-5 h-5" />
        <span class="hidden sm:inline">{{ LaravelLocalization::getCurrentLocaleNative() }}</span>
        <x-lucide-chevron-down class="w-3.5 h-3.5" />
    </button>
    <div id="languageDropdownMenu"
        class="z-50 hidden my-2 w-40 bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-gray-700 dark:border-gray-600">
        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                class="block px-4 py-2 text-sm text-heading dark:text-gray-300 rounded-base transition-colors duration-150 hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white {{ $localeCode === LaravelLocalization::getCurrentLocale() ? 'bg-neutral-secondary-soft dark:bg-gray-600' : '' }}">
                {{ $properties['native'] }}
            </a>
        @endforeach
    </div>
</div>
