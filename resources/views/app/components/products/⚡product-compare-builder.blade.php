<?php

use App\Actions\Products\CompareAction;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    private const PER_COLUMN = 10;

    #[Url(as: 'product1')]
    public ?string $productId1 = null;

    #[Url(as: 'product2')]
    public ?string $productId2 = null;

    #[Url(as: 'product3')]
    public ?string $productId3 = null;

    public string $search1 = '';
    public string $search2 = '';
    public string $search3 = '';

    public function selectProduct(int $column, string $id): void
    {
        $prop = "productId{$column}";
        $this->$prop = $id;
        $this->{"search{$column}"} = '';
    }

    public function removeProduct(int $column): void
    {
        $this->{"productId{$column}"} = null;
    }

    public function clearAll(): void
    {
        $this->productId1 = null;
        $this->productId2 = null;
        $this->productId3 = null;
    }

    public function browseProducts(): void
    {
        app(CompareAction::class)->clear();
        $this->redirectRoute('products.index', navigate: true);
    }

    private function searchQuery(int $column)
    {
        $search = $this->{"search{$column}"};

        if (mb_strlen($search) < 1) {
            return collect();
        }

        return Product::query()
            ->whereFullText(['trade_name', 'trade_name_ar'], $search . '*', ['mode' => 'boolean'])
            ->with(['dosageForm', 'manufacturer'])
            ->take(self::PER_COLUMN)
            ->get();
    }

    #[Computed]
    public function searchResults1()
    {
        return $this->searchQuery(1);
    }

    #[Computed]
    public function searchResults2()
    {
        return $this->searchQuery(2);
    }

    #[Computed]
    public function searchResults3()
    {
        return $this->searchQuery(3);
    }

    private function loadProduct(?string $id): ?Product
    {
        if (! $id) {
            return null;
        }

        return Product::query()
            ->where('id', $id)
            ->with([
                'dosageForm',
                'manufacturer',
                'activeIngredients',
                'diseases',
                'dosages.species',
                'withdrawalPeriods.species',
                'indications',
                'contraindications',
                'precautions',
                'sideEffects',
                'images',
                'companies',
            ])
            ->first();
    }

    #[Computed]
    public function product1(): ?Product
    {
        return $this->loadProduct($this->productId1);
    }

    #[Computed]
    public function product2(): ?Product
    {
        return $this->loadProduct($this->productId2);
    }

    #[Computed]
    public function product3(): ?Product
    {
        return $this->loadProduct($this->productId3);
    }

    #[Computed]
    public function selectedProducts(): array
    {
        return array_filter([$this->product1, $this->product2, $this->product3]);
    }

    #[Computed]
    public function totalCount(): int
    {
        return count($this->selectedProducts);
    }
};
?>

