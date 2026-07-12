@extends('app.layouts.guest')

@section('title', __('messages.verify_email.title'))

@section('content')
    <div class="w-full max-w-md p-6 bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 sm:p-8">
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-14 h-14 bg-brand-soft rounded-full flex items-center justify-center mb-4">
                <x-lucide-mail class="w-7 h-7 text-fg-brand" />
            </div>
            <h2 class="text-2xl font-bold leading-tight tracking-tight text-heading dark:text-white">
                {{ __('messages.verify_email.heading') }}
            </h2>
            <p class="mt-2 text-sm text-body dark:text-gray-400 max-w-xs">
                {{ __('messages.verify_email.subtitle') }}
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="flex items-center p-4 mb-4 text-sm rounded-base bg-success-soft border border-success-subtle text-fg-success-strong dark:bg-gray-700 dark:text-green-400 dark:border-green-600" role="alert">
                <x-lucide-circle-check class="shrink-0 inline w-4 h-4 me-3" />
                <span>{{ __('messages.verify_email.sent') }}</span>
            </div>
        @endif

        <p class="mb-6 text-sm text-body dark:text-gray-400 leading-relaxed">
            {{ __('messages.verify_email.instruction') }}
        </p>

        <form method="POST" action="{{ route('verification.resend') }}" class="space-y-3">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none dark:bg-brand dark:hover:bg-brand-strong dark:focus:ring-brand-medium">
                <span class="inline-flex items-center gap-2">
                    <x-lucide-refresh-cw class="w-4 h-4" />
                    {{ __('messages.verify_email.resend_button') }}
                </span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center text-body bg-transparent box-border border border-default-medium hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700">
                <span class="inline-flex items-center gap-2">
                    <x-lucide-log-out class="w-4 h-4" />
                    {{ __('messages.verify_email.logout') }}
                </span>
            </button>
        </form>
    </div>
@endsection
