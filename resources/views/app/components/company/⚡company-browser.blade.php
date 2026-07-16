<?php

use App\Models\Company;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    private const PER_PAGE = 21;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'letter')]
    public ?string $activeLetter = null;

    #[Url(as: 'type')]
    public ?string $activeType = null;

    #[Url]
    public ?int $page = null;

    public function filterByType(string $type): void
    {
        $this->activeType = $type === 'all' ? null : $type;
        $this->activeLetter = null;
        $this->search = '';
        $this->page = null;
    }

    public function filterByLetter(string $letter): void
    {
        $this->activeLetter = $letter;
        $this->search = '';
        $this->page = null;
    }

    public function clear(): void
    {
        $this->reset('search', 'activeLetter', 'activeType', 'page');
    }

    public function updatedSearch(): void
    {
        $this->activeType = null;
        $this->activeLetter = null;
        $this->page = null;
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
    }

    private function booleanQuery(string $search): string
    {
        $terms = preg_split('/\s+/', trim($search));
        $terms = array_filter($terms, fn ($t) => strlen($t) > 0);

        return implode(' ', array_map(fn ($t) => '+'.$t.'*', $terms));
    }

    private function filteredQuery()
    {
        $query = Company::query()->withCount('products');

        if ($this->activeType) {
            $query->where('company_type', $this->activeType);
        }

        if ($this->activeLetter) {
            $query->where('name', 'LIKE', $this->activeLetter.'%');
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereFullText(['name', 'name_ar'], $this->booleanQuery($this->search), ['mode' => 'boolean'])
                    ->orWhere('country', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('name');
    }

    #[Computed]
    public function totalCount(): int
    {
        $query = Company::query();

        if ($this->activeType) {
            $query->where('company_type', $this->activeType);
        }

        if ($this->activeLetter) {
            $query->where('name', 'LIKE', $this->activeLetter.'%');
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereFullText(['name', 'name_ar'], $this->booleanQuery($this->search), ['mode' => 'boolean'])
                    ->orWhere('country', 'like', "%{$this->search}%");
            });
        }

        return $query->count();
    }

    #[Computed]
    public function companies()
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
    public function availableLetters(): array
    {
        $query = Company::query();

        if ($this->activeType) {
            $query->where('company_type', $this->activeType);
        }

        return $query
            ->selectRaw('UPPER(LEFT(name, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter')
            ->toArray();
    }

    #[Computed]
    public function typeCounts(): array
    {
        $types = ['manufacturer', 'agent', 'distributor', 'marketing'];
        $counts = [];

        foreach ($types as $type) {
            $counts[$type] = Company::where('company_type', $type)->count();
        }

        return $counts;
    }

    private function typeIcon(string $type): string
    {
        return match ($type) {
            'manufacturer' => 'building-2',
            'agent' => 'handshake',
            'distributor' => 'truck',
            'marketing' => 'megaphone',
        };
    }
};
?>

<div class="space-y-4">
    {{-- Page hero --}}
    <x-page-hero
        :heading="__('messages.companies.index_heading')"
        :subtitle="__('messages.companies.index_subtitle')"
        :stats="[
            ['count' => number_format($this->totalCount), 'label' => __('messages.companies.companies_label'), 'icon' => 'building-2'],
        ]"
    />

    {{-- Search --}}
    <div class="bg-neutral-primary-soft rounded-full shadow-xs p-5 dark:bg-gray-800">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <x-lucide-search class="w-4 h-4 text-body" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-full focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="{{ __('messages.companies.search_placeholder') }}">
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" wire:click="clear"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-full hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 px-4 py-2.5">
                    <x-lucide-x class="w-4 h-4" />
                    {{ __('messages.companies.clear_filter') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Type filter (segmented control) --}}
    <div class="bg-neutral-primary-soft rounded-full shadow-xs p-4 dark:bg-gray-800">
        <div class="bg-neutral-secondary-soft p-1 rounded-full border border-default-medium inline-flex flex-wrap gap-0.5 w-full sm:w-auto">
            <button type="button" wire:click="filterByType('all')"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-full transition-colors @if(!$activeType) bg-brand text-white shadow-xs @else text-heading hover:bg-neutral-primary-soft dark:text-white dark:hover:bg-gray-700 @endif">
                <x-lucide-layers class="w-4 h-4" />
                {{ __('messages.companies.filter_all') }}
                <span class="text-xs @if(!$activeType) text-white/80 @else text-body @endif">({{ number_format(array_sum($this->typeCounts)) }})</span>
            </button>

            @foreach (['manufacturer', 'agent', 'distributor', 'marketing'] as $type)
                <button type="button" wire:click="filterByType('{{ $type }}')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-full transition-colors @if($activeType === $type) bg-brand text-white shadow-xs @else text-heading hover:bg-neutral-primary-soft dark:text-white dark:hover:bg-gray-700 @endif">
                    @php $icon = $this->typeIcon($type); @endphp
                    <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
                    {{ __('messages.companies.types.' . $type) }}
                    <span class="text-xs @if($activeType === $type) text-white/80 @else text-body @endif">({{ $this->typeCounts[$type] }})</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Letter navigation --}}
    @if ($this->availableLetters)
        <div class="bg-neutral-primary-soft rounded-full shadow-xs p-4 dark:bg-gray-800">
            <div class="flex flex-wrap items-center justify-center gap-1">
                @php $letters = range('A', 'Z'); @endphp
                @foreach ($letters as $letter)
                    @if (in_array($letter, $this->availableLetters))
                        @if ($activeLetter === $letter)
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand text-white text-sm font-bold cursor-default">{{ $letter }}</span>
                        @else
                            <button type="button" wire:click="filterByLetter('{{ $letter }}')"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-medium text-heading hover:bg-brand/10 hover:text-brand dark:text-white dark:hover:text-brand transition-colors">
                                {{ $letter }}
                            </button>
                        @endif
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm text-body/30 dark:text-gray-600 cursor-default">{{ $letter }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Results header --}}
    <div class="flex items-center justify-between">
        <div class="text-sm text-body dark:text-gray-400">
            @if ($activeLetter)
                {{ $this->companies->count() }} {{ __('messages.companies.of') }} {{ $this->totalCount }} {{ __('messages.companies.companies_label') }}
                {{ __('messages.companies.starting_with', ['letter' => $activeLetter]) }}
            @else
                {{ $this->companies->count() }} {{ __('messages.companies.of') }} {{ $this->totalCount }} {{ __('messages.companies.companies_label') }}
            @endif
        </div>
    </div>

    {{-- Card grid --}}
    @if ($this->companies->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($this->companies as $company)
                @php
                    $initial = strtoupper(substr($company->name, 0, 1));
                    $hasWebsite = $company->website !== null && $company->website !== '';
                @endphp
                <div class="group bg-white dark:bg-gray-800 rounded-xl border border-neutral-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col relative overflow-hidden">
                    {{-- Accent bar --}}
                    <div class="h-1 w-full bg-brand/40 group-hover:bg-brand absolute top-0 left-0 transition-all duration-300"></div>

                    <div class="p-5 pt-6 flex flex-col flex-1">
                        {{-- Avatar + Name row --}}
                        <div class="flex items-center gap-3">
                            <div class="shrink-0 w-10 h-10 rounded-full bg-brand/10 flex items-center justify-center text-brand font-bold text-base">
                                {{ $initial }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('companies.show', $company) }}" class="text-base font-semibold text-heading dark:text-white group-hover:text-brand transition-colors leading-snug block truncate">
                                    {{ $company->name }}
                                </a>
                                @if ($company->name_ar && app()->getLocale() === 'ar')
                                    <p class="text-xs text-body dark:text-gray-400 truncate">{{ $company->name_ar }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Type badge --}}
                        <div class="mt-2.5">
                            @php
                                $typeDotStyles = [
                                    'manufacturer' => 'bg-success',
                                    'agent' => 'bg-brand',
                                    'distributor' => 'bg-danger',
                                    'marketing' => 'bg-body',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-body dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full {{ $typeDotStyles[$company->company_type] ?? 'bg-brand' }}"></span>
                                {{ __('messages.companies.types.' . $company->company_type) }}
                            </span>
                        </div>

                        {{-- Metadata row --}}
                        <div class="mt-auto pt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-body dark:text-gray-400">
                            @if ($company->country)
                                <span class="inline-flex items-center gap-1">
                                    <x-lucide-map-pin class="w-3.5 h-3.5 shrink-0" />
                                    {{ $company->country }}
                                </span>
                                <span class="text-body/30 dark:text-gray-600">|</span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <x-lucide-package class="w-3.5 h-3.5 shrink-0" />
                                {{ $company->products_count }} {{ __('messages.companies.products_count') }}
                            </span>

                            @if ($hasWebsite)
                                <span class="text-body/30 dark:text-gray-600">|</span>
                                <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-fg-brand hover:underline">
                                    <x-lucide-globe class="w-3.5 h-3.5 shrink-0" />
                                    <span class="truncate max-w-[140px]">{{ parse_url($company->website, PHP_URL_HOST) ?: $company->website }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Action bar --}}
                    <a href="{{ route('companies.show', $company) }}" class="border-t border-default-medium dark:border-gray-700 px-5 py-2.5 flex items-center justify-between text-sm font-medium text-body hover:text-brand dark:text-gray-400 dark:hover:text-brand hover:bg-neutral-secondary-soft dark:hover:bg-gray-700/50 transition-colors">
                        <span class="flex items-center gap-2">
                            <x-lucide-eye class="w-4 h-4" />
                            {{ __('messages.companies.details') }}
                        </span>
                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($this->lastPage > 1)
            <div class="flex items-center justify-between bg-neutral-primary-soft rounded-full shadow-xs px-5 py-3 dark:bg-gray-800">
                <div class="text-sm text-body dark:text-gray-400">
                    {{ __('messages.companies.showing') }}
                    {{ (($this->currentPage - 1) * self::PER_PAGE) + 1 }}–{{ min($this->currentPage * self::PER_PAGE, $this->totalCount) }}
                    {{ __('messages.companies.of') }}
                    {{ $this->totalCount }}
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="goToPage({{ $this->currentPage - 1 }})" @if($this->currentPage === 1) disabled @endif
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-full hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                        <x-lucide-chevron-left class="w-4 h-4 rtl:rotate-180" />
                        <span class="ms-1">{{ __('messages.companies.previous_page') }}</span>
                    </button>

                    <button type="button" wire:click="goToPage({{ $this->currentPage + 1 }})" @if(!$this->hasMorePages) disabled @endif
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-full hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                        <span class="me-1">{{ __('messages.companies.next_page') }}</span>
                        <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    </button>
                </div>
            </div>
        @endif
    @else
        <div class="bg-neutral-primary-soft rounded-full shadow-xs dark:bg-gray-800 py-16 text-center">
            <x-lucide-building-2 class="w-12 h-12 text-body mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.companies.no_companies') }}</h3>
            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.companies.try_another') }}</p>
        </div>
    @endif
</div>
