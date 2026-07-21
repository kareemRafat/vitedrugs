<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open" @click.outside="open = false"
        class="flex items-center gap-1 text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white p-2 rounded-base text-sm font-medium"
        title="{{ __('messages.common.switch_language') }}">
        <x-lucide-languages class="w-5 h-5" />
        <span class="hidden sm:inline">{{ LaravelLocalization::getCurrentLocaleNative() }}</span>
        <x-lucide-chevron-down class="w-3.5 h-3.5 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
    </button>
    <div x-show="open" x-cloak
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute end-0 z-50 mt-2 w-40 bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-slate-700 dark:border-slate-600">
        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
            <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                class="block px-4 py-2 text-sm text-heading dark:text-slate-300 rounded-base transition-colors duration-150 hover:bg-neutral-secondary-soft dark:hover:bg-slate-600 dark:hover:text-white {{ $localeCode === LaravelLocalization::getCurrentLocale() ? 'bg-neutral-secondary-soft dark:bg-slate-600' : '' }}">
                {{ $properties['native'] }}
            </a>
        @endforeach
    </div>
</div>
