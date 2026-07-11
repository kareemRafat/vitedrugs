@extends('app.layouts.guest')

@section('title', 'Welcome')

@section('content')
    <div class="w-full max-w-md p-6 bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 sm:p-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-brand-soft dark:bg-brand/20">
                <x-lucide-shield-check class="w-8 h-8 text-fg-brand dark:text-brand" />
            </div>
            <h2 class="text-2xl font-bold text-heading dark:text-white mb-2">
                Welcome to {{ config('app.name', 'VetPedia') }}
            </h2>
            <p class="text-body dark:text-gray-400 mb-6">
                Veterinary drug database — browse products, companies, diseases, and active ingredients.
            </p>

            <div class="space-y-3">
                <a href="{{ route('login') }}"
                    class="block w-full text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-5 py-2.5 focus:outline-none dark:bg-brand dark:hover:bg-brand-strong dark:focus:ring-brand-medium">
                    Sign in
                </a>

                <a href="{{ route('register') }}"
                    class="block w-full text-heading bg-neutral-primary-soft border border-default-medium hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-brand-medium">
                    Create account
                </a>

                <div class="relative my-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-default-medium dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-neutral-primary-soft dark:bg-gray-800 text-body dark:text-gray-400">or</span>
                    </div>
                </div>

                <a href="{{ route('home') }}"
                    class="block w-full px-5 py-2.5 text-sm font-medium text-heading focus:outline-none bg-neutral-primary-soft rounded-base border border-default-medium hover:bg-neutral-secondary-soft hover:text-fg-brand focus:z-10 focus:ring-4 focus:ring-neutral-secondary-soft dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Continue as guest
                </a>
            </div>
        </div>
    </div>
@endsection
