<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() === 'ar') dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VetPedia') }} - @yield('title', '')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>
<body class="bg-neutral-secondary-soft dark:bg-gray-900 antialiased">
    <div class="flex flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8 min-h-screen">
        <a href="{{ route('home') }}" class="flex items-center mb-8 space-x-3 rtl:space-x-reverse">
            <x-lucide-shield-check class="w-8 h-8 text-fg-brand dark:text-brand" />
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">{{ config('app.name', 'VetPedia') }}</span>
        </a>

        @yield('content')

        <p class="mt-8 text-sm text-body dark:text-gray-400">
            &copy; {{ date('Y') }} <a href="{{ route('home') }}" class="hover:underline text-heading dark:text-gray-300">{{ config('app.name', 'VetPedia') }}</a>. All rights reserved.
        </p>
    </div>

    @yield('js')
</body>
</html>
