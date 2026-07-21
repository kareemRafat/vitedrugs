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
                    <div class="group bg-white dark:bg-slate-800 rounded-xl border border-neutral-200 dark:border-slate-700 shadow-sm transition-all duration-300 flex flex-col relative overflow-hidden">
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
                        <div class="border-t border-default-medium dark:border-slate-700 px-5 py-3.5 flex items-center justify-between">
                            <a href="{{ route('products.show', $product) }}" class="flex items-center gap-2 text-sm font-medium text-brand hover:text-brand-strong dark:text-sky-400 dark:hover:text-sky-300 hover:bg-brand/5 dark:hover:bg-slate-700/50 transition-all group/action">
                                <x-lucide-eye class="w-4 h-4" />
                                {{ __('messages.products.details') }}
                                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover/action:translate-x-1 transition-transform" />
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
