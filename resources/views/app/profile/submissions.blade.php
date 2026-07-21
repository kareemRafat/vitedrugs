@extends('app.layouts.master')

@section('title', __('messages.profile.submissions_heading'))

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
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <h2 class="text-lg font-bold text-heading dark:text-white mb-1">{{ __('messages.profile.submissions_heading') }}</h2>
                        <p class="text-sm text-body dark:text-slate-400">{{ __('messages.profile.submissions_subtitle') }}</p>
                    </div>

                    @if (session('success'))
                        <div class="mx-6 mt-6">
                            <div class="flex items-center p-4 text-sm rounded-base bg-success-soft border border-success-subtle text-fg-success-strong dark:bg-green-900/30 dark:border-green-700 dark:text-green-400" role="alert">
                                <x-lucide-circle-check class="shrink-0 inline w-4 h-4 me-3" />
                                <div>{{ session('success') }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($products->isEmpty())
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                            <div class="w-16 h-16 rounded-full bg-neutral-secondary-soft dark:bg-slate-700 flex items-center justify-center mb-4">
                                <x-lucide-package class="w-8 h-8 text-body dark:text-slate-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.profile.submissions_empty') }}</h3>
                            <p class="text-sm text-body dark:text-slate-400 mb-6 max-w-sm">{{ __('messages.profile.submissions_empty_desc') }}</p>
                            <a href="{{ route('products.submission.create') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-base bg-brand text-white hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs transition-colors duration-200 dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-300">
                                <x-lucide-plus class="w-4 h-4" />
                                {{ __('messages.profile.submit_product') }}
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-body uppercase tracking-wider bg-neutral-secondary-soft dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">{{ __('messages.profile.submissions_product') }}</th>
                                        <th scope="col" class="px-6 py-3">{{ __('messages.profile.submissions_status') }}</th>
                                        <th scope="col" class="px-6 py-3 hidden sm:table-cell">{{ __('messages.profile.submissions_submitted') }}</th>
                                        <th scope="col" class="px-6 py-3 hidden md:table-cell">{{ __('messages.profile.submissions_reviewed') }}</th>
                                        <th scope="col" class="px-6 py-3"><span class="sr-only">{{ __('messages.profile.submissions_action') }}</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-default-medium dark:divide-slate-700">
                                    @foreach ($products as $product)
                                        <tr class="hover:bg-neutral-secondary-soft dark:hover:bg-slate-700/30 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-heading dark:text-white">{{ $product->trade_name }}</div>
                                                @if ($product->trade_name_ar)
                                                    <div class="text-xs text-body dark:text-slate-400 mt-0.5" dir="rtl">{{ $product->trade_name_ar }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusKey = $product->status->value;
                                                    $statusClass = match ($statusKey) {
                                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold leading-none rounded-full whitespace-nowrap {{ $statusClass }}">
                                                    @if ($statusKey === 'pending')
                                                        <x-lucide-clock class="shrink-0 w-3.5 h-3.5" />
                                                    @elseif ($statusKey === 'approved')
                                                        <x-lucide-check-circle class="shrink-0 w-3.5 h-3.5" />
                                                    @elseif ($statusKey === 'rejected')
                                                        <x-lucide-x-circle class="shrink-0 w-3.5 h-3.5" />
                                                    @endif
                                                    {{ __('messages.profile.status_' . $statusKey) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 hidden sm:table-cell">
                                                <span class="text-sm text-body dark:text-slate-400">{{ $product->created_at->format('M j, Y') }}</span>
                                            </td>
                                            <td class="px-6 py-4 hidden md:table-cell">
                                                @if ($product->reviewed_at)
                                                    <span class="text-sm text-body dark:text-slate-400">{{ $product->reviewed_at->format('M j, Y') }}</span>
                                                @else
                                                    <span class="text-sm text-body/50 dark:text-slate-600">&mdash;</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-end">
                                                <a href="{{ route('products.show', $product) }}" wire:navigate
                                                   class="text-fg-brand hover:underline text-sm font-medium">
                                                    {{ __('messages.profile.submissions_view') }}
                                                </a>
                                            </td>
                                        </tr>
                                        @if ($product->admin_notes)
                                            <tr class="hover:bg-neutral-secondary-soft dark:hover:bg-slate-700/30 transition-colors duration-150">
                                                <td colspan="5" class="px-6 py-2 pb-4">
                                                    <div class="text-xs text-body dark:text-slate-400 italic">
                                                        <span class="font-medium not-italic">{{ __('messages.profile.admin_notes') }}:</span>
                                                        {{ $product->admin_notes }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4 border-t border-default-medium dark:border-slate-700">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </main>
        </div>
    </div>
@endsection
