<?php

use App\Models\Company;
use App\Models\DosageForm;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    private const PER_PAGE = 21;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'company')]
    public string $company = '';

    #[Url(as: 'dosage_form')]
    public string $dosageForm = '';

    #[Url(as: 'sort')]
    public string $sort = 'latest';

    #[Url]
    public ?int $page = null;

    public function search(): void
    {
        $this->page = null;
    }

    public function clear(): void
    {
        $this->reset('search', 'company', 'dosageForm', 'sort', 'page');
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
    }

    private function filteredQuery()
    {
        $query = Product::query()
            ->with([
                'manufacturer',
                'dosageForm',
                'activeIngredients',
            ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereFullText(['trade_name', 'trade_name_ar'], $this->search . '*', ['mode' => 'boolean'])
                    ->orWhereHas('activeIngredients', fn ($q) => $q->whereFullText(['name', 'name_ar'], $this->search . '*', ['mode' => 'boolean']))
                    ->orWhereHas('companies', fn ($q) => $q->whereFullText(['name', 'name_ar'], $this->search . '*', ['mode' => 'boolean']))
                    ->orWhereHas('diseases', fn ($q) => $q->whereFullText(['name', 'name_ar'], $this->search . '*', ['mode' => 'boolean']));
            });
        }

        if ($this->company) {
            $query->whereHas('companies', fn ($q) => $q->where('companies.id', $this->company));
        }

        if ($this->dosageForm) {
            $query->where('dosage_form_id', $this->dosageForm);
        }

        switch ($this->sort) {
            case 'name':
                $query->orderBy('trade_name');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        return $query;
    }

    #[Computed]
    public function totalCount(): int
    {
        return $this->filteredQuery()->count();
    }

    #[Computed]
    public function products()
    {
        $currentPage = max(1, $this->page ?? 1);

        return $this->filteredQuery()
            ->forPage($currentPage, self::PER_PAGE)
            ->get();
    }

    #[Computed]
    public function hasMorePages(): bool
    {
        return $this->totalCount > (max(1, $this->page ?? 1) * self::PER_PAGE);
    }

    #[Computed]
    public function currentPage(): int
    {
        return max(1, $this->page ?? 1);
    }

    #[Computed]
    public function lastPage(): int
    {
        return (int) ceil($this->totalCount / self::PER_PAGE);
    }

    #[Computed]
    public function companies()
    {
        return Company::query()->orderBy('name')->get();
    }

    #[Computed]
    public function dosageForms()
    {
        return DosageForm::query()->orderBy('name')->get();
    }
};
?>

<div class="space-y-4">
    <x-page-hero
        :heading="__('messages.products.index_heading')"
        :subtitle="__('messages.products.index_subtitle')"
        :stats="[
            ['count' => number_format($this->totalCount), 'label' => __('messages.products.products_label'), 'icon' => 'package'],
            ['count' => $this->companies->count(), 'label' => __('messages.products.companies_label'), 'icon' => 'building-2'],
            ['count' => $this->dosageForms->count(), 'label' => __('messages.products.forms_label'), 'icon' => 'pill'],
        ]">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('products.submission.create') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-base text-sm font-medium bg-white/20 text-white hover:bg-white/30 transition-colors shadow-xs border border-white/30">
                <x-lucide-plus class="w-3.5 h-3.5" />
                {{ __('messages.products.submit_product') }}
            </a>
            <a href="{{ route('products.compare') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-base text-sm font-medium bg-white text-brand hover:bg-white/90 transition-colors shadow-xs border border-white/20">
                <x-lucide-git-compare class="w-4 h-4" />
                {{ __('messages.compare.compare') }}
            </a>
        </div>
    </x-page-hero>

    {{-- Search / Filters --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
        <form wire:submit.prevent="search">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-3">
            <div class="lg:col-span-1 relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                </div>
                <input type="text" wire:model="search" wire:keydown.enter="search"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                    placeholder="{{ __('messages.products.search_placeholder') }}">
            </div>

            <div x-data="{ open: window.innerWidth >= 640 }"
                 x-init="if (window.innerWidth < 640) open = false"
                 x-bind:class="open ? 'contents' : 'flex flex-col gap-4'"
                 class="sm:contents max-sm:flex max-sm:flex-col max-sm:gap-4">
                <select wire:model="company"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    <option value="">{{ __('messages.products.all_companies') }}</option>
                    @foreach ($this->companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>

                <select wire:model="dosageForm"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    <option value="">{{ __('messages.products.all_forms') }}</option>
                    @foreach ($this->dosageForms as $form)
                        <option value="{{ $form->id }}">{{ $form->name }}</option>
                    @endforeach
                </select>

                <select wire:model="sort"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    <option value="latest">{{ __('messages.products.sort_latest') }}</option>
                    <option value="name">{{ __('messages.products.sort_name') }}</option>
                    <option value="oldest">{{ __('messages.products.sort_oldest') }}</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                    <x-lucide-search class="w-4 h-4 inline -mt-0.5 me-1" />
                    {{ __('messages.products.search_button') }}
                </button>
                <button type="button" wire:click="clear"
                    class="inline-flex items-center justify-center gap-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 px-4 py-2.5">
                    <x-lucide-x class="w-4 h-4" />
                    {{ __('messages.products.clear_filter') }}
                </button>
            </div>
        </div>
        </form>

        <button type="button" x-on:click="open = !open"
            class="max-sm:flex sm:hidden mt-3 w-full items-center justify-center gap-1.5 text-sm text-fg-brand hover:underline cursor-pointer"
            x-bind:class="open ? 'flex' : 'flex'">
            <span x-text="open ? '{{ __('messages.products.hide_filters') }}' : '{{ __('messages.products.advanced_search') }}'"></span>
            <x-lucide-chevron-down class="w-4 h-4 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
        </button>
    </div>

    {{-- Products Grid --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800">
        <div class="px-5 py-4 border-b border-default-medium dark:border-slate-700 flex items-center justify-between">
            <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.products.directory_title') }}</h2>
            <span class="text-sm text-body dark:text-slate-400">
                {{ __('messages.products.showing') }}
                {{ (($this->currentPage - 1) * self::PER_PAGE) + 1 }}–{{ min($this->currentPage * self::PER_PAGE, $this->totalCount) }}
                {{ __('messages.products.of') }}
                {{ number_format($this->totalCount) }}
            </span>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($this->products as $product)
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
                            <a href="{{ route('products.show', $product) }}" class="block min-h-[3rem] group/title">
                                <h3 class="text-lg sm:text-xl font-extrabold text-heading dark:text-white group-hover/title:text-brand dark:group-hover/title:text-sky-400 transition-colors duration-200 leading-snug line-clamp-2">
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
                            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-brand hover:text-white bg-brand/10 hover:bg-brand dark:bg-sky-400/10 dark:text-sky-400 dark:hover:bg-sky-500 dark:hover:text-white transition-all duration-200 shadow-2xs group/action">
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

    {{-- Pagination --}}
    @if ($this->lastPage > 1)
        <div class="flex items-center justify-between bg-neutral-primary-soft rounded-base shadow-xs px-5 py-3 dark:bg-slate-800">
            <div class="text-sm text-body dark:text-slate-400">
                {{ __('messages.products.showing') }}
                {{ (($this->currentPage - 1) * self::PER_PAGE) + 1 }}–{{ min($this->currentPage * self::PER_PAGE, $this->totalCount) }}
                {{ __('messages.products.of') }}
                {{ $this->totalCount }}
            </div>

            <div class="flex items-center gap-2">
                <button type="button" wire:click="goToPage({{ $this->currentPage - 1 }})" @if($this->currentPage === 1) disabled @endif
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                    <x-lucide-chevron-left class="w-4 h-4 rtl:rotate-180" />
                    <span class="ms-1">{{ __('messages.products.previous_page') }}</span>
                </button>

                <button type="button" wire:click="goToPage({{ $this->currentPage + 1 }})" @if(!$this->hasMorePages) disabled @endif
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                    <span class="me-1">{{ __('messages.products.next_page') }}</span>
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                </button>
            </div>
        </div>
    @endif
</div>
