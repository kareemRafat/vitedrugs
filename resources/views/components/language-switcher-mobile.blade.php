<div class="space-y-1">
    <p class="pb-1 text-xs font-semibold text-body uppercase tracking-wider dark:text-slate-400">{{ __('messages.common.switch_language') }}</p>
    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
            onclick="toggleMobileMenu()"
            class="block px-3 py-2.5 text-sm font-medium rounded-base transition-colors duration-150
                {{ $localeCode === LaravelLocalization::getCurrentLocale()
                    ? 'text-fg-brand bg-brand-soft dark:text-brand dark:bg-brand/20'
                    : 'text-body hover:text-fg-brand hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' }}">
            {{ $properties['native'] }}
        </a>
    @endforeach
</div>
