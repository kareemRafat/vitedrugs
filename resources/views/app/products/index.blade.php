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
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <form method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-3">
                    <div class="lg:col-span-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-3 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            placeholder="{{ __('messages.products.search_placeholder') }}">
                    </div>

                    <div id="advanced-filters" class="sm:contents max-sm:flex max-sm:flex-col max-sm:gap-4">
                        <select name="company"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">{{ __('messages.products.all_companies') }}</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" @selected(request('company') == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>

                        <select name="dosage_form"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">{{ __('messages.products.all_forms') }}</option>
                            @foreach ($dosageForms as $form)
                                <option value="{{ $form->id }}" @selected(request('dosage_form') == $form->id)>{{ $form->name }}</option>
                            @endforeach
                        </select>

                        <select name="sort"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="latest" @selected(request('sort') == 'latest')>{{ __('messages.products.sort_latest') }}</option>
                            <option value="name" @selected(request('sort') == 'name')>{{ __('messages.products.sort_name') }}</option>
                            <option value="oldest" @selected(request('sort') == 'oldest')>{{ __('messages.products.sort_oldest') }}</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            {{ __('messages.products.search_button') }}
                        </button>
                        <a href="{{ route('products.index') }}"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600">
                            <x-lucide-x class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                <button type="button" onclick="var f=document.getElementById('advanced-filters');var s=f.style;s.display=s.display==='none'?'':'none';this.querySelector('.chevron').classList.toggle('rotate-180')"
                    class="max-sm:flex sm:hidden mt-3 w-full items-center justify-center gap-1.5 text-sm text-fg-brand hover:underline cursor-pointer">
                    <span>{{ __('messages.products.advanced_search') }}</span>
                    <x-lucide-chevron-down class="chevron w-4 h-4 transition-transform" />
                </button>
            </form>
        </div>

        {{-- Products Grid --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800">
            <div class="px-5 py-4 border-b border-default-medium dark:border-slate-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.products.directory_title') }}</h2>
                <span class="text-sm text-body dark:text-slate-400">
                    {{ __('messages.products.showing') }}
                    {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                    {{ __('messages.products.of') }}
                    {{ number_format($products->total()) }}
                </span>
            </div>

            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($products as $product)
                        <div class="group bg-white dark:bg-slate-800 rounded-xl border border-neutral-200 dark:border-slate-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col relative overflow-hidden">
                            {{-- Gradient accent bar --}}
                            <div class="h-1.5 w-full bg-gradient-to-r from-brand via-brand-strong to-sky-400 dark:from-sky-400 dark:via-brand dark:to-sky-300 absolute top-0 left-0 opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="p-5 pt-6 flex flex-col flex-1">
                                {{-- Card header --}}
                                <div class="bg-gradient-to-br from-brand/5 to-sky-50 dark:from-brand/10 dark:to-slate-700 -mx-5 -mt-6 px-5 pt-5 pb-4 mb-4 border-b border-brand/10 dark:border-slate-600">
                                    {{-- Top row: product type + manufacturer --}}
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <div class="flex items-center gap-2 min-w-0">
                                            @if ($product->product_type)
                                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-semibold tracking-wider rtl:tracking-normal bg-brand/15 text-brand-strong dark:bg-brand/25 dark:text-brand shrink-0 shadow-sm">
                                                    {{ __('messages.products.types.' . $product->product_type) }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs truncate text-right bg-white/60 dark:bg-slate-600/80 text-brand-strong dark:text-sky-300 py-1.5 px-2.5 rounded-md font-semibold tracking-wider rtl:tracking-normal shadow-sm backdrop-blur-sm">
                                            <x-lucide-building-2 class="w-3 h-3 inline -mt-0.5 me-1" />
                                            {{ $product->manufacturer->first()?->name ?? __('messages.products.unknown') }}
                                        </span>
                                    </div>

                                    {{-- Trade name --}}
                                    <a href="{{ route('products.show', $product) }}" class="text-lg font-bold text-heading dark:text-white group-hover:text-brand dark:group-hover:text-sky-400 transition-colors leading-tight block min-h-[3.5rem] line-clamp-2">
                                        {{ $product->trade_name }}
                                    </a>
                                </div>

                                {{-- Details section with colored icons --}}
                                <div class="flex-1 space-y-3">
                                    @if ($product->dosageForm)
                                        <div class="flex items-center gap-2.5 text-sm text-body dark:text-slate-300 group/detail">
                                            <span class="w-7 h-7 rounded-lg bg-brand/10 dark:bg-brand/20 flex items-center justify-center shrink-0 group-hover/detail:bg-brand/20 dark:group-hover/detail:bg-brand/30 transition-colors">
                                                <x-lucide-pill class="w-3.5 h-3.5 text-brand" />
                                            </span>
                                            <span>{{ $product->dosageForm->name }}</span>
                                        </div>
                                    @endif

                                    @if ($product->activeIngredients->isNotEmpty())
                                        @php $firstIngredient = $product->activeIngredients->first(); @endphp
                                        <div class="flex items-center gap-2.5 text-sm text-body dark:text-slate-300 group/detail">
                                            <span class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center shrink-0 group-hover/detail:bg-sky-100 dark:group-hover/detail:bg-sky-900/50 transition-colors">
                                                <x-lucide-flask-conical class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400" />
                                            </span>
                                            <span>
                                                <span class="font-medium text-heading dark:text-white">{{ $firstIngredient->name }}</span>
                                                @if ($firstIngredient->pivot?->strength)
                                                    <span class="text-brand font-semibold">—{{ rtrim(rtrim($firstIngredient->pivot->strength, '0'), '.') }}</span>
                                                    @if ($firstIngredient->pivot?->unit)
                                                        <span class="text-body dark:text-slate-400">/{{ $firstIngredient->pivot->unit }}</span>
                                                    @endif
                                                @endif
                                                @if ($product->activeIngredients->count() > 1)
                                                    <span class="text-brand font-medium"> +{{ $product->activeIngredients->count() - 1 }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    @if ($product->package_size)
                                        <div class="flex items-center gap-2.5 text-sm text-body dark:text-slate-300 group/detail">
                                            <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center shrink-0 group-hover/detail:bg-amber-100 dark:group-hover/detail:bg-amber-900/50 transition-colors">
                                                <x-lucide-package class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                                            </span>
                                            <span>{{ $product->package_size }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Bottom action bar --}}
                            <a href="{{ route('products.show', $product) }}" class="border-t border-default-medium dark:border-slate-700 px-5 py-3.5 flex items-center justify-between text-sm font-medium text-brand hover:text-brand-strong dark:text-sky-400 dark:hover:text-sky-300 hover:bg-brand/5 dark:hover:bg-slate-700/50 transition-all group/action">
                                <span class="flex items-center gap-2">
                                    <x-lucide-eye class="w-4 h-4" />
                                    {{ __('messages.products.details') }}
                                </span>
                                <span class="flex items-center gap-1 group-hover/action:translate-x-1 transition-transform">
                                    <span class="text-xs text-body dark:text-slate-400">{{ __('messages.nav.view') }}</span>
                                    <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                </span>
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <x-lucide-package class="w-12 h-12 text-body dark:text-slate-400 mx-auto mb-4" />
                            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.products.no_products') }}</h3>
                            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.products.try_another') }}</p>
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

@section('js')
<script>
if (window.innerWidth < 640) {
    document.getElementById('advanced-filters').style.display = 'none';
}
</script>
@endsection


