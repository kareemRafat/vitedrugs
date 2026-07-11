@extends('app.layouts.master')

@section('title', $product->trade_name . ' | VetPedia')

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $product->trade_name }}, veterinary drug, {{ $product->dosageForm?->name }}
@endsection

@section('og_title', $product->trade_name)

@section('og_description')
    {{ \Illuminate\Support\Str::limit($product->description, 160) }}
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 rtl:space-x-reverse text-sm text-body dark:text-gray-400">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.products.home') }}</a>
            </li>
            <li>
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <a href="{{ route('products.index') }}" class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.products.products') }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <span class="text-heading dark:text-white font-medium">{{ $product->trade_name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Product Hero --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 mb-4 dark:bg-gray-800">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7 xl:col-span-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $product->trade_name }}</h1>
                @if ($product->trade_name_ar)
                    <p class="text-body dark:text-gray-400 text-sm mb-2">{{ $product->trade_name_ar }}</p>
                @endif
                <div class="flex flex-wrap gap-2 mb-3">
                    @if ($product->product_type)
                        <span class="inline-flex items-center px-3 py-1 rounded-base text-base font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('messages.products.types.' . $product->product_type) }}</span>
                    @endif
                    @if ($product->dosageForm?->name)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-base text-sm font-medium bg-neutral-secondary-soft text-heading border border-default-medium dark:bg-gray-700 dark:text-white dark:border-gray-600">{{ $product->dosageForm->name }}</span>
                    @endif
                </div>
                <p class="text-sm text-body dark:text-gray-400">{{ $product->description ?? __('messages.products.no_description') }}</p>
            </div>

            <div class="lg:col-span-5 xl:col-span-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <span class="block text-sm uppercase text-body dark:text-gray-400 mb-1">{{ __('messages.products.dosage_form') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-sm">{{ $product->dosageForm?->name ?? __('messages.products.na') }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <span class="block text-sm uppercase text-body dark:text-gray-400 mb-1">{{ __('messages.products.product_type') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-sm">{{ $product->product_type ? __('messages.products.types.' . $product->product_type) : __('messages.products.na') }}</span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <span class="block text-sm uppercase text-body dark:text-gray-400 mb-1">{{ __('messages.products.manufacturer') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-sm">
                            @if ($manufacturer = $product->companies->first(fn($company) => $company->pivot?->role === 'manufacturer'))
                                <a href="{{ route('companies.show', $manufacturer) }}" class="text-fg-brand hover:underline">{{ $manufacturer->name }}</a>
                            @else
                                {{ __('messages.products.na') }}
                            @endif
                        </span>
                    </div>
                    <div class="bg-neutral-secondary-soft rounded-base p-3 border border-default-medium shadow-sm dark:bg-gray-700 dark:border-gray-600">
                        <span class="block text-sm uppercase text-body dark:text-gray-400 mb-1">{{ __('messages.products.package') }}</span>
                        <span class="font-semibold text-heading dark:text-white text-sm">{{ $product->package_size ?? __('messages.products.na') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Main Column --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            {{-- Dosages --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-gray-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-syringe class="w-4 h-4 text-body" />
                    <h2 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.dosages') }}</h2>
                </div>
                <div class="p-5">
                    @if ($product->dosages->isEmpty())
                        <p class="text-sm text-body dark:text-gray-400 text-center py-4">{{ __('messages.products.no_dosages') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-heading dark:text-white">
                                <thead class="text-sm uppercase text-body bg-neutral-secondary-soft dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.species') }}</th>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.dosage') }}</th>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.route') }}</th>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.duration') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->dosages as $dosage)
                                        <tr class="border-b border-default-medium dark:border-gray-700">
                                            <td class="px-4 py-3">{{ $dosage->species?->name }}</td>
                                            <td class="px-4 py-3">{{ $dosage->dosage }}</td>
                                            <td class="px-4 py-3">{{ $dosage->route }}</td>
                                            <td class="px-4 py-3">{{ $dosage->duration }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Withdrawal Periods --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-gray-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-clock class="w-4 h-4 text-body" />
                    <h2 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.withdrawal_periods') }}</h2>
                </div>
                <div class="p-5">
                    @if ($product->withdrawalPeriods->isEmpty())
                        <p class="text-sm text-body dark:text-gray-400 text-center py-4">{{ __('messages.products.no_withdrawal') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left rtl:text-right text-heading dark:text-white">
                                <thead class="text-sm uppercase text-body bg-neutral-secondary-soft dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.species') }}</th>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.meat') }}</th>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.milk') }}</th>
                                        <th scope="col" class="px-4 py-3">{{ __('messages.products.eggs') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->withdrawalPeriods as $item)
                                        <tr class="border-b border-default-medium dark:border-gray-700">
                                            <td class="px-4 py-3">{{ $item->species?->name }}</td>
                                            <td class="px-4 py-3">{{ $item->meat_days }}</td>
                                            <td class="px-4 py-3">{{ $item->milk_days }}</td>
                                            <td class="px-4 py-3">{{ $item->egg_days }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Active Ingredients --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-pill class="w-4 h-4 text-body" />
                    <h2 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.active_ingredients') }}</h2>
                </div>
                <div class="p-5">
                    @if ($product->activeIngredients->isEmpty())
                        <p class="text-sm text-body dark:text-gray-400 text-center py-4">{{ __('messages.products.no_ingredients') }}</p>
                    @else
                        <ul class="space-y-3">
                            @foreach ($product->activeIngredients as $ingredient)
                                <li class="pb-3 border-b border-default-medium last:border-0 last:pb-0">
                                    <a href="{{ route('active-ingredients.show', $ingredient) }}" class="text-sm font-medium text-fg-brand hover:underline">{{ $ingredient->name }}</a>
                                    @if ($ingredient->pivot->strength)
                                        <p class="text-sm text-body dark:text-gray-400 mt-0.5">{{ $ingredient->pivot->strength }} {{ $ingredient->pivot->unit }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Diseases --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-activity class="w-4 h-4 text-body" />
                    <h2 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.diseases') }}</h2>
                </div>
                <div class="p-5">
                    @if ($product->diseases->isEmpty())
                        <p class="text-sm text-body dark:text-gray-400 text-center py-4">{{ __('messages.products.no_diseases') }}</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($product->diseases as $disease)
                                <li>
                                    <a href="{{ route('diseases.show', $disease) }}" class="text-sm font-medium text-fg-brand hover:underline">{{ $disease->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Related Products --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-link class="w-4 h-4 text-body" />
                    <h2 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.related_products') }}</h2>
                </div>
                <div class="p-5">
                    @if ($relatedProducts->count())
                        <ul class="space-y-2">
                            @foreach ($relatedProducts as $related)
                                <li>
                                    <a href="{{ route('products.show', $related) }}" class="text-sm font-medium text-fg-brand hover:underline">{{ $related->trade_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm font-medium text-heading dark:text-white mb-1">{{ __('messages.products.no_related') }}</p>
                            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.products.no_related_desc') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Related Products By Active Ingredient --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-flask-conical class="w-4 h-4 text-body" />
                    <h2 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.related_by_ingredient') }}</h2>
                </div>
                <div class="p-5">
                    @if ($relatedByIngredients->count())
                        <ul class="space-y-2">
                            @foreach ($relatedByIngredients as $related)
                                <li>
                                    <a href="{{ route('products.show', $related) }}" class="text-sm font-medium text-fg-brand hover:underline">{{ $related->trade_name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm font-medium text-heading dark:text-white mb-1">{{ __('messages.products.no_related') }}</p>
                            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.products.no_related_desc') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Companies --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-gray-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-building-2 class="w-4 h-4 text-body" />
                    <h2 class="text-sm font-semibold text-heading dark:text-white">{{ __('messages.products.companies') }}</h2>
                </div>
                <div class="p-5">
                    @if ($product->companies->isEmpty())
                        <p class="text-sm text-body dark:text-gray-400 text-center py-4">{{ __('messages.products.no_companies') }}</p>
                    @else
                        <ul class="space-y-3">
                            @foreach ($product->companies as $company)
                                <li class="pb-3 border-b border-default-medium last:border-0 last:pb-0">
                                    <a href="{{ route('companies.show', $company) }}" class="text-sm font-medium text-fg-brand hover:underline">{{ $company->name }}</a>
                                    <p class="text-sm text-body dark:text-gray-400 mt-0.5 capitalize">{{ $company->pivot->role }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection
