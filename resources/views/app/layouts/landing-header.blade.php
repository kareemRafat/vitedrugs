{{--
    Landing-only chrome. Does not replace app.layouts.main-header, which every other page still uses.

    A floating pill, as the reference has it: offset from the top edge and
    narrower than the page sections (max-w-6xl against their max-w-7xl) so it
    reads as a separate floating object rather than a second content column.

    The pill is translucent and blurs what passes beneath it, so the un-overlaid
    hero photograph reads through it. Dark mode uses slate-950 rather than
    slate-900 at a higher opacity: over a photograph the lighter slate-900 tint
    washed out to grey and the nav lost contrast against the image.

    Below `sm` the theme toggle and the language switcher live in the drawer, not
    the pill. The pill's own row does not compress — a wordmark plus five
    controls overflowed its own rounded end on a phone and the layout's
    overflow-x-hidden clipped the menu button off-screen — so the two widest
    controls are hidden and reproduced in the drawer instead.

    `x-effect` pins the scroll lock to the single `drawerOpen` flag the drawer,
    the Escape handler and the hamburger all already write to, so there is no
    second source of truth to fall out of step.
--}}
@php
    /*
     * In-page anchors only. The navbar is chrome for THIS page, so it links to
     * the sections below it and scrolls there rather than navigating away.
     *
     * The blog section renders only when something is published, so its link is
     * conditional too — a permanent link to #blog would be a dead anchor on an
     * empty database.
     */
    $hasBlogSection = ($latestBlogs ?? collect())->isNotEmpty();

    $landingNav = [
        ['hash' => '#about', 'label' => __('messages.landing.header.pillars')],
        ['hash' => '#browse-hub', 'label' => __('messages.landing.header.knowledge_bases')],
        ['hash' => '#database-stats', 'label' => __('messages.landing.header.statistics')],
        ['hash' => '#contact', 'label' => __('messages.nav.contact')],
    ];

    if ($hasBlogSection) {
        $landingNav[] = ['hash' => '#blog', 'label' => __('messages.nav.blog')];
    }
@endphp

