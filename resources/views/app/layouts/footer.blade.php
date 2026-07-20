<footer class="bg-neutral-primary-soft border-t border-default-medium dark:bg-slate-800 dark:border-slate-700">
    <div class="w-full px-4 py-8 mx-auto lg:py-10">
        <div class="flex flex-col gap-12 lg:flex-row lg:gap-24">

            {{-- Brand --}}
            <div class="lg:w-1/3 ms-5">
                <a href="{{ route('home') }}" class="flex items-center mb-3">
                    <span class="self-center text-xl font-semibold whitespace-nowrap text-heading dark:text-white">VetPedia</span>
                </a>
                <p class="text-sm text-body dark:text-slate-400 max-w-xs">
                    {{ __('messages.nav.footer_text') }}
                </p>
            </div>

            {{-- Quick Links, Resources, Language --}}
            <div class="px-4 sm:px-0 grid grid-cols-2 gap-1 sm:grid-cols-3 lg:w-2/3">

                <div class="w-50">
                    <button type="button" onclick="this.parentElement.classList.toggle('is-open')" class="lg:pointer-events-none lg:cursor-default flex items-center gap-1 w-full mb-3">
                        <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.nav.quick_links') }}</h3>
                        <x-lucide-chevron-down class="w-4 h-4 text-body lg:hidden transition-transform [.is-open_&]:rotate-180" />
                    </button>
                    <ul class="max-lg:hidden lg:flex lg:flex-col space-y-2 text-sm [.is-open_&]:max-lg:block">
                        <li>
                            <a href="{{ route('products.index') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('products.*'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('products.*')])>{{ __('messages.nav.products') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('diseases.index') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('diseases.*'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('diseases.*')])>{{ __('messages.nav.diseases') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('active-ingredients.index') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('active-ingredients.*'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('active-ingredients.*')])>{{ __('messages.nav.ingredients') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('companies.index') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('companies.*'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('companies.*')])>{{ __('messages.nav.companies') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('blog.index') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('blog.*'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('blog.*')])>{{ __('messages.nav.blog') }}</a>
                        </li>
                    </ul>
                </div>

                <div class="w-50">
                    <button type="button" onclick="this.parentElement.classList.toggle('is-open')" class="lg:pointer-events-none lg:cursor-default flex items-center gap-1 w-full mb-3">
                        <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.nav.resources') }}</h3>
                        <x-lucide-chevron-down class="w-4 h-4 text-body lg:hidden transition-transform [.is-open_&]:rotate-180" />
                    </button>
                    <ul class="max-lg:hidden lg:flex lg:flex-col space-y-2 text-sm [.is-open_&]:max-lg:block">
                        <li>
                            <a href="{{ route('about') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('about'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('about')])>{{ __('messages.nav.about') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('contact'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('contact')])>{{ __('messages.nav.contact') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('privacy-policy') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('privacy-policy'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('privacy-policy')])>{{ __('messages.nav.privacy_policy') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('terms-of-service') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('terms-of-service'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('terms-of-service')])>{{ __('messages.nav.terms_of_service') }}</a>
                        </li>
                    </ul>
                </div>

                <div class="w-50 max-sm:hidden">
                    <button type="button" onclick="this.parentElement.classList.toggle('is-open')" class="lg:pointer-events-none lg:cursor-default flex items-center gap-1 w-full mb-3">
                        <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.nav.language') }}</h3>
                        <x-lucide-chevron-down class="w-4 h-4 text-body lg:hidden transition-transform [.is-open_&]:rotate-180" />
                    </button>
                    <ul class="max-lg:hidden lg:flex lg:flex-col space-y-2 text-sm [.is-open_&]:max-lg:block">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li>
                                <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                   @class(['transition-colors duration-150', 'text-fg-brand font-medium' => $localeCode === LaravelLocalization::getCurrentLocale(), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => $localeCode !== LaravelLocalization::getCurrentLocale()])>
                                    {{ $properties['native'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>

        <hr class="my-6 border-default-medium dark:border-slate-700 sm:mx-auto lg:my-8">

        <div class="text-center">
            <span class="text-sm text-body dark:text-slate-400">
                &copy; {{ date('Y') }}
                <a href="{{ route('home') }}" class="hover:underline text-heading dark:text-white">VetPedia</a>.
                {{ __('messages.nav.all_rights_reserved') }}
            </span>
        </div>
    </div>
</footer>
