@extends('app.layouts.master')

@section('title', $disease->name . ' | VetPedia')

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($disease->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $disease->name }}, veterinary disease
@endsection

@section('og_title', $disease->name)

@section('og_description')
    {{ \Illuminate\Support\Str::limit($disease->description, 160) }}
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 rtl:space-x-reverse text-base text-body dark:text-slate-400">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.diseases.home') }}</a>
            </li>
            <li>
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <a href="{{ route('diseases.index') }}" class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.diseases.diseases') }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <span class="text-heading dark:text-white font-medium">{{ $disease->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Main Column --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            {{-- Disease Overview --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 dark:bg-slate-800">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $disease->name }}</h1>
                @if ($disease->name_ar && app()->getLocale() === 'ar')
                    <p class="text-body dark:text-slate-400 text-base mb-2">{{ $disease->name_ar }}</p>
                @endif
                @if ($disease->description)
                    <p class="text-base text-body dark:text-slate-400">{{ $disease->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.diseases.related_products') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $disease->products->count() }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-slate-700 dark:border-slate-600">
                        <span class="block text-base uppercase text-body dark:text-slate-400 mb-1">{{ __('messages.diseases.ingredients_count') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-base">{{ $ingredients->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Related Products --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-package class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.products_title') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    @if ($disease->products->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-8">{{ __('messages.diseases.no_products') }}</p>
                    @else
                        <table class="w-full text-base text-left rtl:text-right text-heading dark:text-white">
                            <thead class="text-base uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700 dark:text-slate-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3">{{ __('messages.diseases.trade_name') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.diseases.dosage_form') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.diseases.manufacturer') }}</th>
                                    <th scope="col" class="px-5 py-3 w-20">{{ __('messages.diseases.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($disease->products as $product)
                                    <tr class="border-b border-default-medium dark:border-slate-700">
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" class="font-medium text-fg-brand hover:underline">
                                                {{ $product->trade_name }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell text-body dark:text-slate-400 text-base">
                                            {{ $product->dosageForm?->name ?? __('messages.diseases.na') }}
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell">
                                            @php
                                                $manufacturer = $product->companies->first(fn($c) => $c->pivot?->role === 'manufacturer');
                                            @endphp
                                            @if ($manufacturer)
                                                <a href="{{ route('companies.show', $manufacturer) }}" class="font-medium text-fg-brand hover:underline">
                                                    {{ $manufacturer->name }}
                                                </a>
                                            @else
                                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.na') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}"
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

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Active Ingredients --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-pill class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.active_ingredients') }}</h2>
                </div>
                <div class="p-5">
                    @if ($ingredients->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.diseases.no_active_ingredients') }}</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($ingredients as $ingredient)
                                <li>
                                    <a href="{{ route('active-ingredients.show', $ingredient) }}" class="text-base font-medium text-fg-brand hover:underline">
                                        {{ $ingredient->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection
