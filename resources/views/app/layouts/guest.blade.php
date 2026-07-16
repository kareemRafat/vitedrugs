<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() === 'ar') dir="rtl" @endif>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'VetPedia') }} - @yield('title', '')</title>
    @if(app()->getLocale() === 'ar')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Readex+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            html { font-family: 'Readex Pro', sans-serif; }
        </style>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>
<body class="bg-neutral-secondary-soft dark:bg-slate-900 antialiased" data-loading-text="{{ __('messages.common.loading') }}">
    <div class="flex flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8 min-h-screen">
        <a href="{{ route('home') }}" class="flex items-center mb-8 space-x-3 rtl:space-x-reverse">
            <x-lucide-shield-check class="w-8 h-8 text-fg-brand dark:text-brand" />
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">{{ config('app.name', 'VetPedia') }}</span>
        </a>

        @yield('content')

        <p class="mt-8 text-sm text-body dark:text-slate-400">
            &copy; {{ date('Y') }} <a href="{{ route('home') }}" class="hover:underline text-heading dark:text-slate-300">{{ config('app.name', 'VetPedia') }}</a>. All rights reserved.
        </p>
    </div>

    @yield('js')

    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('[type="submit"]');
                if (btn && !btn.dataset.loading) {
                    btn.dataset.loading = 'true';
                    btn.disabled = true;
                    const loadingText = document.body.dataset.loadingText || 'Loading...';
                    const spinner = '<svg class="animate-spin w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
                    btn.innerHTML = '<span class="inline-flex items-center gap-2">' + spinner + ' ' + loadingText + '</span>';
                }
            });
        });
    </script>
</body>
</html>
