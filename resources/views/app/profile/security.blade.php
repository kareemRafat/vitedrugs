@extends('app.layouts.master')

@section('title', __('messages.profile.security_heading'))

@section('content')
    <div class="max-w-7xl mx-auto w-full mt-6">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Sidebar --}}
            <aside class="w-full lg:w-64 shrink-0">
                <div class="bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-slate-800 dark:border-slate-700 p-3">
                    @include('app.profile.partials.sidebar')
                </div>
            </aside>

            {{-- Main content --}}
            <main class="flex-1 min-w-0">
                <div class="bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-slate-800 dark:border-slate-700 p-6">
                    <h2 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('messages.profile.security_heading') }}</h2>
                    <p class="text-sm text-body dark:text-slate-400 mb-6">{{ __('messages.profile.security_subtitle') }}</p>

                    @if (session('success'))
                        <div class="flex items-center p-4 mb-6 text-sm rounded-base bg-success-soft border border-success-subtle text-fg-success-strong dark:bg-green-900/30 dark:border-green-700 dark:text-green-400" role="alert">
                            <x-lucide-circle-check class="shrink-0 inline w-4 h-4 me-3" />
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.security.update') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block mb-2 text-sm font-medium text-heading dark:text-white">{{ __('messages.profile.current_password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <x-lucide-lock class="w-4 h-4 text-body dark:text-slate-400" />
                                </div>
                                <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('current_password') border-red-600 @enderror"
                                    placeholder="{{ __('messages.profile.current_password_placeholder') }}">
                            </div>
                            @error('current_password')
                                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                                    <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-heading dark:text-white">{{ __('messages.profile.new_password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <x-lucide-lock class="w-4 h-4 text-body dark:text-slate-400" />
                                </div>
                                <input type="password" name="password" id="password" required autocomplete="new-password"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('password') border-red-600 @enderror"
                                    placeholder="{{ __('messages.profile.new_password_placeholder') }}">
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                                    <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-heading dark:text-white">{{ __('messages.profile.confirm_password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <x-lucide-lock class="w-4 h-4 text-body dark:text-slate-400" />
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    placeholder="{{ __('messages.profile.confirm_password_placeholder') }}">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-base bg-brand text-white hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs transition-colors duration-200 dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-300">
                                <x-lucide-shield-check class="w-4 h-4" />
                                {{ __('messages.profile.password_button') }}
                            </button>
                            <a href="{{ route('profile.show') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-base bg-neutral-primary-soft border border-default-medium text-heading hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-neutral-secondary-soft shadow-xs transition-colors duration-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600">
                                {{ __('messages.profile.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
@endsection
