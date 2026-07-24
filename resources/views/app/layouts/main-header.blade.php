<nav class="fixed top-0 z-50 w-full bg-neutral-primary-soft border-b border-default-medium dark:bg-slate-800 dark:border-slate-700">
    <div class="px-3 py-3 lg:px-5">
        <div class="flex items-center justify-between sm:grid sm:grid-cols-3 sm:items-center">
            <div class="flex justify-start items-center gap-2">
                <button id="hamburgerBtn" onclick="toggleMobileMenu()" class="sm:hidden flex flex-col items-center justify-center w-8 h-8 gap-1.5 p-1.5 text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white rounded-base transition-colors duration-200" aria-label="Toggle menu">
                    <span class="hamburger-line block w-full h-0.5 bg-current rounded-xs transition-all duration-300 ease-in-out origin-center"></span>
                    <span class="hamburger-line block w-full h-0.5 bg-current rounded-xs transition-all duration-300 ease-in-out origin-center"></span>
                    <span class="hamburger-line block w-full h-0.5 bg-current rounded-xs transition-all duration-300 ease-in-out origin-center"></span>
                </button>
                <a href="{{ route('home') }}" class="flex">
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">VetPedia</span>
                </a>
            </div>

            <div class="hidden sm:flex items-center justify-center gap-1">
                <a href="{{ route('products.index') }}" wire:navigate @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150 whitespace-nowrap', 'text-fg-brand font-semibold' => request()->routeIs('products.*'), 'text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('products.*')])>{{ __('messages.nav.products') }}</a>
                <a href="{{ route('products.submission.create') }}" wire:navigate @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150 whitespace-nowrap', 'text-fg-brand font-semibold' => request()->routeIs('products.submission.*'), 'text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('products.submission.*')])>{{ __('messages.nav.add_product') }}</a>
                <a href="{{ route('products.compare') }}" wire:navigate @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150 whitespace-nowrap', 'text-fg-brand font-semibold' => request()->routeIs('products.compare'), 'text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('products.compare')])>{{ __('messages.nav.compare_products') }}</a>
                <a href="{{ route('diseases.index') }}" wire:navigate @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150 whitespace-nowrap', 'text-fg-brand font-semibold' => request()->routeIs('diseases.*'), 'text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('diseases.*')])>{{ __('messages.nav.diseases') }}</a>
                <a href="{{ route('active-ingredients.index') }}" wire:navigate @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150 whitespace-nowrap', 'text-fg-brand font-semibold' => request()->routeIs('active-ingredients.*'), 'text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('active-ingredients.*')])>{{ __('messages.nav.ingredients') }}</a>
                <a href="{{ route('companies.index') }}" wire:navigate @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150 whitespace-nowrap', 'text-fg-brand font-semibold' => request()->routeIs('companies.*'), 'text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white' => !request()->routeIs('companies.*')])>{{ __('messages.nav.companies') }}</a>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white px-2 py-1 text-sm font-medium inline-flex items-center gap-1 whitespace-nowrap" type="button">
                        {{ __('messages.nav.platform') }}
                        <x-lucide-chevron-down class="w-4 h-4 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>

                    <div x-show="open" x-cloak
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute end-0 z-50 my-2 w-48 bg-neutral-primary-soft rounded-base shadow-lg dark:bg-slate-700">
                        <ul class="py-1 text-sm text-heading dark:text-slate-300">
                            <li>
                                <a href="{{ route('blog.index') }}" @class(['group flex items-center gap-3 px-4 py-2 transition-colors duration-150', 'text-fg-brand font-semibold dark:text-white' => request()->routeIs('blog.*'), 'text-heading dark:text-slate-300 hover:text-fg-brand' => !request()->routeIs('blog.*')])>
                                    <span class="flex-1">{{ __('messages.nav.blog') }}</span>
                                    <x-lucide-check class="w-4 h-4 shrink-0 transition-opacity duration-150 text-fg-brand dark:text-white opacity-0 group-hover:opacity-100" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('about') }}" @class(['group flex items-center gap-3 px-4 py-2 transition-colors duration-150', 'text-fg-brand font-semibold dark:text-white' => request()->routeIs('about'), 'text-heading dark:text-slate-300 hover:text-fg-brand' => !request()->routeIs('about')])>
                                    <span class="flex-1">{{ __('messages.nav.about') }}</span>
                                    <x-lucide-check class="w-4 h-4 shrink-0 transition-opacity duration-150 text-fg-brand dark:text-white opacity-0 group-hover:opacity-100" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" @class(['group flex items-center gap-3 px-4 py-2 transition-colors duration-150', 'text-fg-brand font-semibold dark:text-white' => request()->routeIs('contact'), 'text-heading dark:text-slate-300 hover:text-fg-brand' => !request()->routeIs('contact')])>
                                    <span class="flex-1">{{ __('messages.nav.contact') }}</span>
                                    <x-lucide-check class="w-4 h-4 shrink-0 transition-opacity duration-150 text-fg-brand dark:text-white opacity-0 group-hover:opacity-100" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('privacy-policy') }}" @class(['group flex items-center gap-3 px-4 py-2 transition-colors duration-150', 'text-fg-brand font-semibold dark:text-white' => request()->routeIs('privacy-policy'), 'text-heading dark:text-slate-300 hover:text-fg-brand' => !request()->routeIs('privacy-policy')])>
                                    <span class="flex-1">{{ __('messages.nav.privacy_policy') }}</span>
                                    <x-lucide-check class="w-4 h-4 shrink-0 transition-opacity duration-150 text-fg-brand dark:text-white opacity-0 group-hover:opacity-100" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('terms-of-service') }}" @class(['group flex items-center gap-3 px-4 py-2 transition-colors duration-150', 'text-fg-brand font-semibold dark:text-white' => request()->routeIs('terms-of-service'), 'text-heading dark:text-slate-300 hover:text-fg-brand' => !request()->routeIs('terms-of-service')])>
                                    <span class="flex-1">{{ __('messages.nav.terms_of_service') }}</span>
                                    <x-lucide-check class="w-4 h-4 shrink-0 transition-opacity duration-150 text-fg-brand dark:text-white opacity-0 group-hover:opacity-100" />
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-1 sm:gap-2">
                <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white bg-neutral-tertiary-soft dark:bg-slate-700 px-4 py-1.5 rounded-base text-sm" title="{{ __('messages.nav.search') }}">
                    <x-lucide-search class="w-4 h-4" />
                    <span class="hidden sm:inline">{{ __('messages.nav.search') }}</span>
                </a>

                <div class="hidden sm:flex items-center gap-1">
                    <button onclick="toggleTheme()"
                        class="text-body hover:text-fg-brand dark:text-slate-400 dark:hover:text-white p-2 rounded-base"
                        title="{{ __('messages.common.toggle_theme') }}">
                        <x-lucide-sun class="w-5 h-5 dark:hidden" />
                        <x-lucide-moon class="w-5 h-5 hidden dark:block" />
                    </button>
                    <x-language-switcher />
                </div>

                @auth
                    <div x-data="{ open: false }" class="hidden sm:flex items-center relative">
                        <button @click="open = !open" @click.outside="open = false" class="flex text-sm rounded-full focus:ring-4 focus:ring-neutral-secondary-soft dark:focus:ring-slate-600" type="button">
                            <span class="sr-only">{{ __('messages.nav.toggle_user_menu') }}</span>
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-medium">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                        <div x-show="open" x-cloak
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute end-0 top-full z-50 my-2 w-56 bg-neutral-primary-soft rounded-base shadow-lg border border-default-medium dark:bg-slate-700 dark:border-slate-600">
                            <div class="px-4 py-4 flex items-center gap-3 border-b border-default-medium dark:border-slate-600">
                                <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-sm font-semibold shrink-0">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-heading dark:text-white truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-body dark:text-slate-400 truncate">{{ Auth::user()->email }}</div>
                                </div>
                            </div>
                            <ul class="py-1 text-sm text-heading dark:text-slate-300">
                                <li>
                                    <a href="{{ route('profile.show') }}" wire:navigate @click="open = false" class="flex items-center gap-3 w-full text-start px-4 py-2.5 rounded-base transition-colors duration-150 hover:bg-neutral-secondary-soft dark:hover:bg-slate-600 text-body hover:text-heading dark:hover:text-white">
                                        <x-lucide-user class="w-4 h-4" />
                                        {{ __('messages.profile.title') }}
                                    </a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 w-full text-start px-4 py-2.5 rounded-base transition-colors duration-150 hover:bg-danger-soft dark:hover:bg-slate-600 text-body hover:text-fg-danger-strong dark:hover:text-red-400">
                                            <x-lucide-log-out class="w-4 h-4" />
                                            {{ __('messages.nav.sign_out') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex text-fg-brand hover:text-brand-strong dark:text-brand dark:hover:text-brand px-3 py-1.5 text-sm font-medium border border-brand rounded-base hover:bg-brand-soft dark:hover:bg-brand/20">{{ __('messages.nav.sign_in') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Mobile sidebar backdrop --}}
<div id="mobileBackdrop" onclick="toggleMobileMenu()" class="fixed inset-0 z-40 bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 sm:hidden"></div>

{{-- Mobile sidebar --}}
<div id="mobileSidebar" class="fixed top-0 start-0 z-50 w-full h-screen bg-neutral-primary-soft dark:bg-slate-800 -translate-x-full rtl:translate-x-full transition-transform duration-300 ease-in-out sm:hidden overflow-y-auto hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-default-medium dark:border-slate-700">
        <span class="text-lg font-semibold text-heading dark:text-white">VetPedia</span>
        <button onclick="toggleMobileMenu()" class="p-1.5 text-body hover:text-heading dark:text-slate-400 dark:hover:text-white rounded-base transition-colors duration-200">
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>

    <div class="px-5 py-5 space-y-6">
        <div class="space-y-1">
            <a href="{{ route('products.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('products.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('products.*')])>
                <x-lucide-pill class="w-5 h-5" />
                {{ __('messages.nav.products') }}
            </a>
            <a href="{{ route('products.submission.create') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('products.submission.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('products.submission.*')])>
                <x-lucide-plus class="w-5 h-5" />
                {{ __('messages.nav.add_product') }}
            </a>
            <a href="{{ route('products.compare') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('products.compare'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('products.compare')])>
                <x-lucide-arrow-left-right class="w-5 h-5" />
                {{ __('messages.nav.compare_products') }}
            </a>
            <a href="{{ route('diseases.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('diseases.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('diseases.*')])>
                <x-lucide-stethoscope class="w-5 h-5" />
                {{ __('messages.nav.diseases') }}
            </a>
            <a href="{{ route('active-ingredients.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('active-ingredients.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('active-ingredients.*')])>
                <x-lucide-flask-conical class="w-5 h-5" />
                {{ __('messages.nav.ingredients') }}
            </a>
            <a href="{{ route('companies.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('companies.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('companies.*')])>
                <x-lucide-building-2 class="w-5 h-5" />
                {{ __('messages.nav.companies') }}
            </a>
            <a href="{{ route('blog.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('blog.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('blog.*')])>
                <x-lucide-newspaper class="w-5 h-5" />
                {{ __('messages.nav.blog') }}
            </a>
        </div>

        <div class="border-t border-default-medium dark:border-slate-700 pt-5">
            <p class="px-4 pb-2 text-xs font-semibold text-body uppercase tracking-wider dark:text-slate-400">{{ __('messages.nav.platform') }}</p>
            <div class="space-y-1">
                <a href="{{ route('about') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('about'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('about')])>
                    <x-lucide-info class="w-5 h-5" />
                    {{ __('messages.nav.about') }}
                </a>
                <a href="{{ route('contact') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('contact'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('contact')])>
                    <x-lucide-mail class="w-5 h-5" />
                    {{ __('messages.nav.contact') }}
                </a>
                <a href="{{ route('privacy-policy') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('privacy-policy'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('privacy-policy')])>
                    <x-lucide-shield class="w-5 h-5" />
                    {{ __('messages.nav.privacy_policy') }}
                </a>
                <a href="{{ route('terms-of-service') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('terms-of-service'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700' => !request()->routeIs('terms-of-service')])>
                    <x-lucide-file-text class="w-5 h-5" />
                    {{ __('messages.nav.terms_of_service') }}
                </a>
            </div>
        </div>

        <div class="border-t border-default-medium dark:border-slate-700 pt-5">
            <p class="pb-1 text-xs font-semibold text-body uppercase tracking-wider dark:text-slate-400">{{ __('messages.common.toggle_theme') }}</p>
            <button onclick="toggleTheme()" class="flex items-center gap-3 w-full px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150 text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700">
                <x-lucide-sun class="w-5 h-5 dark:hidden" />
                <x-lucide-moon class="w-5 h-5 hidden dark:block" />
                <span class="dark:hidden">{{ __('messages.common.light_mode') }}</span>
                <span class="hidden dark:inline">{{ __('messages.common.dark_mode') }}</span>
            </button>
        </div>

        <div class="border-t border-default-medium dark:border-slate-700 pt-5">
            <x-language-switcher-mobile />
        </div>

        <div class="border-t border-default-medium dark:border-slate-700 pt-5">
            @auth
                <div class="space-y-3">
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-sm font-medium flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-heading dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-body dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.show') }}" onclick="toggleMobileMenu()" class="flex items-center gap-3 w-full text-start px-4 py-3 text-sm font-medium text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 rounded-base transition-colors duration-150">
                        <x-lucide-user class="w-5 h-5 text-body dark:text-slate-400" />
                        {{ __('messages.profile.title') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-start px-4 py-3 text-sm font-medium text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-700 rounded-base transition-colors duration-150">
                            <x-lucide-log-out class="w-5 h-5 text-body dark:text-slate-400" />
                            {{ __('messages.nav.sign_out') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="px-4">
                    <a href="{{ route('login') }}" onclick="toggleMobileMenu()" class="flex items-center justify-center gap-2 w-full px-4 py-3 text-sm font-medium text-white bg-brand hover:bg-brand-strong dark:bg-brand dark:hover:bg-brand-strong rounded-base transition-colors duration-150">
                        <x-lucide-log-in class="w-5 h-5" />
                        {{ __('messages.nav.sign_in') }}
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<script>
    function toggleMobileMenu() {
        const sidebar = document.getElementById('mobileSidebar');
        const backdrop = document.getElementById('mobileBackdrop');
        const btn = document.getElementById('hamburgerBtn');
        const isOpen = sidebar.classList.contains('open');

        if (isOpen) {
            sidebar.classList.remove('open');
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full', 'rtl:translate-x-full');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
            btn?.classList.remove('active');

            const onEnd = () => {
                if (!sidebar.classList.contains('open')) {
                    sidebar.classList.add('hidden');
                }
                sidebar.removeEventListener('transitionend', onEnd);
            };
            sidebar.addEventListener('transitionend', onEnd);
        } else {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('-translate-x-full', 'rtl:translate-x-full');
            void sidebar.offsetHeight;
            sidebar.classList.add('open');
            sidebar.classList.remove('-translate-x-full', 'rtl:translate-x-full');
            sidebar.classList.add('translate-x-0');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
            btn?.classList.add('active');
        }
    }

    function setTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
    }

    function toggleTheme() {
        setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
    }
</script>
