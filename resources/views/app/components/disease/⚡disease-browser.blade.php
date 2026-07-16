<?php

use App\Models\Disease;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'letter')]
    public ?string $activeLetter = null;

    public function filterByLetter(string $letter): void
    {
        $this->activeLetter = $letter;
        $this->search = '';
    }

    public function clear(): void
    {
        $this->reset('search', 'activeLetter');
    }

    #[Computed]
    public function diseases()
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

        return $query->orderBy('name')->get();
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
            ['count' => number_format($this->diseases->count()), 'label' => __('messages.diseases.diseases_label'), 'icon' => 'activity'],
        ]"
    />

    {{-- Search --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-gray-800">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                    <x-lucide-search class="w-4 h-4 text-body" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="{{ __('messages.diseases.search_placeholder') }}">
            </div>
            <div class="flex justify-between gap-2">
                <button type="button" wire:click="clear"
                    class="flex-1 inline-flex items-center justify-center gap-2 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 px-4 py-2.5">
                    <x-lucide-x class="w-4 h-4" />
                    {{ __('messages.diseases.clear_filter') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Letter navigation --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-4 dark:bg-gray-800">
        <div class="flex flex-wrap items-center justify-center gap-1">
            @php $letters = range('A', 'Z'); @endphp
            @foreach ($letters as $letter)
                @if (in_array($letter, $this->availableLetters))
                    @if ($activeLetter === $letter)
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-base bg-brand text-white text-sm font-bold cursor-default">{{ $letter }}</span>
                    @else
                        <button type="button" wire:click="filterByLetter('{{ $letter }}')"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-base text-base font-medium text-heading hover:bg-brand/10 hover:text-brand dark:text-white dark:hover:text-brand transition-colors">
                            {{ $letter }}
                        </button>
                    @endif
                @else
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-base text-sm text-body/30 dark:text-gray-600 cursor-default">{{ $letter }}</span>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Results header --}}
    <div class="flex items-center justify-between">
        <div class="text-sm text-body dark:text-gray-400">
            @if ($activeLetter)
                {{ __('messages.diseases.showing') }} {{ $this->diseases->count() }} {{ __('messages.diseases.diseases_label') }}
                {{ __('messages.diseases.starting_with', ['letter' => $activeLetter]) }}
            @else
                {{ __('messages.diseases.showing') }} {{ $this->diseases->count() }} {{ __('messages.diseases.diseases_label') }}
            @endif
        </div>
    </div>

    {{-- Card grid --}}
    @if ($this->diseases->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($this->diseases as $disease)
                <div class="group bg-white dark:bg-gray-800 rounded-xl border border-neutral-200 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col relative overflow-hidden">
                    <div class="h-1 w-full bg-brand/40 group-hover:bg-brand absolute top-0 left-0 transition-all duration-300"></div>

                    <div class="p-5 pt-6 flex flex-col flex-1">
                        <a href="{{ route('diseases.show', $disease) }}" class="text-lg font-semibold text-heading dark:text-white group-hover:text-brand transition-colors leading-tight">
                            {{ $disease->name }}
                        </a>

                        @if ($disease->name_ar && app()->getLocale() === 'ar')
                            <p class="text-sm text-body dark:text-gray-400 mt-1">{{ $disease->name_ar }}</p>
                        @endif

                        <div class="mt-auto pt-4 flex items-center gap-1.5 text-sm text-body dark:text-gray-400">
                            <x-lucide-activity class="w-4 h-4" />
                            <span>{{ $disease->products_count }} {{ __('messages.diseases.products_count') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('diseases.show', $disease) }}" class="border-t border-default-medium dark:border-gray-700 px-5 py-3 flex items-center justify-between text-sm font-medium text-body hover:text-brand dark:text-gray-400 dark:hover:text-brand hover:bg-neutral-secondary-soft dark:hover:bg-gray-700/50 transition-colors">
                        <span class="flex items-center gap-2">
                            <x-lucide-eye class="w-4 h-4" />
                            {{ __('messages.diseases.details') }}
                        </span>
                        <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 transition-transform" />
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-gray-800 py-16 text-center">
            <x-lucide-activity class="w-12 h-12 text-body mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-heading dark:text-white mb-1">{{ __('messages.diseases.no_diseases') }}</h3>
            <p class="text-sm text-body dark:text-gray-400">{{ __('messages.diseases.try_another') }}</p>
        </div>
    @endif
</div>
