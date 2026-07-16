@extends('app.layouts.master')

@section('title', __('messages.products.index_title'))

@section('content')
    <div class="space-y-4">

        <x-page-hero
            :heading="__('messages.products.index_heading')"
            :subtitle="__('messages.products.index_subtitle')"
            :stats="[
                ['count' => number_format($products->total()), 'label' => __('messages.products.products_label'), 'icon' => 'package'],
                ['count' => $companies->count(), 'label' => __('messages.products.companies_label'), 'icon' => 'building-2'],
                ['count' => $dosageForms->count(), 'label' => __('messages.products.forms_label'), 'icon' => 'pill'],
            ]"
        />

        {{-- Search / Filters --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div class="lg:col-span-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-3 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            placeholder="{{ __('messages.products.search_placeholder') }}">
                    </div>

                    <select name="company"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">{{ __('messages.products.all_companies') }}</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected(request('company') == $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>

                    <select name="dosage_form"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">{{ __('messages.products.all_forms') }}</option>
                        @foreach ($dosageForms as $form)
                            <option value="{{ $form->id }}" @selected(request('dosage_form') == $form->id)>{{ $form->name }}</option>
                        @endforeach
                    </select>

                    <select name="sort"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="latest" @selected(request('sort') == 'latest')>{{ __('messages.products.sort_latest') }}</option>
                        <option value="name" @selected(request('sort') == 'name')>{{ __('messages.products.sort_name') }}</option>
                        <option value="oldest" @selected(request('sort') == 'oldest')>{{ __('messages.products.sort_oldest') }}</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            {{ __('messages.products.search_button') }}
                        </button>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                            <x-lucide-x class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Products Grid --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800">
            <div class="px-5 py-4 border-b border-default-medium flex items-center justify-between">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.products.directory_title') }}</h2>
                <span class="text-sm text-body dark:text-gray-400">
                    {{ __('messages.products.showing') }}
                    {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                    {{ __('messages.products.of') }}
                    {{ number_format($products->total()) }}
                </span>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($products as $product)
                        <div class="group bg-white dark:bg-gray-800 rounded border border-neutral-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col relative overflow-hidden">
                            {{-- Top brand accent bar --}}
                            <div class="h-1 w-full bg-brand/40 group-hover:bg-brand absolute top-0 left-0 transition-all duration-300"></div>

                            <div class="p-5 pt-6 flex flex-col flex-1">
                                {{-- Top row: product type + manufacturer --}}
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        @if ($product->product_type)
                                            <span class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-semibold tracking-wider rtl:tracking-normal bg-brand/10 text-brand dark:bg-brand/20 dark:text-brand shrink-0">
                                                {{ __('messages.products.types.' . $product->product_type) }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-white truncate text-right bg-slate-600 py-1.5 px-2.5 rounded font-semibold tracking-wider rtl:tracking-normal">
                                        {{ $product->manufacturer->first()?->name ?? __('messages.products.unknown') }}
                                    </span>
                                </div>

                                {{-- Trade name --}}
                                <a href="{{ route('products.show', $product) }}" class="text-lg font-semibold text-heading dark:text-white group-hover:text-brand transition-colors leading-tight mb-3">
                                    {{ $product->trade_name }}
                                </a>

                                {{-- Details section --}}
                                <div class="mt-auto pt-3 border-t border-dashed border-neutral-200 dark:border-gray-700 space-y-2">
                                    @if ($product->dosageForm)
                                        <div class="flex items-center gap-2 text-sm text-body dark:text-gray-400">
                                            <x-lucide-pill class="w-4 h-4 shrink-0" />
                                            <span>{{ $product->dosageForm->name }}</span>
                                        </div>
                                    @endif

                                    @if ($product->activeIngredients->isNotEmpty())
                                        @php $firstIngredient = $product->activeIngredients->first(); @endphp
                                        <div class="flex items-center gap-2 text-sm text-body dark:text-gray-400">
                                            <x-lucide-flask-conical class="w-4 h-4 shrink-0" />
                                            <span>
                                                <span class="font-medium text-heading dark:text-white">{{ $firstIngredient->name }}</span>
                                                @if ($firstIngredient->pivot?->strength)
                                                    <span class="text-brand font-semibold">—{{ rtrim(rtrim($firstIngredient->pivot->strength, '0'), '.') }}</span>
                                                    @if ($firstIngredient->pivot?->unit)
                                                        <span class="text-body">/{{ $firstIngredient->pivot->unit }}</span>
                                                    @endif
                                                @endif
                                                @if ($product->activeIngredients->count() > 1)
                                                    <span class="text-brand font-medium"> +{{ $product->activeIngredients->count() - 1 }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    @if ($product->package_size)
                                        <div class="flex items-center gap-2 text-sm text-body dark:text-gray-400">
                                            <x-lucide-package class="w-4 h-4 shrink-0" />
                                            <span>{{ $product->package_size }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom action bar --}}
                            <a href="{{ route('products.show', $product) }}" class="border-t border-default-medium dark:border-gray-700 px-5 py-3 flex items-center justify-between text-sm font-medium text-body hover:text-brand dark:text-gray-400 dark:hover:text-brand hover:bg-neutral-secondary-soft dark:hover:bg-gray-700/50 transition-colors">
                                <span class="flex items-center gap-2">
                                    <x-lucide-eye class="w-4 h-4" />
                                    {{ __('messages.products.details') }}
                                </span>
                                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 transition-transform" />
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <x-lucide-package class="w-12 h-12 text-body mx-auto mb-4" />
                            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.products.no_products') }}</h3>
                            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.products.try_another') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="w-full">
                {{ $products->withQueryString()->links('pagination::tailwind') }}
            </div>
        @endif

    </div>
@endsection
