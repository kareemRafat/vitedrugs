{{-- Landing-only chrome. Does not replace app.layouts.footer, which every other page still uses. --}}
@php
    $landingPlatform = [
        ['route' => 'large-animals.home', 'label' => __('messages.landing.footer.link_large_animals')],
        ['route' => 'drugs.home', 'label' => __('messages.landing.footer.link_drugs')],
        ['route' => 'poultry.home', 'label' => __('messages.landing.footer.link_poultry')],
        ['route' => 'active-ingredients.index', 'label' => __('messages.landing.footer.link_ingredients')],
        ['route' => 'products.compare', 'label' => __('messages.landing.footer.link_compare')],
    ];

    $landingResources = [
        ['route' => 'blog.index', 'label' => __('messages.landing.footer.link_blog')],
        ['route' => 'about', 'label' => __('messages.landing.footer.link_about')],
        ['route' => 'contact', 'label' => __('messages.landing.footer.link_contact')],
        ['route' => 'privacy-policy', 'label' => __('messages.landing.footer.link_privacy')],
        ['route' => 'terms-of-service', 'label' => __('messages.landing.footer.link_terms')],
    ];
@endphp

<footer id="footer" class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 pb-12 border-b border-slate-800">

            {{-- Brand --}}
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4">
                    <span class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold">V</span>
                    <span class="text-2xl font-bold tracking-tight text-white font-sans">
                        Vet<span class="text-indigo-400">Pedia</span>
                    </span>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed mb-6 max-w-sm">
                    {{ __('messages.landing.footer.tagline') }}
                </p>
                {{-- The reference put "Medical Grade · Open Clinical Access · ISO/HL7 Compliant" here.
                     Nothing in this codebase supports those claims, so it is replaced by a
                     limitation that is true rather than a certification that is not. --}}
                <p class="text-xs text-slate-500">
                    {{ __('messages.landing.footer.disclaimer') }}
                </p>
            </div>

            {{-- Platform --}}
            <div class="lg:col-span-3">
                <p class="text-xs font-bold text-white uppercase tracking-wider mb-4">{{ __('messages.landing.footer.platform') }}</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach ($landingPlatform as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="hover:text-white transition">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Resources --}}
            <div class="lg:col-span-3">
                <p class="text-xs font-bold text-white uppercase tracking-wider mb-4">{{ __('messages.landing.footer.resources') }}</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach ($landingResources as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="hover:text-white transition">{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Language --}}
            <div class="lg:col-span-2">
                <p class="text-xs font-bold text-white uppercase tracking-wider mb-4">{{ __('messages.landing.footer.language') }}</p>

                <div class="flex flex-col gap-2.5 mb-6">
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        @php
                            // getCurrentLocale() rather than app()->getLocale(), so "current"
                            // is computed the same way the header switchers compute it.
                            $isCurrentLocale = $localeCode === LaravelLocalization::getCurrentLocale();
                        @endphp
                        <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                            aria-current="{{ $isCurrentLocale ? 'true' : 'false' }}"
                            @class([
                                'group flex items-center gap-3 p-3 rounded-xl border transition-all duration-300',
                                'bg-indigo-600/15 border-indigo-400/50 shadow-lg shadow-indigo-950/20' => $isCurrentLocale,
                                'bg-slate-800/60 border-slate-700/60 hover:bg-slate-800 hover:border-slate-600' => ! $isCurrentLocale,
                            ])>
                            <span class="flex-1 min-w-0">
                                {{-- The code is the locale key. The old markup sliced the
                                     native name instead, which rendered Arabic as "ال". --}}
                                <span @class([
                                    'block text-sm font-semibold truncate',
                                    'text-white' => $isCurrentLocale,
                                    'text-slate-200' => ! $isCurrentLocale,
                                ])>{{ $properties['native'] }}</span>
                                <span class="block text-[11px] font-mono uppercase text-slate-500">{{ strtoupper($localeCode) }}</span>
                            </span>
                            @if ($isCurrentLocale)
                                <x-lucide-check class="w-4 h-4 shrink-0 text-indigo-300" />
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>
                &copy; {{ date('Y') }}
                <a href="{{ route('home') }}" class="hover:text-white transition">{{ __('messages.nav.footer_text') }}</a>
            </p>
            <button type="button" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
                aria-label="{{ __('messages.landing.footer.back_to_top') }}"
                class="inline-flex items-center gap-1.5 text-slate-400 hover:text-white transition focus:outline-none focus:underline">
                <span>{{ __('messages.landing.footer.back_to_top') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="m4.5 15.75 7.5-7.5 7.5 7.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
    </div>
</footer>
