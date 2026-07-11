<nav class="fixed top-0 z-50 w-full bg-neutral-primary-soft border-b border-default-medium dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5">
        <div class="grid grid-cols-3 items-center">
            <div class="flex justify-start">
                <a href="{{ route('home') }}" class="flex">
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">VetPedia</span>
                </a>
            </div>

            <div class="hidden sm:flex items-center justify-center gap-1">
                <a href="{{ route('products.index') }}" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">{{ __('messages.nav.products') }}</a>
                <a href="{{ route('diseases.index') }}" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">{{ __('messages.nav.diseases') }}</a>
                <a href="{{ route('active-ingredients.index') }}" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">{{ __('messages.nav.ingredients') }}</a>
                <a href="{{ route('companies.index') }}" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">{{ __('messages.nav.companies') }}</a>

                <button id="moreDropdown" data-dropdown-toggle="moreDropdownMenu" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium inline-flex items-center gap-1" type="button">
                    {{ __('messages.nav.platform') }}
                    <x-lucide-chevron-down class="w-4 h-4" />
                </button>

                <div id="moreDropdownMenu" class="z-50 hidden my-4 w-48 bg-neutral-primary-soft rounded-base shadow-lg dark:bg-gray-700">
                    <ul class="py-1 text-sm text-heading dark:text-gray-300">
                        <li>
                            <a href="{{ route('about') }}" class="block px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white">{{ __('messages.nav.about') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="block px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white">{{ __('messages.nav.contact') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('privacy-policy') }}" class="block px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white">{{ __('messages.nav.privacy_policy') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('terms-of-service') }}" class="block px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white">{{ __('messages.nav.terms_of_service') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-end gap-1 sm:gap-2">
                <a href="{{ route('search') }}" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white p-2 rounded-base" title="{{ __('messages.nav.search') }}">
                    <x-lucide-search class="w-5 h-5" />
                </a>

                <x-language-switcher />

                @auth
                    <div class="flex items-center">
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
                    <a href="{{ route('login') }}" class="text-fg-brand hover:text-brand-strong dark:text-brand dark:hover:text-brand px-3 py-1.5 text-sm font-medium border border-brand rounded-base hover:bg-brand-soft dark:hover:bg-brand/20">{{ __('messages.nav.sign_in') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
