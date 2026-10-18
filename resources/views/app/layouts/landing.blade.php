{{--
    Standalone document for the redesigned landing page.

    Deliberately does NOT extend `app.layouts.master` and does not set
    `data-part` on <body>. Two reasons:

      1. `master` hard-codes `data-part="large-animals"`, which themes the whole
         page orange. The reference design is indigo.
      2. The palette is applied through `.landing-scope` (see resources/css/app.css),
         which re-declares the stock slate scale so the `dark` remap at the top of
         app.css cannot reach this subtree. Extending `master` would put the
         remap on the same element that carries the scope.

    Cookie consent and the compare bar sit OUTSIDE `.landing-scope` because both
    are built from Flowbite semantic tokens (`bg-brand`, `bg-neutral-primary-soft`)
    and must keep the site's normal theming.
--}}
<!DOCTYPE html>
{{-- `scroll-smooth` drives the in-page navbar anchors. It belongs on <html>,
     not inside .landing-scope: scroll-behavior only affects the element that
     actually scrolls. --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() === 'ar') dir="rtl" @endif class="overflow-x-hidden w-full relative scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>@yield('title', 'VetPedia')</title>
    <meta name="description" content="@yield('meta_description', 'Veterinary pharmaceutical and disease knowledge platform.')">
    <meta name="keywords" content="@yield('meta_keywords', 'veterinary drugs, veterinary diseases, active ingredients')">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', 'VetPedia'))">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(config('app.noindex'))
        <meta name="robots" content="noindex, nofollow, noarchive">
    @endif

    {{-- Reference font stack, loaded only on this page. Inter + Cairo (Arabic) + JetBrains Mono.
         Cairo replaced IBM Plex Sans Arabic; it is a variable family, so the whole
         400-800 range is requested in one go and the browser synthesises nothing.
         The font-family itself is applied inside `.landing-scope`, never on `html`,
         so no other page changes type. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    {{-- Dark mode: apply before render to prevent flash.
         Reads the same `theme` key the rest of the site writes, so the toggle stays
         in sync between this page and every other one. The reference used its own
         `vetpedia-theme` key, which would have silently desynced the two. --}}
    <script>
        (function() {
            function applyTheme() {
                var theme = localStorage.getItem('theme');
                if (!theme) {
                    theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.classList.toggle('dark', theme === 'dark');
            }
            applyTheme();
            document.addEventListener('livewire:navigated', applyTheme);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    @yield('css')
</head>
<body class="bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen w-full relative overflow-x-hidden">

    <div class="landing-scope flex min-h-screen flex-col">
        @include('app.layouts.landing-header')

        {{-- No top padding here: the bar is flush and h-16, and the hero owns its
             own clearance. Padding both would double the gap under the bar.
             `tabindex="-1"` so the scroll-to-top control can move focus here
             after sending the page back up - see resources/js/back-to-top.js. --}}
        <main tabindex="-1" class="flex-1 outline-none">
            @yield('content')
        </main>

        @include('app.layouts.landing-footer')

        {{-- Landing-only, and inside the scope so it keeps the landing palette. --}}
        <x-back-to-top />
    </div>

    {{-- Contact feedback. The contact form dispatches `contact-sent` /
         `contact-failed`, but the layout has to own the element the toast script
         looks up by id, otherwise those dispatches resolve to nothing. Anchored
         bottom-right: the navbar floats at top-4 and the cookie consent occupies
         the bottom-left corner. --}}
    <x-toast id="contactToast" type="success" position="bottom" title="" message="" />

    <x-cookie-consent />

    @unless (request()->routeIs('products.compare'))
        @livewire('products.product-compare-bar')
    @endunless

    @yield('js')

</body>
</html>