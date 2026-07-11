@extends('app.layouts.guest')

@section('title', 'Sign in')

@section('content')
    <div class="w-full max-w-md p-6 bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 sm:p-8">
        <h2 class="mb-2 text-2xl font-bold leading-tight tracking-tight text-heading dark:text-white">
            Sign in to your account
        </h2>
        <p class="mb-6 text-sm font-light text-body dark:text-gray-400">
            Enter your credentials to access the platform
        </p>

        @if ($errors->any())
            <div class="flex items-center p-4 mb-4 text-sm rounded-base bg-danger-soft border border-danger-subtle text-fg-danger-strong dark:bg-gray-700 dark:text-red-400 dark:border-red-600" role="alert">
                <x-lucide-circle-alert class="shrink-0 inline w-4 h-4 me-3" />
                <span class="sr-only">Error</span>
                <div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="flex items-center p-4 mb-4 text-sm rounded-base bg-success-soft border border-success-subtle text-fg-success-strong dark:bg-gray-700 dark:text-green-400 dark:border-green-600" role="alert">
                <x-lucide-circle-check class="shrink-0 inline w-4 h-4 me-3" />
                <span class="sr-only">Success</span>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-5">
            @csrf

            <div>
                <label for="email" class="block mb-2.5 text-sm font-medium text-heading dark:text-white">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-mail class="w-4 h-4 text-body dark:text-gray-400" />
                    </div>
                    <input type="email" name="email" id="email"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand @error('email') border-danger-subtle dark:border-red-600 @enderror"
                        placeholder="name@example.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username">
                </div>
            </div>

            <div>
                <label for="password" class="block mb-2.5 text-sm font-medium text-heading dark:text-white">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-lock class="w-4 h-4 text-body dark:text-gray-400" />
                    </div>
                    <input type="password" name="password" id="password"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand @error('password') border-danger-subtle dark:border-red-600 @enderror"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="remember" name="remember" type="checkbox"
                            class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-brand-soft">
                    </div>
                    <label for="remember" class="ms-2 text-sm font-medium text-heading select-none dark:text-gray-300">Remember me</label>
                </div>
            </div>

            <button type="submit"
                class="w-full text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none dark:bg-brand dark:hover:bg-brand-strong dark:focus:ring-brand-medium">
                Sign in
            </button>

            <p class="text-sm font-light text-body dark:text-gray-400">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-fg-brand hover:underline dark:text-brand">Register here</a>
            </p>
        </form>
    </div>
@endsection
