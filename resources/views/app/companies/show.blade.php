@extends('app.layouts.master')

@section('title', $company->name . ' | VetPedia')

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($company->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $company->name }}, veterinary company
@endsection

@section('og_title', $company->name)

@section('og_description')
    {{ \Illuminate\Support\Str::limit($company->description, 160) }}
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 rtl:space-x-reverse text-base text-body dark:text-slate-400">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.companies.home') }}</a>
            </li>
            <li>
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <a href="{{ route('companies.index') }}" class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.companies.companies') }}</a>
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
    <div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 mb-4 dark:bg-slate-800">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7 xl:col-span-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $company->name }}</h1>
                @if ($company->name_ar && app()->getLocale() === 'ar')
                    <p class="text-body dark:text-slate-400 text-base mb-2">{{ $company->name_ar }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-base text-sm font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand capitalize">
                        {{ __('messages.companies.types.' . $company->company_type) }}
                    </span>
                    @if ($company->country)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-base text-sm font-medium bg-neutral-secondary-soft text-heading border border-default-medium dark:bg-slate-700 dark:text-white dark:border-slate-600">
                            <x-lucide-map-pin class="w-3.5 h-3.5 me-1" />
                            {{ $company->country }}
                        </span>
                    @endif
                </div>
                @if ($company->description)
                    <p class="text-base text-body dark:text-slate-400">{{ $company->description }}</p>
                @endif
            </div>

            <div class="lg:col-span-5 xl:col-span-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.type') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base capitalize">{{ __('messages.companies.types.' . $company->company_type) }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.country') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $company->country ?? __('messages.companies.na') }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.products_count') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $company->products->count() }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.companies.registration') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $company->registration_number ?? __('messages.companies.na') }}</span>
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
                    @if ($company->products->isEmpty())
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
                                @foreach ($company->products as $product)
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
            </div>

            {{-- Parent Company --}}
            @if ($company->parentCompany)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-building class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.companies.parent_company') }}</h2>
                    </div>
                    <div class="p-5">
                        <a href="{{ route('companies.show', $company->parentCompany) }}" class="text-base font-medium text-fg-brand hover:underline">
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
                                    <a href="{{ route('companies.show', $subsidiary) }}" class="text-base font-medium text-fg-brand hover:underline">
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

            {{-- Contact Information --}}
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
                            'governorate' => ['icon' => 'map-pin', 'label' => __('messages.companies.governorate')],
                            'coverage_area' => ['icon' => 'compass', 'label' => __('messages.companies.coverage_area')],
                        ];
                    @endphp
                    @foreach ($contactFields as $field => $meta)
                        @if ($company->$field)
                            <div class="flex items-start gap-3">
                                <x-dynamic-component :component="'lucide-' . $meta['icon']" class="w-4 h-4 text-body mt-0.5 shrink-0" />
                                <div class="min-w-0">
                                    <span class="block text-sm uppercase text-body dark:text-slate-400">{{ $meta['label'] }}</span>
                                    @if (in_array($field, ['email', 'website']))
                                        <a href="{{ $field === 'email' ? 'mailto:' . $company->$field : $company->$field }}"
                                           target="_blank" class="text-base font-medium text-fg-brand hover:underline break-all">
                                            {{ $company->$field }}
                                        </a>
                                    @else
                                        <span class="text-base font-medium text-heading dark:text-white">{{ $company->$field }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if ($company->address)
                        <div class="flex items-start gap-3 pt-3 border-t border-default-medium">
                            <x-lucide-map-pin class="w-4 h-4 text-body mt-0.5 shrink-0" />
                            <div class="min-w-0">
                                <span class="block text-sm uppercase text-body dark:text-slate-400">{{ __('messages.companies.address') }}</span>
                                <span class="text-base font-medium text-heading dark:text-white">{{ $company->address }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection
