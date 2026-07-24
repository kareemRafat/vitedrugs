@extends('app.layouts.master')

@section('title', $activeIngredient->name . ' | VetPedia')

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($activeIngredient->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $activeIngredient->name }},
    active ingredient,
    veterinary pharmacology
@endsection

@section('og_title', $activeIngredient->name)

@section('og_description')
    {{ \Illuminate\Support\Str::limit($activeIngredient->description, 160) }}
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <nav class="flex mb-4 pt-4" aria-label="Breadcrumb">
        <ol class="flex items-start flex-wrap gap-x-1 text-base text-body dark:text-slate-400">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.active_ingredients.home') }}</a>
            </li>
            <li>
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <a href="{{ route('active-ingredients.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.active_ingredients.ingredients') }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <span class="text-heading dark:text-white font-medium">{{ $activeIngredient->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Main Column --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            {{-- Overview --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 dark:bg-slate-800">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $activeIngredient->name }}</h1>
                @if ($activeIngredient->name_ar && app()->getLocale() === 'ar')
                    <p class="text-body dark:text-slate-400 text-base mb-2">{{ $activeIngredient->name_ar }}</p>
                @endif
                @if ($activeIngredient->description)
                    <p class="text-base text-body dark:text-slate-400">{{ $activeIngredient->description }}</p>
                @else
                    <p class="text-base text-body dark:text-slate-400">{{ __('messages.active_ingredients.no_description') }}</p>
                @endif

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-package class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-400 mb-1">{{ __('messages.active_ingredients.products_using') }}</p>
                            <p class="text-base font-bold text-slate-900 dark:text-white">{{ $activeIngredient->products->count() }}</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-flask-conical class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-amber-600 dark:text-amber-400 mb-1">{{ __('messages.active_ingredients.drug_classes') }}</p>
                            <p class="text-base font-bold text-slate-900 dark:text-white">{{ $activeIngredient->drugClasses->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Indications --}}
            @if ($activeIngredient->indications)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium">
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.indications') }}</h2>
                    </div>
                    <div class="p-5 text-base text-body dark:text-slate-400">
                        {!! nl2br(e($activeIngredient->indications)) !!}
                    </div>
                </div>
            @endif

            {{-- Contraindications --}}
            @if ($activeIngredient->contraindications)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium">
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.contraindications') }}</h2>
                    </div>
                    <div class="p-5 text-base text-body dark:text-slate-400">
                        {!! nl2br(e($activeIngredient->contraindications)) !!}
                    </div>
                </div>
            @endif

            {{-- Precautions --}}
            @if ($activeIngredient->precautions)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium">
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.precautions') }}</h2>
                    </div>
                    <div class="p-5 text-base text-body dark:text-slate-400">
                        {!! nl2br(e($activeIngredient->precautions)) !!}
                    </div>
                </div>
            @endif

            {{-- Side Effects --}}
            @if ($activeIngredient->side_effects)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium">
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.side_effects') }}</h2>
                    </div>
                    <div class="p-5 text-base text-body dark:text-slate-400">
                        {!! nl2br(e($activeIngredient->side_effects)) !!}
                    </div>
                </div>
            @endif

            {{-- Related Products --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-package class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.related_products') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    @if ($activeIngredient->products->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-8">{{ __('messages.active_ingredients.no_products') }}</p>
                    @else
                        <table class="w-full text-base text-left rtl:text-right text-heading dark:text-white">
                            <thead class="text-base uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700 dark:text-slate-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3">{{ __('messages.active_ingredients.trade_name') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.active_ingredients.strength') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.active_ingredients.company') }}</th>
                                    <th scope="col" class="px-5 py-3 w-20">{{ __('messages.active_ingredients.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeIngredient->products as $product)
                                    <tr class="border-b border-default-medium dark:border-slate-700">
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" wire:navigate class="font-medium text-fg-brand hover:underline">
                                                {{ $product->trade_name }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell text-body dark:text-slate-400 text-base">
                                            {{ $product->pivot->strength }} {{ $product->pivot->unit }}
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell">
                                            @php
                                                $manufacturer = $product->manufacturer()->first();
                                            @endphp
                                            @if ($manufacturer)
                                                <a href="{{ route('companies.show', $manufacturer) }}" wire:navigate class="font-medium text-fg-brand hover:underline">
                                                    {{ $manufacturer->name }}
                                                </a>
                                            @else
                                                <span class="text-body dark:text-slate-400">{{ __('messages.active_ingredients.na') }}</span>
                                            @endif
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

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Drug Classes --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-flask-conical class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.drug_classes') }}</h2>
                </div>
                <div class="p-5">
                    @if ($activeIngredient->drugClasses->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach ($activeIngredient->drugClasses as $class)
                                <li class="text-base font-medium text-heading dark:text-white">{{ $class->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-base text-body dark:text-slate-400">{{ __('messages.active_ingredients.no_drug_classes') }}</p>
                    @endif
                </div>
            </div>

            {{-- Related Diseases --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-activity class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.active_ingredients.related_diseases') }}</h2>
                </div>
                <div class="p-5">
                    @if ($diseases->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach ($diseases as $disease)
                                <li>
                                    <a href="{{ route('diseases.show', $disease) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">
                                        {{ $disease->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-base text-body dark:text-slate-400">{{ __('messages.active_ingredients.no_diseases') }}</p>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection
