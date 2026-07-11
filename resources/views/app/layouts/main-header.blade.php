<nav class="fixed top-0 z-50 w-full bg-neutral-primary-soft border-b border-default-medium dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-body rounded-base sm:hidden hover:bg-neutral-secondary-soft focus:outline-none focus:ring-2 focus:ring-neutral-secondary-soft dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    <span class="sr-only">Open sidebar</span>
                    <x-lucide-menu class="w-6 h-6" />
                </button>
                <a href="{{ route('home') }}" class="flex ms-2 md:me-24">
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">VetPedia</span>
                </a>
            </div>
            <div class="flex items-center gap-1 sm:gap-2">
                <a href="{{ route('products.index') }}" class="hidden sm:inline-flex text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">Products</a>
                <a href="{{ route('diseases.index') }}" class="hidden sm:inline-flex text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">Diseases</a>
                <a href="{{ route('active-ingredients.index') }}" class="hidden sm:inline-flex text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">Ingredients</a>
                <a href="{{ route('companies.index') }}" class="hidden sm:inline-flex text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white px-2 py-1 text-sm font-medium">Companies</a>

                <a href="{{ route('search') }}" class="text-body hover:text-fg-brand dark:text-gray-400 dark:hover:text-white p-2 rounded-base" title="Search">
                    <x-lucide-search class="w-5 h-5" />
                </a>

                @auth
                    <div class="flex items-center ms-3">
                        <button id="userDropdown" data-dropdown-toggle="userDropdownMenu" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-neutral-secondary-soft dark:focus:ring-gray-600" type="button">
                            <span class="sr-only">Toggle user menu</span>
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
                                        <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-neutral-secondary-soft dark:hover:bg-gray-600 dark:hover:text-white">Sign out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-fg-brand hover:text-brand-strong dark:text-brand dark:hover:text-brand px-3 py-1.5 text-sm font-medium border border-brand rounded-base hover:bg-brand-soft dark:hover:bg-brand/20">Sign in</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
