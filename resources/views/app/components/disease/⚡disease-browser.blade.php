<?php

use App\Models\Disease;
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

    #[Url]
    public ?int $page = null;

    public function filterByLetter(string $letter): void
    {
        $this->activeLetter = $letter;
        $this->search = '';
        $this->page = null;
    }

    public function clear(): void
    {
        $this->reset('search', 'activeLetter', 'page');
    }

    public function updatedSearch(): void
    {
        $this->page = null;
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
    }

    private function filteredQuery()
    {
        $query = Disease::query()->withCount('products');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereFullText(['name', 'name_ar'], $this->search);
            });
        }

        if ($this->activeLetter) {
            $query->where('name', 'LIKE', $this->activeLetter.'%');
        }

        return $query->orderBy('name');
    }

    #[Computed]
    public function totalCount(): int
    {
        $query = Disease::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereFullText(['name', 'name_ar'], $this->search);
            });
        }

        if ($this->activeLetter) {
            $query->where('name', 'LIKE', $this->activeLetter.'%');
        }

        return $query->count();
    }

    #[Computed]
    public function diseases()
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
        return Disease::query()
            ->selectRaw('UPPER(LEFT(name, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter')
            ->toArray();
    }
};
?>

<div class="space-y-4">
    {{-- Page hero --}}
    <x-page-hero
        :heading="__('messages.diseases.index_heading')"
        :subtitle="__('messages.diseases.index_subtitle')"
        :stats="[
            ['count' => number_format($this->totalCount), 'label' => __('messages.diseases.diseases_label'), 'icon' => 'activity'],
        ]"
    />

    {{-- Search --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:placeholder:text-slate-400 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                    placeholder="{{ __('messages.diseases.search_placeholder') }}">
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" wire:click="clear"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 px-4 py-2.5">
                    <x-lucide-x class="w-4 h-4" />
                    {{ __('messages.diseases.clear_filter') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Letter navigation --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-slate-800">
        <div class="flex flex-wrap items-center justify-center gap-1">
            @php $letters = range('A', 'Z'); @endphp
            @foreach ($letters as $letter)
                @if (in_array($letter, $this->availableLetters))
                    @if ($activeLetter === $letter)
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-base bg-brand text-white text-sm font-bold cursor-default">{{ $letter }}</span>
                    @else
                        <button type="button" wire:click="filterByLetter('{{ $letter }}')"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-base text-sm font-medium text-heading hover:bg-brand/10 hover:text-brand dark:text-white dark:hover:text-brand transition-colors">
                            {{ $letter }}
                        </button>
                    @endif
                @else
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-base text-sm text-body/30 dark:text-slate-600 cursor-default">{{ $letter }}</span>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Results header --}}
    <div class="flex items-center justify-between">
        <div class="text-sm text-body dark:text-slate-400">
            @if ($activeLetter)
                {{ $this->diseases->count() }} {{ __('messages.diseases.of') }} {{ $this->totalCount }} {{ __('messages.diseases.diseases_label') }}
                {{ __('messages.diseases.starting_with', ['letter' => $activeLetter]) }}
            @else
                {{ $this->diseases->count() }} {{ __('messages.diseases.of') }} {{ $this->totalCount }} {{ __('messages.diseases.diseases_label') }}
            @endif
        </div>
    </div>

    {{-- Card grid --}}
    @if ($this->diseases->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($this->diseases as $disease)
                <div class="group bg-white dark:bg-slate-800 rounded-xl border border-neutral-200 dark:border-slate-700 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col relative overflow-hidden">
                    <div class="h-1 w-full bg-brand/40 group-hover:bg-brand absolute top-0 left-0 transition-all duration-300"></div>

                    <div class="p-5 pt-6 flex flex-col flex-1">
                        <a href="{{ route('diseases.show', $disease) }}" class="text-lg font-semibold text-heading dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors leading-tight">
                            {{ $disease->name }}
                        </a>

                        @if ($disease->name_ar && app()->getLocale() === 'ar')
                            <p class="text-sm text-body dark:text-slate-400 mt-1">{{ $disease->name_ar }}</p>
                        @endif

                        <div class="mt-auto pt-4 flex items-center gap-1.5 text-sm text-body dark:text-slate-400">
                            <x-lucide-activity class="w-4 h-4" />
                            <span>{{ $disease->products_count }} {{ __('messages.diseases.products_count') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('diseases.show', $disease) }}" class="border-t border-default-medium dark:border-slate-700 px-5 py-3 flex items-center justify-between text-sm font-medium text-body hover:text-brand dark:text-slate-400 dark:hover:text-brand hover:bg-neutral-secondary-soft dark:hover:bg-slate-700/50 transition-colors">
                        <span class="flex items-center gap-2">
                            <x-lucide-eye class="w-4 h-4" />
                            {{ __('messages.diseases.details') }}
                        </span>
                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($this->lastPage > 1)
            <div class="flex items-center justify-between bg-neutral-primary-soft rounded-base shadow-xs px-5 py-3 dark:bg-slate-800">
                <div class="text-sm text-body dark:text-slate-400">
                    {{ __('messages.diseases.showing') }}
                    {{ (($this->currentPage - 1) * self::PER_PAGE) + 1 }}–{{ min($this->currentPage * self::PER_PAGE, $this->totalCount) }}
                    {{ __('messages.diseases.of') }}
                    {{ $this->totalCount }}
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="goToPage({{ $this->currentPage - 1 }})" @if($this->currentPage === 1) disabled @endif
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                        <x-lucide-chevron-left class="w-4 h-4 rtl:rotate-180" />
                        <span class="ms-1">{{ __('messages.diseases.previous_page') }}</span>
                    </button>

                    <button type="button" wire:click="goToPage({{ $this->currentPage + 1 }})" @if(!$this->hasMorePages) disabled @endif
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft disabled:opacity-40 disabled:cursor-not-allowed dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600 transition-colors">
                        <span class="me-1">{{ __('messages.diseases.next_page') }}</span>
                        <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    </button>
                </div>
            </div>
        @endif
    @else
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 py-16 text-center">
            <x-lucide-activity class="w-12 h-12 text-body dark:text-slate-400 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.diseases.no_diseases') }}</h3>
            <p class="text-sm text-body dark:text-slate-400">{{ __('messages.diseases.try_another') }}</p>
        </div>
    @endif
</div>
