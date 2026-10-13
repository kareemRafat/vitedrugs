<?php

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\DosageForm;
use App\Models\Product;
use App\Models\WithdrawalPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The drug catalogue for one part.
 *
 * Shared by `/poultry/products` and `/large-animals/products`, for the same
 * reason the comparison builder is shared (§9.19): listing drugs is the same
 * operation on both sides and only the candidate pool differs.
 *
 * `products` is one shared table with no species column, so membership comes from
 * `Product::applicableTo()` — a product belongs to a part when a dosing
 * instruction exists for one of that part's species. Three signals were available
 * and they disagree; dosing won because it is the only one of the three that is a
 * statement *about the animal*. The counts behind that choice are in the phase
 * notes, and they are the reason this page lists 15 products and not 24.
 *
 * Cards link out to `/drugs/products/{slug}` rather than rendering a second
 * detail page. One URL per product is worth more than a detail page scoped to 15
 * rows, and it leaves the Drugs part untouched.
 */
new class extends Component
{
    private const PER_PAGE = 12;

    public string $part = 'large-animals';

    #[Url(as: 'q', history: false)]
    public string $search = '';

    #[Url(as: 'dosage_form', history: false)]
    public string $dosageForm = '';

    #[Url(as: 'ingredient', history: false)]
    public string $ingredient = '';

    #[Url(as: 'manufacturer', history: false)]
    public string $manufacturer = '';

    #[Url(as: 'sort', history: false)]
    public string $sort = 'name';

    public function mount(?string $part = null): void
    {
        $this->part = $this->resolvePart($part);
    }

    /**
     * @return Builder<Product>
     */
    private function baseQuery(): Builder
    {
        $domain = $this->domain();

        return Product::query()
            ->applicableTo($domain)
            ->withPartRelations($domain);
    }

    /**
     * The list, narrowed by the filters.
     *
     * Search spans the four things a vet types when the brand name is not what
     * they know: the brand itself, the molecule, who makes it, or what it treats.
     * Matching `trade_name` alone misses three of the four.
     *
     * @return LengthAwarePaginator<int, Product>
     */
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        $term = trim($this->search);

        $query = $this->baseQuery()
            ->when($term !== '', fn (Builder $q) => $q->where(function (Builder $q) use ($term): void {
                $like = "%{$term}%";

                $q->where('products.trade_name', 'like', $like)
                    ->orWhere('products.trade_name_ar', 'like', $like)
                    ->orWhereHas('activeIngredients', fn (Builder $i) => $i
                        ->where('active_ingredients.name', 'like', $like)
                        ->orWhere('active_ingredients.name_ar', 'like', $like))
                    ->orWhereHas('companies', fn (Builder $c) => $c
                        ->where('companies.name', 'like', $like)
                        ->orWhere('companies.name_ar', 'like', $like))
                    ->orWhereHas('diseases', fn (Builder $d) => $d
                        ->where('diseases.domain', $this->domain())
                        ->where(fn (Builder $n) => $n
                            ->where('diseases.name', 'like', $like)
                            ->orWhere('diseases.name_ar', 'like', $like)));
            }))
            ->when($this->dosageForm !== '', fn (Builder $q) => $q->where('products.dosage_form_id', $this->dosageForm))
            ->when($this->ingredient !== '', fn (Builder $q) => $q
                ->whereHas('activeIngredients', fn (Builder $i) => $i->where('active_ingredients.id', $this->ingredient)))
            // `wherePivot('role', …)` cannot be used here. On this relation it emits a
            // literal, unqualified `"pivot" = ?` into the SQL rather than
            // `product_company"."role"`, so the filter silently matches nothing
            // and the page returns zero rows with no error. Qualifying the pivot
            // table by name produces the query that was intended.
            ->when($this->manufacturer !== '', fn (Builder $q) => $q
                ->whereHas('companies', fn (Builder $c) => $c
                    ->where('companies.id', $this->manufacturer)
                    ->where('product_company.role', 'manufacturer')));

        $query->orderBy('products.trade_name');

        if ($this->sort === 'name_desc') {
            $query->orderByDesc('products.trade_name');
        }

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->dosageForm = '';
        $this->ingredient = '';
        $this->manufacturer = '';
    }

    /**
     * Filter options, each scoped through the same inclusion rule as the page.
     *
     * The catalogue holds 30 products, so offering every manufacturer and every
     * ingredient in the database is mostly noise — and on the poultry half most
     * of those options lead to an empty page. Scoping the option lists means an
     * option is never shown unless it matches at least one product on this page.
     *
     * @return Collection<int, DosageForm>
     */
    #[Computed]
    public function dosageForms(): Collection
    {
        return DosageForm::query()
            ->whereIn('id', $this->baseQuery()->whereNotNull('dosage_form_id')->pluck('dosage_form_id'))
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, ActiveIngredient>
     */
    #[Computed]
    public function ingredients(): Collection
    {
        return ActiveIngredient::query()
            ->whereIn('id', $this->baseQuery()
                ->join('product_active_ingredient', 'product_active_ingredient.product_id', '=', 'products.id')
                ->pluck('product_active_ingredient.active_ingredient_id'))
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Company>
     */
    #[Computed]
    public function manufacturers(): Collection
    {
        return Company::query()
            ->whereIn('id', $this->baseQuery()
                ->join('product_company', 'product_company.product_id', '=', 'products.id')
                ->where('product_company.role', 'manufacturer')
                ->pluck('product_company.company_id'))
            ->orderBy('name')
            ->get();
    }

    /**
     * Withdrawal rows for the part, grouped by the species they apply to.
     *
     * Grouped because the figures are per-species and a flat list reads as one
     * set of numbers for the whole drug. Where a product holds two rows for the
     * same species — Excenel carries two Quail rows with different egg withdrawal
     * — both are kept rather than merged, because silently choosing one would
     * invent a figure the data does not support. That duplication is a data defect
     * and it is visible on purpose; see the phase notes.
     *
     * @return Collection<string, Collection<int, WithdrawalPeriod>>
     */
    public function withdrawalBySpecies(Product $product): Collection
    {
        return $product->withdrawalPeriods
            ->sortBy(fn (WithdrawalPeriod $w): string => (string) $w->species?->localized_name)
            ->groupBy(fn (WithdrawalPeriod $w): string => (string) ($w->species?->localized_name ?? __('common.unknown')));
    }

    private function domain(): string
    {
        return (string) config("vetpedia.parts.{$this->part}.domain");
    }

    private function resolvePart(?string $part): string
    {
        $parts = array_keys((array) config('vetpedia.parts', []));

        return in_array($part, $parts, true) ? $part : (string) array_key_first($parts);
    }
};
?>

@php
    $t = fn (string $key, array $replace = []): string => __($this->part . '.' . $key, $replace);
    $isPoultry = $this->domain() === 'poultry';
    $filters = [
        ['dosageForm', $this->dosageForms(), $t('catalogue.filter_dosage_form')],
        ['ingredient', $this->ingredients(), $t('catalogue.filter_ingredient')],
        ['manufacturer', $this->manufacturers(), $t('catalogue.filter_manufacturer')],
    ];
@endphp

<div class="space-y-6">
    <x-large-animals.page-hero
        :heading="$t('catalogue.heading')"
        :subtitle="$t('catalogue.subtitle')"
        :badge="$t('catalogue.badge')"
        badgeIcon="pill"
        :stats="[
            ['count' => $this->products->total(), 'label' => $t('catalogue.total'), 'icon' => 'pill'],
        ]"
    />

    <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs p-4 md:p-5 border border-default-medium dark:border-slate-700">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
            <div class="xl:col-span-2">
                <label for="catalogue-search" class="block mb-1.5 text-sm font-medium text-heading dark:text-white">{{ $t('catalogue.search_label') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input
                        id="catalogue-search"
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ $t('catalogue.search_placeholder') }}"
                        class="w-full bg-neutral-secondary-medium dark:bg-slate-900 border border-default-medium dark:border-slate-600 text-heading dark:text-white text-sm rounded-base ps-9 pe-3 py-2 focus:ring-brand focus:border-brand shadow-xs placeholder:text-body dark:placeholder:text-slate-500"
                    >
                </div>
            </div>

            @foreach ($filters as [$property, $options, $label])
                <div>
                    <label for="catalogue-{{ $property }}" class="block mb-1.5 text-sm font-medium text-heading dark:text-white">{{ $label }}</label>
                    <select
                        id="catalogue-{{ $property }}"
                        wire:model.live="{{ $property }}"
                        class="w-full bg-neutral-secondary-medium dark:bg-slate-900 border border-default-medium dark:border-slate-600 text-heading dark:text-white text-sm rounded-base px-3 py-2 focus:ring-brand focus:border-brand shadow-xs"
                    >
                        <option value="">{{ $t('catalogue.any') }}</option>
                        @foreach ($options as $option)
                            <option value="{{ $option->id }}">{{ $option->localized_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            <div>
                <label for="catalogue-sort" class="block mb-1.5 text-sm font-medium text-heading dark:text-white">{{ $t('catalogue.sort') }}</label>
                <select
                    id="catalogue-sort"
                    wire:model.live="sort"
                    class="w-full bg-neutral-secondary-medium dark:bg-slate-900 border border-default-medium dark:border-slate-600 text-heading dark:text-white text-sm rounded-base px-3 py-2 focus:ring-brand focus:border-brand shadow-xs"
                >
                    <option value="name">{{ $t('catalogue.sort_name') }}</option>
                    <option value="name_desc">{{ $t('catalogue.sort_name_desc') }}</option>
                </select>
            </div>
        </div>

        @if ($this->search !== '' || $this->dosageForm !== '' || $this->ingredient !== '' || $this->manufacturer !== '')
            <div class="mt-3 flex flex-wrap items-center gap-3">
                <span class="text-sm text-body dark:text-slate-400">{{ $t('catalogue.showing', ['count' => $this->products->total()]) }}</span>
                <button type="button" wire:click="resetFilters" class="text-sm font-medium text-fg-brand hover:underline">
                    {{ $t('catalogue.clear_filters') }}
                </button>
            </div>
        @endif
    </div>

    @if ($this->products->isEmpty())
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 p-10 text-center">
            <x-lucide-pill class="w-10 h-10 mx-auto mb-3 text-body dark:text-slate-500" />
            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $t('catalogue.empty_title') }}</h2>
            <p class="mt-1 text-sm text-body dark:text-slate-400">{{ $t('catalogue.empty_desc') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            @foreach ($this->products as $product)
                @php
                    $withdrawals = $this->withdrawalBySpecies($product);
                @endphp

                <article class="flex flex-col bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs border border-default-medium dark:border-slate-700 hover:border-brand hover:shadow-md transition-all duration-300">
                    <div class="p-5 border-b border-default-medium dark:border-slate-700">
                        <h3 class="text-base font-semibold text-heading dark:text-white">
                            <a href="{{ route('products.show', $product) }}" wire:navigate class="hover:text-fg-brand hover:underline">{{ $product->trade_name }}</a>
                        </h3>

                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @if ($product->dosageForm)
                                <span class="inline-flex items-center rounded-xs px-2 py-0.5 text-xs font-medium bg-neutral-secondary-soft dark:bg-slate-700 text-body dark:text-slate-300">
                                    {{ $product->dosageForm->localized_name }}
                                </span>
                            @endif

                            @foreach ($product->activeIngredients->take(3) as $ingredient)
                                <span class="inline-flex items-center rounded-xs px-2 py-0.5 text-xs font-medium bg-brand-soft text-fg-brand-strong">
                                    {{ $ingredient->localized_name }}@if (filled($ingredient->pivot->strength)) {{ $ingredient->pivot->strength }} {{ $ingredient->pivot->unit }}@endif
                                </span>
                            @endforeach

                            @if ($product->activeIngredients->count() > 3)
                                <span class="inline-flex items-center rounded-xs px-2 py-0.5 text-xs font-medium bg-neutral-secondary-soft dark:bg-slate-700 text-body dark:text-slate-300">
                                    +{{ $product->activeIngredients->count() - 3 }}
                                </span>
                            @endif
                        </div>

                        @if ($product->manufacturer->isNotEmpty())
                            <p class="mt-2 text-xs text-body dark:text-slate-400">
                                {{ $t('catalogue.manufacturer') }}: {{ $product->manufacturer->first()->localized_name }}
                            </p>
                        @endif
                    </div>

                    {{-- Withdrawal is never omitted and never blank. On a food-animal
                         page an absent box reads as "no restriction", which is the
                         one reading that can hurt somebody, so the gap is stated
                         in words instead. --}}
                    <div class="p-5 border-b border-default-medium dark:border-slate-700">
                        <h4 class="flex items-center gap-1.5 text-sm font-semibold text-heading dark:text-white">
                            <x-lucide-clock class="w-4 h-4" />
                            {{ $t('catalogue.withdrawal') }}
                        </h4>

                        @if ($withdrawals->isEmpty())
                            <p class="mt-1.5 flex items-start gap-1.5 text-sm text-fg-warning-strong dark:text-yellow-300">
                                <x-lucide-info class="w-4 h-4 mt-0.5 shrink-0" />
                                <span>{{ $t('catalogue.withdrawal_unrecorded') }}</span>
                            </p>
                        @else
                            <dl class="mt-1.5 space-y-1.5">
                                @foreach ($withdrawals as $species => $rows)
                                    <div>
                                        <dt class="text-xs font-semibold text-body dark:text-slate-300">{{ $species }}</dt>

                                        @foreach ($rows as $row)
                                            <dd class="text-xs text-body dark:text-slate-400">
                                                @if ($row->meat_days !== null)
                                                    {{ $t('catalogue.meat') }}: {{ $row->meat_days }} {{ $t('catalogue.days') }}
                                                @endif
                                                @if ($isPoultry && $row->egg_days !== null)
                                                    {{ $t('catalogue.egg') }}: {{ $row->egg_days }} {{ $t('catalogue.days') }}
                                                @endif
                                                @if (! $isPoultry && $row->milk_days !== null)
                                                    {{ $t('catalogue.milk') }}: {{ $row->milk_days }} {{ $t('catalogue.days') }}
                                                @endif
                                            </dd>
                                        @endforeach
                                    </div>
                                @endforeach
                            </dl>
                        @endif
                    </div>

                    <div class="p-5 border-b border-default-medium dark:border-slate-700">
                        <h4 class="flex items-center gap-1.5 text-sm font-semibold text-heading dark:text-white">
                            <x-lucide-syringe class="w-4 h-4" />
                            {{ $t('catalogue.dosage') }}
                        </h4>

                        {{-- The species is named on every row, because the whole reason the
                             product is on this page is that a dose exists for one of
                             these animals — "1 ml/50 kg" alone does not say for what. --}}
                        <ul class="mt-1.5 space-y-1.5">
                            @foreach ($product->dosages as $dose)
                                <li class="text-xs">
                                    <span class="font-semibold text-body dark:text-slate-300">{{ $dose->species?->localized_name }}</span>
                                    <span class="text-body dark:text-slate-400">{{ $dose->dosage }}</span>
                                    @if (filled($dose->route))
                                        <span class="text-body dark:text-slate-500">· {{ $dose->route }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="flex-1 p-5">
                        @if ($product->diseases->isNotEmpty())
                            <h4 class="text-sm font-semibold text-heading dark:text-white">{{ $t('catalogue.treats') }}</h4>
                            <ul class="mt-1.5 space-y-1">
                                @foreach ($product->diseases->take(4) as $disease)
                                    <li class="text-sm">
                                        <a href="{{ route($this->part . '.diseases.show', $disease->slug) }}" wire:navigate class="text-fg-brand hover:underline">
                                            {{ $disease->localized_name }}
                                        </a>
                                    </li>
                                @endforeach

                                @if ($product->diseases->count() > 4)
                                    <li class="text-xs text-body dark:text-slate-400">+{{ $product->diseases->count() - 4 }} {{ $t('catalogue.more') }}</li>
                                @endif
                            </ul>
                        @else
                            <p class="text-sm text-body dark:text-slate-400">{{ $t('catalogue.no_diseases') }}</p>
                        @endif
                    </div>

                    <div class="p-5 pt-0">
                        <a
                            href="{{ route('products.show', $product) }}"
                            wire:navigate
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-fg-brand hover:underline"
                        >
                            {{ $t('catalogue.view_product') }}
                            <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($this->products->hasPages())
            <div class="mt-6">
                {{ $this->products->links() }}
            </div>
        @endif
    @endif
</div>