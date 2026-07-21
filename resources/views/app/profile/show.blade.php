@extends('app.layouts.master')

@section('title', __('messages.profile.title'))

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
                <div class="bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-slate-800 dark:border-slate-700 overflow-hidden">
                    {{-- Header --}}
                    <div class="relative h-32 bg-gradient-to-r from-brand to-brand-strong dark:from-sky-700 dark:to-sky-900">
                        <div class="absolute -bottom-10 start-6">
                            <div class="w-20 h-20 rounded-full bg-white dark:bg-slate-800 border-4 border-white dark:border-slate-800 flex items-center justify-center shadow-xs">
                                <span class="text-2xl font-bold text-brand dark:text-sky-400">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-14 px-6 pb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <div>
                                <h1 class="text-xl font-bold text-heading dark:text-white">{{ $user->name }}</h1>
                                <p class="text-sm text-body dark:text-slate-400 mt-0.5">{{ $user->email }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($user->hasVerifiedEmail())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-base bg-success-soft text-fg-success-strong dark:bg-green-900/30 dark:text-green-400">
                                        <x-lucide-badge-check class="w-3.5 h-3.5" />
                                        {{ __('messages.profile.email_verified') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-base bg-danger-soft text-fg-danger-strong dark:bg-red-900/30 dark:text-red-400">
                                        <x-lucide-circle-alert class="w-3.5 h-3.5" />
                                        {{ __('messages.profile.email_not_verified') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-default-medium dark:border-slate-700 pt-5">
                            <dl class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <dt class="w-32 text-sm text-body dark:text-slate-400 shrink-0">{{ __('messages.profile.member_since') }}</dt>
                                    <dd class="text-sm font-medium text-heading dark:text-white">{{ $user->created_at->format('F j, Y') }}</dd>
                                </div>
                                <div class="flex items-center gap-3">
                                    <dt class="w-32 text-sm text-body dark:text-slate-400 shrink-0">{{ __('messages.profile.email_label') }}</dt>
                                    <dd class="text-sm font-medium text-heading dark:text-white">{{ $user->email }}</dd>
                                </div>
                                <div class="flex items-center gap-3">
                                    <dt class="w-32 text-sm text-body dark:text-slate-400 shrink-0">{{ __('messages.profile.name_label') }}</dt>
                                    <dd class="text-sm font-medium text-heading dark:text-white">{{ $user->name }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3 pt-5 border-t border-default-medium dark:border-slate-700">
                            <a href="{{ route('profile.edit') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-base bg-brand text-white hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs transition-colors duration-200 dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-300">
                                <x-lucide-pencil class="w-4 h-4" />
                                {{ __('messages.profile.edit_button') }}
                            </a>
                            <a href="{{ route('profile.security') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-base bg-neutral-primary-soft border border-default-medium text-heading hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-neutral-secondary-soft shadow-xs transition-colors duration-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600">
                                <x-lucide-shield class="w-4 h-4" />
                                {{ __('messages.profile.nav_security') }}
                            </a>
                            <a href="{{ route('profile.submissions') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-base bg-neutral-primary-soft border border-default-medium text-heading hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-neutral-secondary-soft shadow-xs transition-colors duration-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600">
                                <x-lucide-package class="w-4 h-4" />
                                {{ __('messages.profile.nav_submissions') }}
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection
