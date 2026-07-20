<?php

use App\Actions\CompareAction;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[On('compare-updated')]
    public function refresh(): void
    {
        unset($this->products);
    }

    #[Computed]
    public function products()
    {
        $ids = app(CompareAction::class)->all();

        if (empty($ids)) {
            return collect();
        }

        return Product::query()
            ->whereIn('id', $ids)
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
            ->get();
    }

    public function remove(string $id): void
    {
        app(CompareAction::class)->remove($id);
        $this->dispatch('compare-updated');
    }

    public function clear(): void
    {
        app(CompareAction::class)->clear();
        $this->dispatch('compare-updated');
    }
};
?>

<div class="space-y-4">
    <x-page-hero
        :heading="__('messages.compare.page_heading')"
        :subtitle="__('messages.compare.page_subtitle')"
        :stats="[
            ['count' => $this->products->count(), 'label' => __('messages.compare.products_count'), 'icon' => 'git-compare'],
        ]"
    />

    @if ($this->products->isEmpty())
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs py-16 text-center">
            <x-lucide-git-compare class="w-16 h-16 text-body dark:text-slate-400 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-heading dark:text-white mb-2">
                {{ __('messages.compare.empty_title') }}
            </h3>
            <p class="text-sm text-body dark:text-slate-400 mb-6">
                {{ __('messages.compare.empty_desc') }}
            </p>
            <a href="{{ route('products.index') }}" wire:navigate
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-all">
                <x-lucide-shopping-bag class="w-4 h-4" />
                {{ __('messages.compare.browse_products') }}
            </a>
        </div>
    @else
        {{-- Clear all --}}
        <div class="flex justify-end">
            <button x-on:click="
                    $wire.clear().then(() => {
                        window.dispatchEvent(new CustomEvent('compare-clear'));
                    });
                " wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 text-sm text-body hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 underline transition-colors disabled:opacity-50 disabled:cursor-wait">
                <span wire:loading.remove wire:target="clear">
                    <x-lucide-trash-2 class="w-4 h-4" />
                </span>
                <span wire:loading wire:target="clear">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="clear">{{ __('messages.compare.clear_all') }}</span>
                <span wire:loading wire:target="clear">{{ __('messages.compare.clearing') }}</span>
            </button>
        </div>

        {{-- Side-by-side table --}}
        <div class="block bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right [&_th:not(:first-child)]:border-s [&_th:not(:first-child)]:border-default-medium dark:[&_th:not(:first-child)]:border-slate-700 [&_td:not(:first-child)]:border-s [&_td:not(:first-child)]:border-default-medium dark:[&_td:not(:first-child)]:border-slate-700">
                    <thead class="bg-neutral-secondary-soft dark:bg-slate-700">
                        <tr>
                            <th class="px-5 py-4 text-xs uppercase tracking-wider text-body dark:text-slate-400 font-semibold w-48">
                                {{ __('messages.compare.attribute') }}
                            </th>
                            @foreach ($this->products as $product)
                                <th class="px-5 py-4 min-w-[250px]">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-sm font-bold text-heading dark:text-white truncate">
                                            {{ $product->trade_name }}
                                        </span>
                                        <button wire:click="remove('{{ $product->id }}')"
                                            class="text-body hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 transition-colors shrink-0"
                                            title="{{ __('messages.compare.remove_from_compare') }}">
                                            <x-lucide-x class="w-4 h-4" />
                                        </button>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-default-medium dark:divide-slate-700">
                        {{-- Image --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.image') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Trade name --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.trade_name') }}
                            </td>
                            @foreach ($this->products as $product)
                                <td class="px-5 py-4">
                                    <a href="{{ route('products.show', $product) }}"
                                        class="text-sm font-bold text-fg-brand hover:underline">
                                        {{ $product->trade_name }}
                                    </a>
                                    @if ($product->trade_name_ar)
                                        <p class="text-xs text-body dark:text-slate-400 mt-0.5" dir="rtl">{{ $product->trade_name_ar }}</p>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Product type --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.product_type') }}
                            </td>
                            @foreach ($this->products as $product)
                                <td class="px-5 py-4">
                                    @if ($product->product_type)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-base text-xs font-medium bg-brand/15 text-brand-strong dark:bg-brand/25 dark:text-brand">
                                            {{ __('messages.products.types.' . $product->product_type) }}
                                        </span>
                                    @else
                                        <span class="text-body dark:text-slate-400">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Dosage form --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.dosage_form') }}
                            </td>
                            @foreach ($this->products as $product)
                                <td class="px-5 py-4">
                                    <span class="text-sm text-heading dark:text-white">{{ $product->dosageForm?->name ?? '—' }}</span>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Manufacturer --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.manufacturer') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Active ingredients --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.active_ingredients') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Diseases --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.diseases') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Package size --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.package_size') }}
                            </td>
                            @foreach ($this->products as $product)
                                <td class="px-5 py-4">
                                    <span class="text-sm text-heading dark:text-white">{{ $product->package_size ?? '—' }}</span>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Storage conditions --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.storage_conditions') }}
                            </td>
                            @foreach ($this->products as $product)
                                <td class="px-5 py-4">
                                    <span class="text-sm text-heading dark:text-white">{{ $product->storage_conditions ?? '—' }}</span>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Dosages --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.dosages') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Withdrawal periods --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.withdrawal_periods') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Indications --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.indications') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Contraindications --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.contraindications') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Precautions --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.precautions') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>

                        {{-- Side effects --}}
                        <tr>
                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                {{ __('messages.compare.side_effects') }}
                            </td>
                            @foreach ($this->products as $product)
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
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    @endif
</div>
