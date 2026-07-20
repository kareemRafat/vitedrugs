<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() === 'ar') dir="rtl" @endif>
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
            var theme = localStorage.getItem('theme');
            if (!theme) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>
<body class="bg-neutral-secondary-soft dark:bg-slate-900 min-h-screen flex flex-col overflow-x-hidden">

    @include('app.layouts.main-header')

    <div class="px-4 pb-4 flex-1 flex flex-col">
        <div class="mt-20 flex-1 flex flex-col">
            @yield('page-header')
            @yield('content')
        </div>
    </div>

    @include('app.layouts.footer')

    <x-cookie-consent />

    @include('app.layouts.footer-scripts')
</body>
</html>