<div class="space-y-6">
    <x-page-hero
        :heading="__('messages.compare.page_heading')"
        :subtitle="__('messages.compare.page_subtitle')"
        :stats="[
            ['count' => $this->totalCount, 'label' => __('messages.compare.products_count'), 'icon' => 'git-compare'],
        ]"
    />

    {{-- Column pickers --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
        @foreach ([1, 2, 3] as $col)
            @php
                $prop = "productId{$col}";
                $searchProp = "search{$col}";
                $productProp = "product{$col}";
                $resultsProp = "searchResults{$col}";
                $product = $this->$productProp;
            @endphp
            <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs flex flex-col">
                <div class="p-4 border-b border-default-medium dark:border-slate-700">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="{{ $searchProp }}"
                            placeholder="{{ __('messages.products.search_placeholder') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-9 px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    </div>

                    {{-- Search results dropdown --}}
                    <div wire:key="results-{{ $col }}" class="relative mt-1">
                        @if ($this->$searchProp && $this->$resultsProp->isNotEmpty())
                            <div class="absolute z-10 w-full bg-white dark:bg-slate-700 border border-default-medium dark:border-slate-600 rounded-base shadow-lg max-h-60 overflow-y-auto">
                                @foreach ($this->$resultsProp as $result)
                                    <button type="button" wire:click="selectProduct({{ $col }}, '{{ $result->id }}')"
                                        class="w-full text-left px-3 py-2.5 hover:bg-neutral-secondary-soft dark:hover:bg-slate-600 border-b border-default-medium dark:border-slate-600 last:border-b-0 transition-colors">
                                        <span class="text-sm font-medium text-heading dark:text-white block">{{ $result->trade_name }}</span>
                                        <span class="text-xs text-body dark:text-slate-400">
                                            {{ $result->dosageForm?->name }}
                                            @if ($result->dosageForm && $result->manufacturer->first())
                                                &middot;
                                            @endif
                                            {{ $result->manufacturer->first()?->name }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Selected product card --}}
                <div class="p-4 flex-1 flex flex-col">
                    @if ($product)
                        <div class="relative flex gap-3">
                            {{-- Remove --}}
                            <button type="button" wire:click="removeProduct({{ $col }})"
                                class="absolute -top-1.5 -end-1.5 z-10 w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/50 text-red-500 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800 flex items-center justify-center transition-colors">
                                <x-lucide-x class="w-4 h-4" />
                            </button>

                            {{-- Image --}}
                            <div class="w-16 h-16 shrink-0 rounded-base bg-neutral-secondary-medium dark:bg-slate-700 border border-default-medium dark:border-slate-600 flex items-center justify-center overflow-hidden">
                                @if ($product->images->isNotEmpty())
                                    <img src="{{ Storage::disk('public')->url($product->images->first()->image) }}"
                                        alt="{{ $product->trade_name }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy" />
                                @else
                                    <x-lucide-package class="w-6 h-6 text-body dark:text-slate-400" />
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0 pe-6">
                                <a href="{{ route('products.show', $product) }}"
                                    class="text-sm font-semibold text-heading dark:text-white hover:text-brand dark:hover:text-sky-400 transition-colors leading-tight line-clamp-2">
                                    {{ $product->trade_name }}
                                </a>

                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    @if ($product->product_type)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand/15 text-brand-strong dark:bg-brand/25 dark:text-brand">
                                            {{ __('messages.products.types.' . $product->product_type) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs text-body dark:text-slate-400 mt-1 space-y-0.5">
                                    <p>{{ $product->dosageForm?->name }}</p>
                                    <p class="truncate">{{ $product->manufacturer->first()?->name ?? __('messages.products.unknown') }}</p>
                                    @if ($product->activeIngredients->isNotEmpty())
                                        <p class="truncate">{{ $product->activeIngredients->pluck('name')->implode(', ') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <x-lucide-search class="w-10 h-10 text-body/40 dark:text-slate-500 mx-auto mb-2" />
                                <p class="text-sm text-body/60 dark:text-slate-500">
                                    {{ __('messages.compare.search_to_add') }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Clear all --}}
    @if ($this->totalCount > 0)
        <div class="flex justify-end">
            <button type="button" wire:click="clearAll" wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 text-sm text-body hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 underline transition-colors disabled:opacity-50 disabled:cursor-wait">
                <span wire:loading.remove wire:target="clearAll">
                    <x-lucide-trash-2 class="w-4 h-4" />
                </span>
                <span wire:loading wire:target="clearAll">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="clearAll">{{ __('messages.compare.clear_all') }}</span>
                <span wire:loading wire:target="clearAll">{{ __('messages.compare.clearing') }}</span>
            </button>
        </div>
    @endif

    {{-- Comparison table --}}
    @if ($this->totalCount > 0)
        @php $products = [$this->product1, $this->product2, $this->product3]; @endphp
        <div class="block bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right [&_th:not(:first-child)]:border-s [&_th:not(:first-child)]:border-default-medium dark:[&_th:not(:first-child)]:border-slate-700 [&_td:not(:first-child)]:border-s [&_td:not(:first-child)]:border-default-medium dark:[&_td:not(:first-child)]:border-slate-700">
                    <thead class="bg-neutral-secondary-soft dark:bg-slate-700">
                        <tr>
                            <th class="px-5 py-4 text-xs uppercase tracking-wider text-body dark:text-slate-400 font-semibold w-48">
                                {{ __('messages.compare.attribute') }}
                            </th>
                            @foreach ($products as $product)
                                @if ($product)
                                    <th class="px-5 py-4 min-w-[220px] text-sm font-bold text-heading dark:text-white">
                                        {{ $product->trade_name }}
                                    </th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default-medium dark:divide-slate-700">
                        {{-- Image --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.image') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->images->isNotEmpty())
                                            <img src="{{ Storage::disk('public')->url($product->images->first()->image) }}"
                                                alt="{{ $product->trade_name }}"
                                                class="w-24 h-24 object-cover rounded-base border border-default-medium dark:border-slate-600"
                                                loading="lazy" />
                                        @else
                                            <div class="w-24 h-24 rounded-base bg-neutral-secondary-medium dark:bg-slate-700 border border-default-medium dark:border-slate-600 flex items-center justify-center">
                                                <x-lucide-package class="w-8 h-8 text-body dark:text-slate-400" />
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Trade name --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.trade_name') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        <a href="{{ route('products.show', $product) }}"
                                            class="text-sm font-bold text-fg-brand hover:underline">
                                            {{ $product->trade_name }}
                                        </a>
                                        @if ($product->trade_name_ar)
                                            <p class="text-xs text-body dark:text-slate-400 mt-0.5" dir="rtl">{{ $product->trade_name_ar }}</p>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Product type --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.product_type') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->product_type)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-base text-xs font-medium bg-brand/15 text-brand-strong dark:bg-brand/25 dark:text-brand">
                                                {{ __('messages.products.types.' . $product->product_type) }}
                                            </span>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Dosage form --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.dosage_form') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        <span class="text-sm text-heading dark:text-white">{{ $product->dosageForm?->name ?? '—' }}</span>
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Manufacturer --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.manufacturer') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @php $manufacturer = $product->manufacturer->first(); @endphp
                                        @if ($manufacturer)
                                            <a href="{{ route('companies.show', $manufacturer) }}"
                                                class="text-sm text-fg-brand hover:underline">
                                                {{ $manufacturer->name }}
                                            </a>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Active ingredients --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.active_ingredients') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->activeIngredients->isNotEmpty())
                                            <ul class="space-y-2">
                                                @foreach ($product->activeIngredients as $ingredient)
                                                    <li>
                                                        <a href="{{ route('active-ingredients.show', $ingredient) }}"
                                                            class="text-sm text-fg-brand hover:underline font-medium">
                                                            {{ $ingredient->name }}
                                                        </a>
                                                        @if ($ingredient->pivot->strength)
                                                            <span class="text-xs text-body dark:text-slate-400 block">
                                                                {{ rtrim(rtrim($ingredient->pivot->strength, '0'), '.') }}
                                                                @if ($ingredient->pivot->unit)
                                                                    /{{ $ingredient->pivot->unit }}
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Diseases --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.diseases') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->diseases->isNotEmpty())
                                            <ul class="space-y-1">
                                                @foreach ($product->diseases as $disease)
                                                    <li>
                                                        <a href="{{ route('diseases.show', $disease) }}"
                                                            class="text-sm text-fg-brand hover:underline">
                                                            {{ $disease->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Package size --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.package_size') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        <span class="text-sm text-heading dark:text-white">{{ $product->package_size ?? '—' }}</span>
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Storage conditions --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.storage_conditions') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        <span class="text-sm text-heading dark:text-white">{{ $product->storage_conditions ?? '—' }}</span>
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Dosages --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.dosages') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->dosages->isNotEmpty())
                                            <div class="space-y-3">
                                                @foreach ($product->dosages->groupBy(fn($d) => $d->species?->name ?? __('messages.compare.unspecified')) as $species => $dosages)
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase text-body dark:text-slate-400 mb-1">{{ $species }}</p>
                                                        <ul class="space-y-1">
                                                            @foreach ($dosages as $dosage)
                                                                <li class="text-sm text-heading dark:text-white">
                                                                    {{ $dosage->dosage }}
                                                                    @if ($dosage->route)
                                                                        <span class="text-body dark:text-slate-400">({{ $dosage->route }})</span>
                                                                    @endif
                                                                    @if ($dosage->duration)
                                                                        <span class="text-body dark:text-slate-400">— {{ $dosage->duration }}</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Withdrawal periods --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.withdrawal_periods') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->withdrawalPeriods->isNotEmpty())
                                            <div class="space-y-3">
                                                @foreach ($product->withdrawalPeriods as $wp)
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase text-body dark:text-slate-400 mb-1">{{ $wp->species?->name }}</p>
                                                        <div class="text-sm text-heading dark:text-white space-y-0.5">
                                                            @if ($wp->meat_days)
                                                                <span class="block">{{ __('messages.compare.meat') }}: {{ $wp->meat_days }} {{ __('messages.compare.days') }}</span>
                                                            @endif
                                                            @if ($wp->milk_days)
                                                                <span class="block">{{ __('messages.compare.milk') }}: {{ $wp->milk_days }} {{ __('messages.compare.days') }}</span>
                                                            @endif
                                                            @if ($wp->egg_days)
                                                                <span class="block">{{ __('messages.compare.eggs') }}: {{ $wp->egg_days }} {{ __('messages.compare.days') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Indications --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.indications') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->indications->isNotEmpty())
                                            <ul class="space-y-2">
                                                @foreach ($product->indications as $indication)
                                                    <li class="flex items-start gap-1.5">
                                                        <x-lucide-check-circle class="w-4 h-4 text-fg-brand mt-0.5 shrink-0" />
                                                        <span class="text-sm text-heading dark:text-white">{{ $indication->description }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Contraindications --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.contraindications') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->contraindications->isNotEmpty())
                                            <ul class="space-y-2">
                                                @foreach ($product->contraindications as $contraindication)
                                                    <li class="flex items-start gap-1.5">
                                                        <x-lucide-ban class="w-4 h-4 text-red-500 dark:text-red-400 mt-0.5 shrink-0" />
                                                        <span class="text-sm text-heading dark:text-white">{{ $contraindication->description }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Precautions --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.precautions') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->precautions->isNotEmpty())
                                            <ul class="space-y-2">
                                                @foreach ($product->precautions as $precaution)
                                                    <li class="flex items-start gap-1.5">
                                                        <x-lucide-alert-triangle class="w-4 h-4 text-amber-500 dark:text-amber-400 mt-0.5 shrink-0" />
                                                        <span class="text-sm text-heading dark:text-white">{{ $precaution->description }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>

                        {{-- Side effects --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.side_effects') }}
                            </td>
                            @foreach ($products as $product)
                                @if ($product)
                                    <td class="px-5 py-4">
                                        @if ($product->sideEffects->isNotEmpty())
                                            <ul class="space-y-2">
                                                @foreach ($product->sideEffects as $sideEffect)
                                                    <li class="flex items-start gap-1.5">
                                                        <x-lucide-alert-circle class="w-4 h-4 text-red-500 dark:text-red-400 mt-0.5 shrink-0" />
                                                        <span class="text-sm text-heading dark:text-white">{{ $sideEffect->description }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-body dark:text-slate-400">—</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs py-16 text-center">
            <x-lucide-git-compare class="w-16 h-16 text-body dark:text-slate-400 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-heading dark:text-white mb-2">
                {{ __('messages.compare.empty_title') }}
            </h3>
            <p class="text-sm text-body dark:text-slate-400 mb-6">
                {{ __('messages.compare.empty_desc') }}
            </p>
            <button type="button" wire:click="browseProducts"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-all">
                <x-lucide-shopping-bag class="w-4 h-4" />
                {{ __('messages.compare.browse_products') }}
            </button>
        </div>
    @endif
</div>
