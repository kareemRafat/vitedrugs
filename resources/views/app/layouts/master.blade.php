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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>
<body class="bg-neutral-secondary-soft dark:bg-gray-900 min-h-screen flex flex-col">

    @include('app.layouts.main-header')

    <div class="p-4 flex-1 flex flex-col">
        <div class="mt-14 flex-1 flex flex-col">
            @yield('page-header')
            @yield('content')
        </div>
    </div>

    @include('app.layouts.footer')

    @include('app.layouts.footer-scripts')
</body>
</html>
