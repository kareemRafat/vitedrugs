@php
    $quickLinks = match($part ?? 'drugs') {
        'large-animals' => [
            ['url' => route('large-animals.diagnosis'), 'label' => __('large-animals.nav.diagnosis'), 'active' => request()->routeIs('large-animals.diagnosis*')],
            ['url' => route('large-animals.filter'), 'label' => __('large-animals.nav.filter'), 'active' => request()->routeIs('large-animals.filter*')],
            ['url' => route('large-animals.comparison'), 'label' => __('large-animals.nav.comparison'), 'active' => request()->routeIs('large-animals.comparison*')],
            ['url' => route('large-animals.microorganisms.index'), 'label' => __('large-animals.nav.microorganisms'), 'active' => request()->routeIs('large-animals.microorganisms*')],
            ['url' => route('large-animals.medical-articles.index'), 'label' => __('large-animals.nav.articles'), 'active' => request()->routeIs('large-animals.medical-articles*')],
            ['url' => route('large-animals.specializations.index'), 'label' => __('large-animals.nav.specializations'), 'active' => request()->routeIs('large-animals.specializations*')],
            ['url' => route('large-animals.projects.index'), 'label' => __('large-animals.nav.projects'), 'active' => request()->routeIs('large-animals.projects*')],
        ],
        'poultry' => [
            ['url' => route('poultry.home'), 'label' => __('messages.poultry.landing'), 'active' => request()->routeIs('poultry.home')],
        ],
        default => [
            ['url' => route('products.index'), 'label' => __('messages.nav.products'), 'active' => request()->routeIs('products.*') && ! request()->routeIs('products.compare') && ! request()->routeIs('products.submission.*')],
            ['url' => route('products.submission.create'), 'label' => __('messages.nav.add_product'), 'active' => request()->routeIs('products.submission.*')],
            ['url' => route('products.compare'), 'label' => __('messages.nav.compare_products'), 'active' => request()->routeIs('products.compare')],
            ['url' => route('diseases.index'), 'label' => __('messages.nav.diseases'), 'active' => request()->routeIs('diseases.*')],
            ['url' => route('active-ingredients.index'), 'label' => __('messages.nav.ingredients'), 'active' => request()->routeIs('active-ingredients.*')],
            ['url' => route('companies.index'), 'label' => __('messages.nav.companies'), 'active' => request()->routeIs('companies.*')],
        ],
    };
@endphp
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
                        @foreach ($quickLinks as $link)
                            <li>
                                <a href="{{ $link['url'] }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => $link['active'], 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => ! $link['active']])>{{ $link['label'] }}</a>
                            </li>
                        @endforeach
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
                            <a href="{{ route('blog.index') }}" @class(['transition-colors duration-150', 'text-fg-brand font-medium' => request()->routeIs('blog.*'), 'text-body hover:text-heading dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('blog.*')])>{{ __('messages.nav.blog') }}</a>
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
