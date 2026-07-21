@extends('app.layouts.master')

@section('title', __('messages.profile.edit_heading'))

@section('content')
    <div class="max-w-6xl mx-auto w-full mt-6">
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
                    <h2 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('messages.profile.edit_heading') }}</h2>
                    <p class="text-sm text-body dark:text-slate-400 mb-6">{{ __('messages.profile.edit_subtitle') }}</p>

                    @if (session('success'))
                        <div class="flex items-center p-4 mb-6 text-sm rounded-base bg-success-soft border border-success-subtle text-fg-success-strong dark:bg-green-900/30 dark:border-green-700 dark:text-green-400" role="alert">
                            <x-lucide-circle-check class="shrink-0 inline w-4 h-4 me-3" />
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="flex items-center p-4 mb-6 text-sm rounded-base bg-danger-soft border border-danger-subtle text-fg-danger-strong dark:bg-red-900/30 dark:border-red-700 dark:text-red-400" role="alert">
                            <x-lucide-circle-alert class="shrink-0 inline w-4 h-4 me-3" />
                            <div>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-heading dark:text-white">{{ __('messages.profile.name_label') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <x-lucide-user class="w-4 h-4 text-body dark:text-slate-400" />
                                </div>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('name') border-danger-subtle dark:border-red-600 @enderror"
                                    placeholder="{{ __('messages.profile.name_placeholder') }}">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-heading dark:text-white">{{ __('messages.profile.email_label') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <x-lucide-mail class="w-4 h-4 text-body dark:text-slate-400" />
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('email') border-danger-subtle dark:border-red-600 @enderror"
                                    placeholder="{{ __('messages.profile.email_placeholder') }}">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-base bg-brand text-white hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs transition-colors duration-200 dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-300">
                                <x-lucide-save class="w-4 h-4" />
                                {{ __('messages.profile.update_button') }}
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
