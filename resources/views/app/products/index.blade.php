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
            ]">
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('products.submission.create') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-base text-sm font-semibold bg-white/20 text-white hover:bg-white/30 transition-colors shadow-sm border border-white/30">
                    <x-lucide-plus class="w-4 h-4" />
                    {{ __('messages.products.submit_product') }}
                </a>
                <a href="{{ route('products.compare') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-base text-sm font-semibold bg-white text-brand hover:bg-white/90 dark:bg-sky-700 dark:text-white dark:hover:bg-sky-600 transition-colors shadow-sm border border-white/20">
                    <x-lucide-git-compare class="w-4 h-4" />
                    {{ __('messages.compare.page_heading') }}
                </a>
            </div>
        </x-page-hero>

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
                        <a href="{{ route('products.index') }}" wire:navigate
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
                        <div class="group bg-white dark:bg-slate-800 rounded-2xl border border-neutral-200/80 dark:border-slate-700/80 shadow-xs hover:shadow-xl hover:shadow-brand/5 dark:hover:shadow-sky-500/5 hover:-translate-y-1 transition-all duration-300 flex flex-col relative overflow-hidden">
                            {{-- Top Accent Bar --}}
                            <div class="h-1.5 w-full bg-gradient-to-r from-brand via-brand-strong to-sky-400 dark:from-sky-400 dark:via-brand dark:to-sky-300"></div>

                            {{-- Card Header Section --}}
                            <div class="bg-gradient-to-br from-brand/5 to-sky-50 dark:from-brand/10 dark:to-slate-700/70 p-5 border-b border-neutral-200/60 dark:border-slate-700/60">
                                {{-- Top Badge Row: Product Type --}}
                                @if ($product->product_type)
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide bg-brand/10 dark:bg-brand/20 text-brand-strong dark:text-sky-300 border border-brand/15 dark:border-brand/30 shadow-2xs backdrop-blur-xs shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand dark:bg-sky-400"></span>
                                            {{ __('messages.products.types.' . $product->product_type) }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Product Trade Name --}}
                                <a href="{{ route('products.show', $product) }}" wire:navigate class="block min-h-[3rem] group/title">
                                    <h3 class="text-lg sm:text-xl font-bold text-heading dark:text-white group-hover/title:text-brand dark:group-hover/title:text-sky-400 transition-colors duration-200 leading-snug line-clamp-2">
                                        {{ $product->trade_name }}
                                    </h3>
                                    @if (!empty($product->trade_name_ar))
                                        <span class="block text-xs font-medium text-body/70 dark:text-slate-400 mt-1 line-clamp-1">
                                            {{ $product->trade_name_ar }}
                                        </span>
                                    @endif
                                </a>

                                {{-- Manufacturer Sub-header Line (Under Product Name with University Icon) --}}
                                <div class="flex items-center gap-1.5 mt-3 pt-2.5 border-t border-brand/10 dark:border-slate-700/60 min-w-0" title="{{ $product->manufacturer->first()?->name ?? __('messages.products.unknown') }}">
                                    <x-lucide-university class="w-3.5 h-3.5 text-brand dark:text-sky-400 shrink-0" />
                                    <span class="truncate text-sm font-semibold text-body/90 dark:text-slate-300 tracking-wide">
                                        {{ $product->manufacturer->first()?->name ?? __('messages.products.unknown') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Card Body Details Section --}}
                            <div class="p-5 flex-1 space-y-3.5 bg-white dark:bg-slate-800">
                                @if ($product->dosageForm)
                                    <div class="flex items-center gap-3 text-sm text-body dark:text-slate-300 group/detail">
                                        <span class="w-8 h-8 rounded-xl bg-brand/10 dark:bg-brand/20 flex items-center justify-center shrink-0 group-hover/detail:bg-brand/20 dark:group-hover/detail:bg-brand/30 transition-colors">
                                            <x-lucide-pill class="w-4 h-4 text-brand dark:text-sky-400" />
                                        </span>
                                        <span class="font-medium text-heading dark:text-slate-200 text-xs sm:text-sm">{{ $product->dosageForm->name }}</span>
                                    </div>
                                @endif

                                @if ($product->activeIngredients->isNotEmpty())
                                    @php $firstIngredient = $product->activeIngredients->first(); @endphp
                                    <div class="flex items-center gap-3 text-sm text-body dark:text-slate-300 group/detail">
                                        <span class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-800/40 flex items-center justify-center shrink-0 group-hover/detail:bg-sky-100 dark:group-hover/detail:bg-sky-900/50 transition-colors mt-0.5">
                                            <x-lucide-flask-conical class="w-4 h-4 text-sky-600 dark:text-sky-400" />
                                        </span>
                                        <div class="min-w-0 flex-1 leading-snug">
                                            <span class="font-semibold text-heading dark:text-white text-xs sm:text-sm">{{ $firstIngredient->name }}</span>
                                            @if ($firstIngredient->pivot?->strength)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold bg-brand/10 text-brand dark:bg-sky-400/20 dark:text-sky-300 ms-1">
                                                    {{ rtrim(rtrim($firstIngredient->pivot->strength, '0'), '.') }}
                                                    @if ($firstIngredient->pivot?->unit)
                                                        {{ $firstIngredient->pivot->unit }}
                                                    @endif
                                                </span>
                                            @endif
                                            @if ($product->activeIngredients->count() > 1)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-neutral-100 text-body dark:bg-slate-700 dark:text-slate-300 ms-1">
                                                    +{{ $product->activeIngredients->count() - 1 }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($product->package_size)
                                    <div class="flex items-center gap-3 text-sm text-body dark:text-slate-300 group/detail">
                                        <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-800/40 flex items-center justify-center shrink-0 group-hover/detail:bg-amber-100 dark:group-hover/detail:bg-amber-900/50 transition-colors">
                                            <x-lucide-package class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                        </span>
                                        <span class="text-xs sm:text-sm font-medium text-heading dark:text-slate-200">{{ $product->package_size }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Action Footer --}}
                            <div class="px-5 py-3.5 bg-neutral-50/70 dark:bg-slate-800/90 border-t border-neutral-100 dark:border-slate-700/80 flex items-center justify-between mt-auto">
                                <a href="{{ route('products.show', $product) }}" wire:navigate class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-brand hover:text-white bg-brand/10 hover:bg-brand dark:bg-sky-400/10 dark:text-sky-400 dark:hover:bg-sky-500 dark:hover:text-white transition-all duration-200 shadow-2xs group/action">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    <span>{{ __('messages.products.details') }}</span>
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180 group-hover/action:translate-x-0.5 transition-transform" />
                                </a>
                                @livewire('products.product-compare-toggle', ['productId' => $product->id, 'productName' => $product->trade_name], key('compare-'.$product->id))
                            </div>
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

        <x-pagination :paginator="$products" translation-prefix="messages.products" />

    </div>
@endsection

@section('js')
<script>
if (window.innerWidth < 640) {
    document.getElementById('advanced-filters').style.display = 'none';
}
</script>
@endsection


