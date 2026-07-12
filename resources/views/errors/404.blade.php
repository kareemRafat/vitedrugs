<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Found | VetPedia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 p-0 flex items-center justify-center min-h-screen bg-white font-sans">
    <div class="text-center px-6">
        <div class="text-[10rem] sm:text-[14rem] font-bold leading-none tracking-tighter text-slate-100 select-none">
            404
        </div>
        <h1 class="text-xl sm:text-2xl font-semibold text-slate-800 -mt-6 mb-3">
            {{ __('messages.errors.404_heading') }}
        </h1>
        <p class="text-slate-500 text-sm sm:text-base max-w-md mx-auto mb-8">
            {{ __('messages.errors.404_message') }}
        </p>
        <a href="{{ route('home') }}"
            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 rounded-full transition-all duration-200">
            <svg class="w-4 h-4 rtl:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            {{ __('messages.errors.404_cta') }}
        </a>
    </div>
</body>
</html>
