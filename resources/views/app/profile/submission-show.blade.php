@extends('app.layouts.master')

@section('title', $product->trade_name . ' | ' . __('messages.profile.submissions_heading'))

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
                <div class="bg-neutral-primary-soft rounded-base shadow-xs border border-default-medium dark:bg-slate-800 dark:border-slate-700 overflow-hidden">

                    {{-- Header --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-heading dark:text-white mb-1">{{ $product->trade_name }}</h2>
                                @if ($product->trade_name_ar)
                                    <p class="text-sm text-body dark:text-slate-400" dir="rtl">{{ $product->trade_name_ar }}</p>
                                @endif
                            </div>
                            @php
                                $statusKey = $product->status->value;
                                $statusClass = match ($statusKey) {
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
                                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold leading-none rounded-full whitespace-nowrap shrink-0 {{ $statusClass }}">
                                @if ($statusKey === 'pending')
                                    <x-lucide-clock class="shrink-0 w-3.5 h-3.5" />
                                @elseif ($statusKey === 'approved')
                                    <x-lucide-check-circle class="shrink-0 w-3.5 h-3.5" />
                                @elseif ($statusKey === 'rejected')
                                    <x-lucide-x-circle class="shrink-0 w-3.5 h-3.5" />
                                @endif
                                {{ __('messages.profile.status_' . $statusKey) }}
                            </span>
                        </div>
                    </div>

                    {{-- Admin Notes --}}
                    @if ($product->admin_notes)
                        <div class="mx-6 mt-6">
                            <div class="flex items-start gap-3 p-4 text-sm rounded-base {{ $product->isRejected() ? 'bg-danger-soft border border-danger-subtle text-fg-danger-strong dark:bg-red-900/30 dark:border-red-700 dark:text-red-400' : 'bg-yellow-50 border border-yellow-200 text-yellow-800 dark:bg-yellow-900/30 dark:border-yellow-700 dark:text-yellow-300' }}" role="alert">
                                <x-lucide-message-square-text class="shrink-0 inline w-5 h-5 mt-0.5" />
                                <div>
                                    <p class="font-medium mb-0.5">{{ __('messages.profile.admin_notes') }}</p>
                                    <p>{{ $product->admin_notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Submitted info --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-info class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.profile.submission_detail_info') }}</h3>
                        </div>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.profile.submissions_product') }}</dt>
                                <dd class="text-sm font-medium text-heading dark:text-white mt-1">{{ $product->trade_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.profile.submissions_submitted') }}</dt>
                                <dd class="text-sm font-medium text-heading dark:text-white mt-1">{{ $product->created_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.profile.submissions_reviewed') }}</dt>
                                <dd class="text-sm font-medium text-heading dark:text-white mt-1">
                                    @if ($product->reviewed_at)
                                        {{ $product->reviewed_at->format('M j, Y g:i A') }}
                                    @else
                                        <span class="text-body/50 dark:text-slate-600">&mdash;</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Basic Information --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-package class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.profile.submission_detail_basic') }}</h3>
                        </div>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                            <div>
                                <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.products.trade_name') }}</dt>
                                <dd class="text-sm font-medium text-heading dark:text-white mt-1">{{ $product->trade_name }}</dd>
                            </div>
                            @if ($product->trade_name_ar)
                                <div>
                                    <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.products.trade_name') }} (AR)</dt>
                                    <dd class="text-sm font-medium text-heading dark:text-white mt-1" dir="rtl">{{ $product->trade_name_ar }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.products.product_type') }}</dt>
                                <dd class="text-sm font-medium text-heading dark:text-white mt-1">{{ $product->product_type ? __('messages.products.types.' . $product->product_type) : '&mdash;' }}</dd>
                            </div>
                            @if ($product->dosageForm)
                                <div>
                                    <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.products.dosage_form') }}</dt>
                                    <dd class="text-sm font-medium text-heading dark:text-white mt-1">{{ $product->dosageForm->name }}</dd>
                                </div>
                            @endif
                            @if ($product->package_size)
                                <div>
                                    <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.products.package') }}</dt>
                                    <dd class="text-sm font-medium text-heading dark:text-white mt-1">{{ $product->package_size }}</dd>
                                </div>
                            @endif
                            @if ($product->storage_conditions)
                                <div>
                                    <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider">{{ __('messages.products.storage_conditions') }}</dt>
                                    <dd class="text-sm font-medium text-heading dark:text-white mt-1">{{ $product->storage_conditions }}</dd>
                                </div>
                            @endif
                        </dl>
                        @if ($product->description)
                            <div class="mt-6 pt-6 border-t border-default-medium dark:border-slate-700">
                                <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('messages.products.description') }}</dt>
                                <dd class="text-sm text-heading dark:text-white leading-relaxed">{{ $product->description }}</dd>
                            </div>
                        @endif
                        @if ($product->description_ar)
                            <div class="mt-6 pt-6 border-t border-default-medium dark:border-slate-700">
                                <dt class="text-xs text-body dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('messages.products.description') }} (AR)</dt>
                                <dd class="text-sm text-heading dark:text-white leading-relaxed" dir="rtl">{{ $product->description_ar }}</dd>
                            </div>
                        @endif
                    </div>

                    {{-- Company --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-building-2 class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.products.companies') }}</h3>
                        </div>
                        @if ($product->companies->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_companies') }}</p>
                        @else
                            <ul class="space-y-2">
                                @foreach ($product->companies as $company)
                                    <li class="flex items-center gap-3 text-sm">
                                        <span class="font-medium text-heading dark:text-white">{{ $company->name }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-base bg-neutral-secondary-soft text-body dark:bg-slate-700 dark:text-slate-400">
                                            {{ __('messages.companies.types.' . $company->pivot->role) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Active Ingredients --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-pill class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.products.active_ingredients') }}</h3>
                        </div>
                        @if ($product->activeIngredients->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_ingredients') }}</p>
                        @else
                            <ul class="space-y-3">
                                @foreach ($product->activeIngredients as $ingredient)
                                    <li class="flex items-center justify-between py-2 px-3 rounded-base bg-neutral-secondary-soft border border-default-medium dark:bg-slate-700 dark:border-slate-600">
                                        <span class="text-sm font-medium text-heading dark:text-white">{{ $ingredient->name }}</span>
                                        @if ($ingredient->pivot->strength)
                                            <span class="text-sm text-body dark:text-slate-400">{{ $ingredient->pivot->strength }} {{ $ingredient->pivot->unit }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Diseases --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-activity class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.products.diseases') }}</h3>
                        </div>
                        @if ($product->diseases->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_diseases') }}</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($product->diseases as $disease)
                                    <span class="inline-flex items-center px-3 py-1 rounded-base text-sm font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ $disease->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Indications / Contraindications / Precautions / Side Effects --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Indications --}}
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <x-lucide-check-circle class="w-4 h-4 text-fg-brand" />
                                    <h3 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.indications') }}</h3>
                                </div>
                                @if ($product->indications->isEmpty())
                                    <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_indications') }}</p>
                                @else
                                    <ul class="space-y-2">
                                        @foreach ($product->indications as $indication)
                                            <li class="text-sm text-heading dark:text-white flex items-start gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-fg-brand mt-1.5 shrink-0"></span>
                                                <span>{{ $indication->description }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            {{-- Contraindications --}}
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <x-lucide-ban class="w-4 h-4 text-red-500 dark:text-red-400" />
                                    <h3 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.contraindications') }}</h3>
                                </div>
                                @if ($product->contraindications->isEmpty())
                                    <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_contraindications') }}</p>
                                @else
                                    <ul class="space-y-2">
                                        @foreach ($product->contraindications as $item)
                                            <li class="text-sm text-heading dark:text-white flex items-start gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1.5 shrink-0"></span>
                                                <span>{{ $item->description }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            {{-- Precautions --}}
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <x-lucide-alert-triangle class="w-4 h-4 text-amber-500 dark:text-amber-400" />
                                    <h3 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.precautions') }}</h3>
                                </div>
                                @if ($product->precautions->isEmpty())
                                    <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_precautions') }}</p>
                                @else
                                    <ul class="space-y-2">
                                        @foreach ($product->precautions as $item)
                                            <li class="text-sm text-heading dark:text-white flex items-start gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0"></span>
                                                <span>{{ $item->description }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            {{-- Side Effects --}}
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <x-lucide-alert-circle class="w-4 h-4 text-red-500 dark:text-red-400" />
                                    <h3 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.side_effects') }}</h3>
                                </div>
                                @if ($product->sideEffects->isEmpty())
                                    <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_side_effects') }}</p>
                                @else
                                    <ul class="space-y-2">
                                        @foreach ($product->sideEffects as $item)
                                            <li class="text-sm text-heading dark:text-white flex items-start gap-2">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1.5 shrink-0"></span>
                                                <span>{{ $item->description }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Dosages --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-syringe class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.products.dosages') }}</h3>
                        </div>
                        @if ($product->dosages->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_dosages') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left rtl:text-right">
                                    <thead class="text-xs uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700/50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.species') }}</th>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.dosage') }}</th>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.route') }}</th>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.duration') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-default-medium dark:divide-slate-700">
                                        @foreach ($product->dosages as $dosage)
                                            <tr class="hover:bg-neutral-secondary-soft dark:hover:bg-slate-700/30">
                                                <td class="px-4 py-3 text-sm font-medium text-heading dark:text-white">{{ $dosage->species?->name }}</td>
                                                <td class="px-4 py-3 text-sm text-heading dark:text-white">{{ $dosage->dosage }}</td>
                                                <td class="px-4 py-3 text-sm text-body dark:text-slate-400">{{ $dosage->route }}</td>
                                                <td class="px-4 py-3 text-sm text-body dark:text-slate-400">{{ $dosage->duration }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Withdrawal Periods --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-clock class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.products.withdrawal_periods') }}</h3>
                        </div>
                        @if ($product->withdrawalPeriods->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_withdrawal') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left rtl:text-right">
                                    <thead class="text-xs uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700/50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.species') }}</th>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.meat') }}</th>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.milk') }}</th>
                                            <th scope="col" class="px-4 py-3">{{ __('messages.products.eggs') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-default-medium dark:divide-slate-700">
                                        @foreach ($product->withdrawalPeriods as $item)
                                            <tr class="hover:bg-neutral-secondary-soft dark:hover:bg-slate-700/30">
                                                <td class="px-4 py-3 text-sm font-medium text-heading dark:text-white">{{ $item->species?->name }}</td>
                                                <td class="px-4 py-3 text-sm text-heading dark:text-white">{{ $item->meat_days }} {{ __('messages.products.days') }}</td>
                                                <td class="px-4 py-3 text-sm text-heading dark:text-white">{{ $item->milk_days }} {{ __('messages.products.days') }}</td>
                                                <td class="px-4 py-3 text-sm text-heading dark:text-white">{{ $item->egg_days }} {{ __('messages.products.days') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Images --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-image class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.products.images') }}</h3>
                        </div>
                        @if ($product->images->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_images') }}</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                @foreach ($product->images as $image)
                                    <a href="{{ $image->url() }}" target="_blank"
                                       class="block aspect-square rounded-base overflow-hidden border border-default-medium bg-neutral-secondary-soft hover:opacity-90 transition-opacity">
                                        <img src="{{ $image->url() }}" alt="{{ $product->trade_name }}" class="w-full h-full object-cover" loading="lazy" />
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Documents --}}
                    <div class="p-6 border-b border-default-medium dark:border-slate-700">
                        <div class="flex items-center gap-2 mb-4">
                            <x-lucide-file-text class="w-4 h-4 text-body" />
                            <h3 class="text-sm font-semibold text-heading dark:text-white uppercase tracking-wider">{{ __('messages.products.documents') }}</h3>
                        </div>
                        @if ($product->documents->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.no_documents') }}</p>
                        @else
                            <ul class="space-y-2">
                                @foreach ($product->documents as $document)
                                    <li class="flex items-center justify-between py-2 px-3 rounded-base bg-neutral-secondary-soft border border-default-medium dark:bg-slate-700 dark:border-slate-600">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <x-lucide-file class="w-5 h-5 text-body shrink-0" />
                                            <span class="text-sm font-medium text-heading dark:text-white truncate">{{ $document->title }}</span>
                                        </div>
                                        <a href="{{ $document->url() }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-sm font-medium text-fg-brand hover:underline shrink-0 ms-3">
                                            <x-lucide-download class="w-4 h-4" />
                                            {{ __('messages.products.download') }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="p-6 flex flex-wrap items-center justify-between gap-4">
                        <a href="{{ route('profile.submissions') }}" wire:navigate
                           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-base bg-neutral-primary-soft border border-default-medium text-heading hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-neutral-secondary-soft shadow-xs transition-colors duration-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:hover:bg-slate-600">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            {{ __('messages.profile.back_to_submissions') }}
                        </a>
                        @if ($product->isPending())
                            <a href="{{ route('profile.submissions.edit', $product) }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-base bg-brand text-white hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs transition-colors duration-200 dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:ring-sky-300">
                                <x-lucide-pencil class="w-4 h-4" />
                                {{ __('messages.profile.edit_submission') }}
                            </a>
                        @endif
                    </div>

                </div>
            </main>
        </div>
    </div>
@endSection