<header id="site-header"
    class="fixed top-4 inset-x-0 z-50 flex justify-center px-4 sm:px-6 pointer-events-none transition-all duration-300"
    x-data="{ scrolled: false, drawerOpen: false }"
    @scroll.window="scrolled = window.scrollY > 20"
    @keydown.escape.window="drawerOpen = false"
    @resize.window="if (window.innerWidth >= 1024) drawerOpen = false"
    x-effect="document.body.style.overflow = drawerOpen ? 'hidden' : ''">

    {{-- pointer-events-none on the wrapper lets clicks fall through the gaps
         around the pill; the pill itself opts back in. --}}
    <div class="w-full max-w-6xl mx-auto pointer-events-auto">
        {{-- px-4 rather than px-5 below sm: at 320px the pill has 256px of usable
             width and its contents need ~239px. px-5 left 3px of slack there,
             which the `rounded-full` end cap then clipped. --}}
        <div class="flex items-center justify-between h-14 px-4 sm:px-6 rounded-full bg-slate-100/85 dark:bg-slate-950/90 backdrop-blur-md border border-slate-300/80 dark:border-slate-800/80 shadow-lg shadow-indigo-950/5 dark:shadow-black/30 transition-all duration-300"
            :class="scrolled ? 'shadow-xl shadow-indigo-950/10 dark:shadow-black/40' : ''">

            {{-- Wordmark --}}
            <a href="{{ route('home') }}" aria-label="{{ __('messages.landing.header.home') }}"
                class="group flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded-lg py-1 px-1.5">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-sky-400 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3v18M3 12h18"></path>
                        <circle cx="12" cy="12" fill="currentColor" fill-opacity="0.3" r="3"></circle>
                    </svg>
                </span>
                <span class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-sans">
                    ArVet<span class="text-indigo-600 dark:text-indigo-400">Pedia</span>
                </span>
            </a>

            {{-- Desktop navigation --}}
            <nav aria-label="{{ __('messages.landing.header.primary_nav') }}"
                class="hidden lg:flex items-center gap-8 text-[15px] font-semibold text-slate-600 dark:text-slate-300">
                @foreach ($landingNav as $item)
                    <a href="{{ $item['hash'] }}"
                        class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            {{-- Right cluster --}}
            <div class="flex items-center gap-2.5 sm:gap-4">

                {{-- Quick search: focuses the hero field, which Phase 7 adds. --}}
                <button type="button" aria-label="{{ __('messages.landing.header.search') }}"
                    onclick="document.getElementById('hero-search-input')?.focus(); window.scrollTo({ top: 0, behavior: 'smooth' });"
                    class="p-2 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m21 21-4.35-4.35M19 11a8 8 0 1 1-16 0 8 8 0 0 1 16 0z" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>

                {{-- Theme toggle. Uses the same `theme` key as the shared header.
                     Hidden below sm and reproduced in the drawer: with the pill's
                     wordmark plus this button, the search button, the EN/ع switcher
                     and the hamburger, the row needed ~419px against the ~303px a
                     375px phone leaves inside the pill, so the switcher and the
                     hamburger were pushed past the rounded end and clipped by the
                     layout's overflow-x-hidden. --}}
                <button type="button" aria-label="{{ __('messages.landing.header.toggle_theme') }}" onclick="landingSetTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                    class="hidden sm:block p-2 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <svg class="w-5 h-5 block dark:hidden text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14-1.41 1.41" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>

                {{-- Bilingual switcher. Hidden below sm and reproduced in the drawer — see
                     the theme toggle above for why. It is the widest item in the
                     right cluster, so it was the first thing to push the pill
                     over its own edge. --}}
                <div class="relative hidden sm:inline-flex items-center bg-slate-100 dark:bg-slate-800/80 p-0.5 rounded-lg border border-slate-200/80 dark:border-slate-700/80 text-xs font-semibold">
                    <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" aria-label="{{ __('messages.landing.header.switch_to_en') }}"
                        @class([
                            'px-2 py-1 rounded transition-all',
                            'bg-white dark:bg-slate-700 text-indigo-600 dark:text-white shadow-sm' => app()->getLocale() === 'en',
                            'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' => app()->getLocale() !== 'en',
                        ])>EN</a>
                    <span class="text-slate-300 dark:text-slate-600 px-0.5 select-none" aria-hidden="true">|</span>
                    <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" aria-label="{{ __('messages.landing.header.switch_to_ar') }}"
                        @class([
                            'px-2 py-1 rounded transition-all font-sans',
                            'bg-white dark:bg-slate-700 text-indigo-600 dark:text-white shadow-sm' => app()->getLocale() === 'ar',
                            'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' => app()->getLocale() !== 'ar',
                        ])>&nbsp;ع&nbsp;</a>
                </div>

                @auth
                    <div x-data="{ userOpen: false }" class="hidden sm:block relative">
                        <button type="button" @click="userOpen = !userOpen" @click.outside="userOpen = false"
                            class="w-9 h-9 rounded-full bg-[#1e4b7a] dark:bg-[#1e3a8a] hover:bg-[#163a5e] dark:hover:bg-[#172d6e] active:bg-[#112c47] dark:active:bg-[#132354] text-white flex items-center justify-center text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            aria-label="{{ __('messages.landing.header.toggle_user_menu') }}">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </button>
                        <div x-show="userOpen" x-cloak @click.outside="userOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute end-0 top-full z-50 my-2 w-52 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700">
                                <div class="text-sm font-semibold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</div>
                            </div>
                            <ul class="py-1 text-sm">
                                <li>
                                    <a href="{{ route('profile.show') }}" wire:navigate @click="userOpen = false"
                                        class="block px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">{{ __('messages.nav.dashboard') }}</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-start px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">{{ __('messages.nav.sign_out') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[#1e4b7a] dark:bg-[#1e3a8a] hover:bg-[#163a5e] dark:hover:bg-[#172d6e] active:bg-[#112c47] dark:active:bg-[#132354] transition-all shadow-sm shadow-[#0b1f38]/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        {{ __('messages.landing.header.sign_in') }}
                    </a>
                @endauth

                {{-- Hamburger --}}
                <button type="button" @click="drawerOpen = true"
                    :aria-expanded="drawerOpen ? 'true' : 'false'"
                    aria-label="{{ __('messages.landing.header.open_menu') }}"
                    class="lg:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile drawer. `end-0` anchors to the right in LTR and the left in RTL;
         `rtl:` on the closed transform flips the offset to match.

         pointer-events-auto is load-bearing, not decoration: this overlay is a
         child of the pointer-events-none <header>, and pointer-events inherits,
         so without it the backdrop inherited `none` and its own
         @click="drawerOpen = false" could never fire. Tapping the dimmed area
         did nothing; only the X or a nav link closed the drawer. --}}
    <div x-show="drawerOpen" x-cloak role="dialog" aria-modal="true"
        @click="drawerOpen = false"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 pointer-events-auto bg-slate-900/60 backdrop-blur-sm">

        <div @click.stop
            x-show="drawerOpen"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full rtl:-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full rtl:-translate-x-full"
            class="fixed top-0 bottom-0 end-0 w-full max-w-xs bg-slate-50 dark:bg-slate-900 p-6 shadow-2xl flex flex-col justify-between overflow-y-auto">

            <div>
                <div class="flex items-center justify-between pb-6 border-b border-slate-200 dark:border-slate-800">
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Vet<span class="text-indigo-600 dark:text-indigo-400">Pedia</span>
                    </span>
                    <button type="button" @click="drawerOpen = false"
                        aria-label="{{ __('messages.landing.header.close_menu') }}"
                        class="p-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-6">
                    {{-- Mirrors the desktop nav exactly, minus the grouped
                         headings — there is only one list now. --}}
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('messages.landing.header.drawer_sections') }}</p>
                    <nav class="space-y-1">
                        @foreach ($landingNav as $item)
                            <a href="{{ $item['hash'] }}" @click="drawerOpen = false"
                                class="block px-3 py-2.5 rounded-lg text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">{{ $item['label'] }}</a>
                        @endforeach
                    </nav>
                </div>

                {{-- The two controls the pill drops below sm (see the header
                     comment). Styled with the landing palette rather than pulled
                     from x-language-switcher-mobile: that component is built on
                     Flowbite semantic tokens and hardcodes onclick="toggleMobileMenu()",
                     a function defined in app.layouts.main-header, which this
                     layout does not include — the link would navigate and leave
                     the drawer open. --}}
                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ __('messages.common.switch_language') }}</p>
                    <div class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 dark:bg-slate-800/80 p-1 border border-slate-200/80 dark:border-slate-700/80 text-sm font-semibold">
                        <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" @click="drawerOpen = false"
                            aria-label="{{ __('messages.landing.header.switch_to_en') }}"
                            @class([
                                'flex items-center justify-center rounded-lg py-2 transition-all',
                                'bg-white dark:bg-slate-700 text-indigo-600 dark:text-white shadow-sm' => app()->getLocale() === 'en',
                                'text-slate-600 dark:text-slate-400' => app()->getLocale() !== 'en',
                            ])>EN</a>
                        <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" @click="drawerOpen = false"
                            aria-label="{{ __('messages.landing.header.switch_to_ar') }}"
                            @class([
                                'flex items-center justify-center rounded-lg py-2 transition-all font-sans',
                                'bg-white dark:bg-slate-700 text-indigo-600 dark:text-white shadow-sm' => app()->getLocale() === 'ar',
                                'text-slate-600 dark:text-slate-400' => app()->getLocale() !== 'ar',
                            ])>ع</a>
                    </div>

                    {{-- Icon and label agree on which mode is ACTIVE, so the
                         control states what it is rather than what it will do.
                         The pill's bare icon shows the target instead; it has no
                         label to contradict. --}}
                    <button type="button"
                        @click="landingSetTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark')"
                        class="mt-3 w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-base font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <svg class="w-5 h-5 block dark:hidden text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41m14.14-14.14-1.41 1.41" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <svg class="w-5 h-5 hidden dark:block text-indigo-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <span class="dark:hidden">{{ __('messages.common.light_mode') }}</span>
                        <span class="hidden dark:inline">{{ __('messages.common.dark_mode') }}</span>
                    </button>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 dark:border-slate-800">
                @auth
                    <a href="{{ route('profile.show') }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl text-base font-semibold text-white bg-[#1e4b7a] dark:bg-[#1e3a8a] hover:bg-[#163a5e] dark:hover:bg-[#172d6e] active:bg-[#112c47] dark:active:bg-[#132354] transition shadow-sm">{{ __('messages.nav.dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl text-base font-semibold text-white bg-[#1e4b7a] dark:bg-[#1e3a8a] hover:bg-[#163a5e] dark:hover:bg-[#172d6e] active:bg-[#112c47] dark:active:bg-[#132354] transition shadow-sm">{{ __('messages.landing.header.drawer_sign_in') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>

<script>
    /**
     * Namespaced so it can never collide with `setTheme`/`toggleTheme` from
     * app.layouts.main-header. Both layouts write the same `theme` key, so the
     * two pages stay in sync whichever one you came from.
     */
    function landingSetTheme(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        localStorage.setItem('theme', theme);
    }
</script>