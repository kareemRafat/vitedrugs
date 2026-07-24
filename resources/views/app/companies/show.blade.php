@extends('app.layouts.master')

@php $locale = app()->getLocale(); @endphp

@section('title', $company->name . ' | VetPedia')

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($locale === 'ar' && $company->description_ar ? $company->description_ar : $company->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $company->name }}, veterinary company
@endsection

@section('og_title', $company->name)

@section('og_description')
    {{ \Illuminate\Support\Str::limit($locale === 'ar' && $company->description_ar ? $company->description_ar : $company->description, 160) }}
@endsection

@section('content')
<div class="space-y-4">
    {{-- Breadcrumb --}}
    <nav class="flex mb-4 pt-4" aria-label="Breadcrumb">
        <ol class="flex items-start flex-wrap gap-x-1 text-base text-body dark:text-slate-400">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.companies.home') }}</a>
            </li>
            <li>
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <a href="{{ route('companies.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.companies.companies') }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <span class="text-heading dark:text-white font-medium">{{ $company->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Company Hero --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 p-5 sm:p-7">
            <div class="lg:col-span-7 xl:col-span-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $company->name }}</h1>
                @if ($company->name_ar && $locale === 'ar')
                    <p class="text-body dark:text-slate-400 text-base mb-2">{{ $company->name_ar }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-base text-sm font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand capitalize">
                        <x-lucide-tag class="w-3.5 h-3.5" />
                        {{ __('messages.companies.types.' . $company->company_type) }}
                    </span>
                    @if ($company->country)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-base text-sm font-medium bg-neutral-secondary-soft text-heading border border-default-medium dark:bg-slate-700 dark:text-white dark:border-slate-600">
                            <x-lucide-map-pin class="w-3.5 h-3.5" />
                            {{ $company->country }}
                        </span>
                    @endif
                </div>
                @php
                    $displayDescription = $locale === 'ar' && $company->description_ar
                        ? $company->description_ar
                        : $company->description;
                @endphp
                @if ($displayDescription)
                    <p class="text-base text-body dark:text-slate-400">{{ $displayDescription }}</p>
                @endif
            </div>

            <div class="lg:col-span-5 xl:col-span-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-package class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-400 mb-1">{{ __('messages.companies.products_count') }}</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $products->total() }}</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-file-text class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-emerald-600 dark:text-emerald-400 mb-1">{{ __('messages.companies.registration') }}</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $company->registration_number ?? __('messages.companies.na') }}</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-globe class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-amber-600 dark:text-amber-400 mb-1">{{ __('messages.companies.country') }}</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $company->country ?? __('messages.companies.na') }}</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-building class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-rose-600 dark:text-rose-400 mb-1">{{ __('messages.companies.type') }}</p>
                            <p class="text-2xl font-bold text-slate-900 dark:text-white capitalize">{{ __('messages.companies.types.' . $company->company_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Main Column --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            {{-- Products --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-package class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.products_title') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    @if ($products->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-8">{{ __('messages.companies.no_products') }}</p>
                    @else
                        <table class="w-full text-base text-left rtl:text-right text-heading dark:text-white">
                            <thead class="text-base uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700 dark:text-slate-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3">{{ __('messages.companies.trade_name') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.companies.dosage_form') }}</th>
                                    <th scope="col" class="px-5 py-3 w-20">{{ __('messages.companies.details') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr class="border-b border-default-medium dark:border-slate-700">
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" wire:navigate class="font-medium text-fg-brand hover:underline">
                                                {{ $product->trade_name }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell text-body dark:text-slate-400 text-base">
                                            {{ $product->dosageForm?->name ?? __('messages.companies.na') }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" wire:navigate
                                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm">
                                                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <x-pagination
                    :paginator="$products"
                    translation-prefix="messages.companies"
                    border-top
                />
            </div>

            {{-- Parent Company --}}
            @if ($company->parentCompany)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-building class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.parent_company') }}</h2>
                    </div>
                    <div class="p-5">
                        <a href="{{ route('companies.show', $company->parentCompany) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">
                            {{ $company->parentCompany->name }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Subsidiaries --}}
            @if ($company->subsidiaries->isNotEmpty())
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-building class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.subsidiaries') }}</h2>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2">
                            @foreach ($company->subsidiaries as $subsidiary)
                                <li>
                                    <a href="{{ route('companies.show', $subsidiary) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">
                                        {{ $subsidiary->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Contact --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-phone class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.contact_info') }}</h2>
                </div>
                <div class="p-5 space-y-3">
                    @php
                        $contactFields = [
                            'phone' => ['icon' => 'phone', 'label' => __('messages.companies.phone')],
                            'mobile' => ['icon' => 'smartphone', 'label' => __('messages.companies.mobile')],
                            'whatsapp' => ['icon' => 'message-circle', 'label' => __('messages.companies.whatsapp')],
                            'email' => ['icon' => 'mail', 'label' => __('messages.companies.email')],
                            'website' => ['icon' => 'globe', 'label' => __('messages.companies.website')],
                            'contact_person' => ['icon' => 'user', 'label' => __('messages.companies.contact_person')],
                            'google_maps_url' => ['icon' => 'navigation', 'label' => __('messages.companies.google_maps')],
                            'governorate' => ['icon' => 'map-pin', 'label' => __('messages.companies.governorate')],
                            'coverage_area' => ['icon' => 'compass', 'label' => __('messages.companies.coverage_area')],
                        ];
                    @endphp
                    @foreach ($contactFields as $field => $meta)
                        @if ($company->$field)
                            <div class="flex items-start gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-base bg-neutral-secondary-soft dark:bg-slate-700 shrink-0">
                                    <x-dynamic-component :component="'lucide-' . $meta['icon']" class="w-4 h-4 text-body dark:text-slate-400" />
                                </div>
                                <div class="min-w-0">
                                    <span class="block text-xs uppercase text-body dark:text-slate-400">{{ $meta['label'] }}</span>
                                    @if (in_array($field, ['email', 'website']))
                                        <a href="{{ $field === 'email' ? 'mailto:' . $company->$field : $company->$field }}"
                                           target="_blank" class="text-sm font-medium text-fg-brand hover:underline break-all">
                                            {{ $company->$field }}
                                        </a>
                                    @elseif (in_array($field, ['facebook', 'linkedin', 'telegram', 'youtube', 'instagram', 'google_maps_url']))
                                        <a href="{{ $company->$field }}"
                                           target="_blank" class="text-sm font-medium text-fg-brand hover:underline break-all">
                                            {{ $company->$field }}
                                        </a>
                                    @else
                                        <span dir="ltr" class="text-sm font-medium text-heading dark:text-white">{{ str_replace(' ', '', $company->$field) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @php
                        $displayAddress = $locale === 'ar' && $company->address_ar
                            ? $company->address_ar
                            : $company->address;
                    @endphp
                    @if ($displayAddress)
                        <div class="flex items-start gap-3 pt-3 border-t border-default-medium dark:border-slate-700">
                            <div class="flex items-center justify-center w-8 h-8 rounded-base bg-neutral-secondary-soft dark:bg-slate-700 shrink-0">
                                <x-lucide-map-pin class="w-4 h-4 text-body dark:text-slate-400" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs uppercase text-body dark:text-slate-400">{{ __('messages.companies.address') }}</span>
                                <span class="text-sm font-medium text-heading dark:text-white">{{ $displayAddress }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Social Links --}}
            @php
                $socialFields = [
                    'facebook' => 'globe',
                    'linkedin' => 'briefcase',
                    'telegram' => 'send',
                    'youtube' => 'video',
                    'instagram' => 'camera',
                ];
                $hasSocial = collect($socialFields)->keys()->first(fn($f) => $company->$f);
            @endphp
            @if ($hasSocial)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-share-2 class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.social') }}</h2>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-wrap gap-2">
                            @foreach ($socialFields as $field => $icon)
                                @if ($company->$field)
                                    <a href="{{ $company->$field }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-base bg-neutral-secondary-soft text-heading border border-default-medium hover:bg-brand-soft hover:text-fg-brand hover:border-brand-medium dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-brand/20 dark:hover:text-brand transition-colors">
                                        <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
                                        <span class="capitalize">{{ $field }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
