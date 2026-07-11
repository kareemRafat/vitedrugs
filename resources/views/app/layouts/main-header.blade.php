<nav class="fixed top-0 z-50 w-full bg-neutral-primary-soft border-b border-default-medium dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5">
        <div class="flex items-center justify-between sm:grid sm:grid-cols-3 sm:items-center">
            <div class="flex justify-start items-center gap-2">
                <button id="hamburgerBtn" onclick="toggleMobileMenu()" class="sm:hidden flex flex-col items-center justify-center w-8 h-8 gap-1.5 p-1.5 text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white rounded-base transition-colors duration-200" aria-label="Toggle menu">
                    <span class="hamburger-line block w-full h-0.5 bg-current rounded-xs transition-all duration-300 ease-in-out origin-center"></span>
                    <span class="hamburger-line block w-full h-0.5 bg-current rounded-xs transition-all duration-300 ease-in-out origin-center"></span>
                    <span class="hamburger-line block w-full h-0.5 bg-current rounded-xs transition-all duration-300 ease-in-out origin-center"></span>
                </button>
                <a href="{{ route('home') }}" class="flex">
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">VetPedia</span>
                </a>
            </div>

            <div class="hidden sm:flex items-center justify-center gap-1">
                <a href="{{ route('products.index') }}" @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('products.*'), 'text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white' => !request()->routeIs('products.*')])>{{ __('messages.nav.products') }}</a>
                <a href="{{ route('diseases.index') }}" @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('diseases.*'), 'text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white' => !request()->routeIs('diseases.*')])>{{ __('messages.nav.diseases') }}</a>
                <a href="{{ route('active-ingredients.index') }}" @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('active-ingredients.*'), 'text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white' => !request()->routeIs('active-ingredients.*')])>{{ __('messages.nav.ingredients') }}</a>
                <a href="{{ route('companies.index') }}" @class(['px-2 py-1 text-sm font-medium rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('companies.*'), 'text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white' => !request()->routeIs('companies.*')])>{{ __('messages.nav.companies') }}</a>

                <button id="moreDropdown" data-dropdown-toggle="moreDropdownMenu" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium inline-flex items-center gap-1" type="button">
                    {{ __('messages.nav.platform') }}
                    <x-lucide-chevron-down class="w-4 h-4" />
                </button>

                <div id="moreDropdownMenu" class="z-50 hidden my-4 w-48 bg-neutral-primary-soft rounded-base shadow-lg dark:bg-gray-700">
                    <ul class="py-1 text-sm text-heading dark:text-gray-300">
                        <li>
                            <a href="{{ route('about') }}" @class(['block px-4 py-2 rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('about'), 'hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white' => !request()->routeIs('about')])>{{ __('messages.nav.about') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" @class(['block px-4 py-2 rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('contact'), 'hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white' => !request()->routeIs('contact')])>{{ __('messages.nav.contact') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('privacy-policy') }}" @class(['block px-4 py-2 rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('privacy-policy'), 'hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white' => !request()->routeIs('privacy-policy')])>{{ __('messages.nav.privacy_policy') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('terms-of-service') }}" @class(['block px-4 py-2 rounded-base transition-colors duration-150', 'text-fg-brand font-semibold' => request()->routeIs('terms-of-service'), 'hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white' => !request()->routeIs('terms-of-service')])>{{ __('messages.nav.terms_of_service') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-end gap-1 sm:gap-2">
                <a href="{{ route('search') }}" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white p-2 rounded-base" title="{{ __('messages.nav.search') }}">
                    <x-lucide-search class="w-5 h-5" />
                </a>

                <div class="hidden sm:block">
                    <x-language-switcher />
                </div>

                @auth
                    <div class="hidden sm:flex items-center">
                        <button id="userDropdown" data-dropdown-toggle="userDropdownMenu" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-neutral-secondary-soft dark:focus:ring-gray-600" type="button">
                            <span class="sr-only">{{ __('messages.nav.toggle_user_menu') }}</span>
                            <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-sm font-medium">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                        <div id="userDropdownMenu" class="z-50 hidden my-4 w-48 bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-700">
                            <div class="px-4 py-3 text-sm text-heading dark:text-white">
                                <div class="font-medium truncate">{{ Auth::user()->name }}</div>
                                <div class="truncate text-xs text-body dark:text-gray-400">{{ Auth::user()->email }}</div>
                            </div>
                            <ul class="py-1 text-sm text-heading dark:text-gray-300">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-start px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white">{{ __('messages.nav.sign_out') }}</button>
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
<div id="mobileSidebar" class="fixed top-0 start-0 z-50 w-full h-screen bg-neutral-primary-soft dark:bg-gray-800 translate-x-[-100%] rtl:translate-x-[100%] transition-transform duration-300 ease-in-out sm:hidden overflow-y-auto">
    <div class="flex items-center justify-between px-5 py-4 border-b border-default-medium dark:border-gray-700">
        <span class="text-lg font-semibold text-heading dark:text-white">VetPedia</span>
        <button onclick="toggleMobileMenu()" class="p-1.5 text-body hover:text-heading dark:text-gray-400 dark:hover:text-white rounded-base transition-colors duration-200">
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>

    <div class="px-5 py-5 space-y-6">
        <div class="space-y-1">
            <a href="{{ route('products.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('products.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('products.*')])>
                <x-lucide-pill class="w-5 h-5" />
                {{ __('messages.nav.products') }}
            </a>
            <a href="{{ route('diseases.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('diseases.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('diseases.*')])>
                <x-lucide-stethoscope class="w-5 h-5" />
                {{ __('messages.nav.diseases') }}
            </a>
            <a href="{{ route('active-ingredients.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('active-ingredients.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('active-ingredients.*')])>
                <x-lucide-flask-conical class="w-5 h-5" />
                {{ __('messages.nav.ingredients') }}
            </a>
            <a href="{{ route('companies.index') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('companies.*'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('companies.*')])>
                <x-lucide-building-2 class="w-5 h-5" />
                {{ __('messages.nav.companies') }}
            </a>
        </div>

        <div class="border-t border-default-medium dark:border-gray-700 pt-5">
            <p class="px-4 pb-2 text-xs font-semibold text-body uppercase tracking-wider dark:text-gray-400">{{ __('messages.nav.platform') }}</p>
            <div class="space-y-1">
                <a href="{{ route('about') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('about'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('about')])>
                    <x-lucide-info class="w-5 h-5" />
                    {{ __('messages.nav.about') }}
                </a>
                <a href="{{ route('contact') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('contact'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('contact')])>
                    <x-lucide-mail class="w-5 h-5" />
                    {{ __('messages.nav.contact') }}
                </a>
                <a href="{{ route('privacy-policy') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('privacy-policy'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('privacy-policy')])>
                    <x-lucide-shield class="w-5 h-5" />
                    {{ __('messages.nav.privacy_policy') }}
                </a>
                <a href="{{ route('terms-of-service') }}" onclick="toggleMobileMenu()" @class(['flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-base transition-colors duration-150', 'border-s-2 border-brand bg-brand-soft text-fg-brand dark:bg-brand/20' => request()->routeIs('terms-of-service'), 'text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' => !request()->routeIs('terms-of-service')])>
                    <x-lucide-file-text class="w-5 h-5" />
                    {{ __('messages.nav.terms_of_service') }}
                </a>
            </div>
        </div>

        <div class="border-t border-default-medium dark:border-gray-700 pt-5">
            <x-language-switcher-mobile />
        </div>

        <div class="border-t border-default-medium dark:border-gray-700 pt-5">
            @auth
                <div class="space-y-3">
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-sm font-medium flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-heading dark:text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-body dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full text-start px-4 py-3 text-sm font-medium text-body hover:text-heading hover:bg-neutral-secondary-soft dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 rounded-base transition-colors duration-150">
                            <x-lucide-log-out class="w-5 h-5 text-body dark:text-gray-400" />
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
            sidebar.classList.remove('translate-x-0', 'rtl:translate-x-0');
            sidebar.classList.add('translate-x-[-100%]', 'rtl:translate-x-[100%]');
            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = '';
            btn?.classList.remove('active');
        } else {
            sidebar.classList.add('open');
            sidebar.classList.remove('translate-x-[-100%]', 'rtl:translate-x-[100%]');
            sidebar.classList.add('translate-x-0', 'rtl:translate-x-0');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
            btn?.classList.add('active');
        }
    }
</script>
