@extends('app.layouts.guest')

@section('title', __('messages.register.title'))

@section('content')
    <div class="w-full max-w-md p-6 bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 sm:p-8">
        <h2 class="mb-2 text-2xl font-bold leading-tight tracking-tight text-heading dark:text-white">
            {{ __('messages.register.heading') }}
        </h2>
        <p class="mb-6 text-sm font-light text-body dark:text-slate-400">
            {{ __('messages.register.subtitle') }}
        </p>

        @if ($errors->any())
            <div class="flex items-center p-4 mb-4 text-sm rounded-base bg-danger-soft border border-danger-subtle text-fg-danger-strong dark:bg-slate-700 dark:text-red-400 dark:border-red-600" role="alert">
                <x-lucide-circle-alert class="shrink-0 inline w-4 h-4 me-3" />
                <span class="sr-only">{{ __('messages.common.error') }}</span>
                <div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4 md:space-y-5">
            @csrf

            <div>
                <label for="name" class="block mb-2.5 text-sm font-medium text-heading dark:text-white">{{ __('messages.register.name_label') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-user class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="text" name="name" id="name"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand @error('name') border-danger-subtle dark:border-red-600 @enderror"
                        placeholder="{{ __('messages.register.name_placeholder') }}"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name">
                </div>
            </div>

            <div>
                <label for="email" class="block mb-2.5 text-sm font-medium text-heading dark:text-white">{{ __('messages.register.email_label') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-mail class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="email" name="email" id="email"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand @error('email') border-danger-subtle dark:border-red-600 @enderror"
                        placeholder="{{ __('messages.register.email_placeholder') }}"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username">
                </div>
            </div>

            <div>
                <label for="password" class="block mb-2.5 text-sm font-medium text-heading dark:text-white">{{ __('messages.register.password_label') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-lock class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="password" name="password" id="password"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand @error('password') border-danger-subtle dark:border-red-600 @enderror"
                        placeholder="{{ __('messages.register.password_placeholder') }}"
                        required
                        autocomplete="new-password">
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block mb-2.5 text-sm font-medium text-heading dark:text-white">{{ __('messages.register.confirm_label') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-lock class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:focus:ring-brand dark:focus:border-brand @error('password_confirmation') border-danger-subtle dark:border-red-600 @enderror"
                        placeholder="{{ __('messages.register.confirm_placeholder') }}"
                        required
                        autocomplete="new-password">
                </div>
            </div>

            <button type="submit"
                class="w-full flex items-center justify-center text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-300">
                {{ __('messages.register.submit') }}
            </button>

            <p class="text-sm font-light text-body dark:text-slate-400">
                {{ __('messages.register.has_account') }}
                <a href="{{ route('login') }}" class="font-medium text-fg-brand hover:underline dark:text-brand">{{ __('messages.register.login_link') }}</a>
            </p>
        </form>
    </div>
@endsection
