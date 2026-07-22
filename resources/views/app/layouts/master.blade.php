<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() === 'ar') dir="rtl" @endif class="overflow-x-hidden w-full relative">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'VetPedia')</title>
    <meta name="description" content="@yield('meta_description', 'Veterinary pharmaceutical and disease knowledge platform.')">
    <meta name="keywords" content="@yield('meta_keywords', 'veterinary drugs, veterinary diseases, active ingredients')">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', 'VetPedia'))">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(app()->getLocale() === 'ar')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            html { font-family: 'Readex Pro', sans-serif; }
        </style>
    @endif
    {{-- Dark mode: apply before render to prevent flash --}}
    <script>
        (function() {
            function applyTheme() {
                var theme = localStorage.getItem('theme');
                if (!theme) {
                    theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
            applyTheme();
            document.addEventListener('livewire:navigated', applyTheme);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    @yield('css')
</head>
<body class="bg-neutral-secondary-soft dark:bg-slate-900 min-h-screen flex flex-col w-full relative overflow-x-hidden">

    @include('app.layouts.main-header')

    <div class="px-4 pb-4 flex-1 flex flex-col">
        <div class="mt-14 flex-1 flex flex-col">
            @yield('page-header')
            @yield('content')
        </div>
    </div>

    @include('app.layouts.footer')

    <x-cookie-consent />

    @unless (request()->routeIs('products.compare'))
        @livewire('products.product-compare-bar')
    @endunless

    @include('app.layouts.footer-scripts')

    <script>
        (function() {
            const isInternal = (href) => {
                if (!href) return false;
                if (href.startsWith('#')) return false;
                if (href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) return false;
                if (href.startsWith('http') && !href.startsWith(window.location.origin)) return false;
                if (href.includes('/storage/')) return false;
                return true;
            };

            const addNavigate = (root) => {
                (root.querySelectorAll ? root.querySelectorAll('a[href]:not([wire\\:navigate]):not([download]):not([target="_blank"])') : [])
                    .forEach(link => {
                        if (isInternal(link.getAttribute('href'))) {
                            link.setAttribute('wire:navigate', '');
                        }
                    });
            };

            document.addEventListener('DOMContentLoaded', () => addNavigate(document));

            const observer = new MutationObserver(mutations => {
                for (const m of mutations) {
                    for (const node of m.addedNodes) {
                        if (node.nodeType === 1) addNavigate(node);
                    }
                }
            });

            if (document.body) observer.observe(document.body, { childList: true, subtree: true });
        })();
    </script>
</body>
</html>
