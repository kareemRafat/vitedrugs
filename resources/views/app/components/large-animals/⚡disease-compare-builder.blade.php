<?php

use App\Models\Disease;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    private const PER_COLUMN = 10;

    #[Url(as: 'disease1')]
    public ?string $diseaseId1 = null;

    #[Url(as: 'disease2')]
    public ?string $diseaseId2 = null;

    #[Url(as: 'disease3')]
    public ?string $diseaseId3 = null;

    #[Url(as: 'disease4')]
    public ?string $diseaseId4 = null;

    public string $search1 = '';
    public string $search2 = '';
    public string $search3 = '';
    public string $search4 = '';

    public function selectDisease(int $column, string $id): void
    {
        $this->{"diseaseId{$column}"} = $id;
        $this->{"search{$column}"} = '';
    }

    public function removeDisease(int $column): void
    {
        $this->{"diseaseId{$column}"} = null;
    }

    public function clearAll(): void
    {
        foreach ([1, 2, 3, 4] as $column) {
            $this->{"diseaseId{$column}"} = null;
        }
    }

    private function searchQuery(int $column)
    {
        $search = trim($this->{"search{$column}"});

        if ($search === '') {
            return collect();
        }

        return Disease::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('name_ar', 'like', "%{$search}%"))
            ->whereNotIn('id', $this->selectedIds())
            ->orderBy('name')
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

    #[Computed]
    public function searchResults4()
    {
        return $this->searchQuery(4);
    }

    private function selectedIds(): array
    {
        return array_values(array_filter([
            $this->diseaseId1,
            $this->diseaseId2,
            $this->diseaseId3,
            $this->diseaseId4,
        ], fn (?string $id): bool => filled($id)));
    }

    private function loadDisease(?string $id): ?Disease
    {
        if (! filled($id)) {
            return null;
        }

        return Disease::query()
            ->where('id', $id)
            ->with(['diseaseClassification', 'hostSpecies', 'microorganisms', 'clinicalSigns'])
            ->first();
    }

    #[Computed]
    public function disease1(): ?Disease
    {
        return $this->loadDisease($this->diseaseId1);
    }

    #[Computed]
    public function disease2(): ?Disease
    {
        return $this->loadDisease($this->diseaseId2);
    }

    #[Computed]
    public function disease3(): ?Disease
    {
        return $this->loadDisease($this->diseaseId3);
    }

    #[Computed]
    public function disease4(): ?Disease
    {
        return $this->loadDisease($this->diseaseId4);
    }

    #[Computed]
    public function selectedDiseases(): array
    {
        return array_values(array_filter([
            $this->disease1,
            $this->disease2,
            $this->disease3,
            $this->disease4,
        ], fn (?Disease $disease): bool => filled($disease)));
    }

    #[Computed]
    public function totalCount(): int
    {
        return count($this->selectedDiseases);
    }

    private function diseaseItems(Disease $disease, string $path, string $field): array
    {
        $payload = is_array($disease->knowledge_payload) ? $disease->knowledge_payload : [];

        return collect(data_get($payload, $path, []))
            ->pluck($field)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function buildPresenceMatrix(string $path, string $field): array
    {
        $diseases = $this->selectedDiseases;

        $allItems = collect();

        foreach ($diseases as $disease) {
            $allItems = $allItems->merge($this->diseaseItems($disease, $path, $field));
        }

        $allItems = $allItems->unique()->sort()->values();

        return $allItems->map(function (string $item) use ($diseases, $path, $field) {
            $presence = [];

            foreach ($diseases as $disease) {
                $presence[$disease->id] = in_array($item, $this->diseaseItems($disease, $path, $field), true);
            }

            return [
                'name' => $item,
                'presence' => $presence,
                'differs' => count(array_unique($presence)) > 1,
            ];
        })->all();
    }

    #[Computed]
    public function overviewMatrix(): array
    {
        return [
            $this->buildValueRow('etiology', fn (Disease $disease) => $disease->diseaseClassification?->etiology_type),
            $this->buildValueRow('host_species', function (Disease $disease) {
                return $disease->hostSpecies
                    ->pluck('display_name')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->implode(', ');
            }),
            $this->buildValueRow('microorganisms', function (Disease $disease) {
                return $disease->microorganisms
                    ->map(fn ($microorganism) => $microorganism->display_name ?? $microorganism->name)
                    ->filter()
                    ->unique()
                    ->sort()
                    ->implode(', ');
            }),
            $this->buildBooleanRow('zoonotic', fn (Disease $disease) => (bool) $disease->diseaseClassification?->zoonotic),
            $this->buildBooleanRow('notifiable', fn (Disease $disease) => (bool) $disease->diseaseClassification?->notifiable),
            $this->buildValueRow('oie_category', fn (Disease $disease) => $disease->diseaseClassification?->oie_category),
        ];
    }

    private function buildValueRow(string $label, Closure $value): array
    {
        $values = [];

        foreach ($this->selectedDiseases as $disease) {
            $values[$disease->id] = (string) $value($disease);
        }

        return [
            'name' => $label,
            'type' => 'value',
            'values' => $values,
            'differs' => count(array_unique($values)) > 1,
        ];
    }

    private function buildBooleanRow(string $label, Closure $value): array
    {
        $values = [];

        foreach ($this->selectedDiseases as $disease) {
            $values[$disease->id] = (bool) $value($disease);
        }

        return [
            'name' => $label,
            'type' => 'boolean',
            'values' => $values,
            'differs' => count(array_unique($values)) > 1,
        ];
    }

    #[Computed]
    public function clinicalSignsMatrix(): array
    {
        return $this->buildPresenceMatrix('clinical_signs', 'canonical_name');
    }

    #[Computed]
    public function postmortemMatrix(): array
    {
        return $this->buildPresenceMatrix('postmortem_findings', 'canonical_name');
    }

    #[Computed]
    public function diagnosisMatrix(): array
    {
        return $this->buildPresenceMatrix('diagnosis', 'method');
    }

    #[Computed]
    public function treatmentMatrix(): array
    {
        return $this->buildPresenceMatrix('treatment', 'intervention');
    }

    #[Computed]
    public function preventionMatrix(): array
    {
        return $this->buildPresenceMatrix('prevention_control', 'measure');
    }

    #[Computed]
    public function referencesMatrix(): array
    {
        return $this->buildPresenceMatrix('references', 'title');
    }
};
?>

<div class="space-y-6">
    <x-large-animals.page-hero
        :heading="__('large-animals.comparison.heading')"
        :subtitle="__('large-animals.comparison.subtitle')"
        :badge="__('large-animals.hero.badge.comparison')"
        badgeIcon="columns-3"
        :stats="[
            ['count' => $this->totalCount, 'label' => __('large-animals.comparison.selected'), 'icon' => 'columns-3'],
        ]"
    />

    {{-- Column pickers --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6">
        @foreach ([1, 2, 3, 4] as $col)
            @php
                $diseaseProp = "disease{$col}";
                $searchProp = "search{$col}";
                $resultsProp = "searchResults{$col}";
                $disease = $this->$diseaseProp;
            @endphp
            <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs flex flex-col">
                <div class="p-4 border-b border-default-medium dark:border-slate-700">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="{{ $searchProp }}"
                            placeholder="{{ __('large-animals.comparison.search_placeholder') }}"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-9 px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    </div>

                    <div wire:key="disease-results-{{ $col }}" class="relative mt-1">
                        @if ($this->$searchProp && $this->$resultsProp->isNotEmpty())
                            <div class="absolute z-10 w-full bg-white dark:bg-slate-700 border border-default-medium dark:border-slate-600 rounded-base shadow-lg max-h-60 overflow-y-auto">
                                @foreach ($this->$resultsProp as $result)
                                    <button type="button" wire:click="selectDisease({{ $col }}, '{{ $result->id }}')"
                                        class="w-full text-left px-3 py-2.5 hover:bg-neutral-secondary-soft dark:hover:bg-slate-600 border-b border-default-medium dark:border-slate-600 last:border-b-0 transition-colors">
                                        <span class="text-sm font-medium text-heading dark:text-white block">{{ $result->name }}</span>
                                        @if ($result->name_ar)
                                            <span class="text-xs text-body dark:text-slate-400" dir="rtl">{{ $result->name_ar }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    @if ($disease)
                        <div class="relative flex gap-3">
                            <button type="button" wire:click="removeDisease({{ $col }})"
                                class="absolute -top-1.5 -end-1.5 z-10 w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/50 text-red-500 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800 flex items-center justify-center transition-colors">
                                <x-lucide-x class="w-4 h-4" />
                            </button>

                            <div class="w-12 h-12 shrink-0 rounded-base bg-brand/10 dark:bg-brand/20 border border-brand/20 dark:border-brand/30 flex items-center justify-center">
                                <x-lucide-activity class="w-6 h-6 text-brand dark:text-brand" />
                            </div>

                            <div class="flex-1 min-w-0 pe-6">
                                <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate
                                    class="text-sm font-semibold text-heading dark:text-white hover:text-brand dark:hover:text-sky-400 transition-colors leading-tight line-clamp-2">
                                    {{ $disease->name }}
                                </a>
                                @if ($disease->name_ar)
                                    <p class="text-xs text-body dark:text-slate-400 mt-0.5 truncate" dir="rtl">{{ $disease->name_ar }}</p>
                                @endif

                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @if ($disease->diseaseClassification?->etiology_type)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand/15 text-brand-strong dark:bg-brand/25 dark:text-brand">
                                            {{ $disease->diseaseClassification->etiology_type }}
                                        </span>
                                    @endif
                                    @if ($disease->diseaseClassification?->zoonotic)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-danger-soft text-fg-danger-strong dark:bg-red-950 dark:text-red-300">
                                            {{ __('large-animals.disease.zoonotic') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs text-body dark:text-slate-400 mt-1 space-y-0.5">
                                    <p>{{ $disease->hostSpecies->count() }} {{ __('large-animals.hero.stats.species') }}</p>
                                    <p>{{ $disease->clinicalSigns->count() }} {{ __('large-animals.hero.stats.clinical_signs') }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <div class="text-center">
                                <x-lucide-search class="w-10 h-10 text-body/40 dark:text-slate-500 mx-auto mb-2" />
                                <p class="text-sm text-body/60 dark:text-slate-500">
                                    {{ __('large-animals.comparison.search_to_add') }}
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
                <span wire:loading.remove wire:target="clearAll">{{ __('large-animals.comparison.clear_all') }}</span>
                <span wire:loading wire:target="clearAll">{{ __('large-animals.comparison.clearing') }}</span>
            </button>
        </div>
    @endif

    {{-- Comparison results --}}
    @php
        $comparisonTabs = collect([
            ['id' => 'overview', 'label' => __('large-animals.comparison.overview'), 'icon' => 'clipboard-list', 'type' => 'overview', 'rows' => $this->overviewMatrix],
            ['id' => 'clinical_signs', 'label' => __('large-animals.comparison.clinical_signs'), 'icon' => 'stethoscope', 'type' => 'presence', 'rows' => $this->clinicalSignsMatrix],
            ['id' => 'postmortem_findings', 'label' => __('large-animals.comparison.postmortem_findings'), 'icon' => 'microscope', 'type' => 'presence', 'rows' => $this->postmortemMatrix],
            ['id' => 'diagnosis', 'label' => __('large-animals.comparison.diagnosis'), 'icon' => 'test-tube-2', 'type' => 'presence', 'rows' => $this->diagnosisMatrix],
            ['id' => 'treatment', 'label' => __('large-animals.comparison.treatment'), 'icon' => 'syringe', 'type' => 'presence', 'rows' => $this->treatmentMatrix],
            ['id' => 'prevention', 'label' => __('large-animals.comparison.prevention'), 'icon' => 'shield-check', 'type' => 'presence', 'rows' => $this->preventionMatrix],
            ['id' => 'references', 'label' => __('large-animals.comparison.references'), 'icon' => 'book-marked', 'type' => 'presence', 'rows' => $this->referencesMatrix],
        ])
            ->filter(fn (array $tab) => count($tab['rows']) > 0)
            ->map(function (array $tab) {
                $tab['rowCount'] = count($tab['rows']);
                $tab['diffCount'] = collect($tab['rows'])->where('differs', true)->count();

                return $tab;
            })
            ->values()
            ->all();

        $defaultTab = $comparisonTabs[0]['id'] ?? 'overview';
    @endphp

    @if ($this->totalCount >= 2 && count($comparisonTabs) > 0)
        <div x-data="{ active: '{{ $defaultTab }}', diffOnly: false }" class="space-y-4">

            {{-- Tab bar + differences toggle --}}
            <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs p-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap gap-1" role="tablist">
                        @foreach ($comparisonTabs as $tab)
                            <button type="button" role="tab"
                                x-on:click="active = '{{ $tab['id'] }}'"
                                x-bind:class="active === '{{ $tab['id'] }}'
                                    ? 'bg-brand text-white shadow-xs'
                                    : 'text-body hover:bg-neutral-secondary-soft dark:hover:bg-slate-700 dark:text-slate-300'"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-base text-sm font-medium transition-colors">
                                <x-dynamic-component :component="'lucide-' . $tab['icon']" class="w-4 h-4" />
                                <span>{{ $tab['label'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" x-model="diffOnly"
                            class="w-4 h-4 rounded-xs text-brand focus:ring-2 focus:ring-brand-soft dark:bg-slate-600 dark:border-slate-500">
                        <span class="text-sm font-medium text-heading dark:text-white">{{ __('large-animals.comparison.show_differences_only') }}</span>
                    </label>
                </div>
            </div>

            {{-- Tab panels --}}
            @foreach ($comparisonTabs as $tab)
                <div x-show="active === '{{ $tab['id'] }}'" x-cloak class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <x-dynamic-component :component="'lucide-' . $tab['icon']" class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $tab['label'] }}</h2>
                        </div>
                        <span class="text-xs text-body dark:text-slate-400">
                            <span x-show="!diffOnly">{{ $tab['rowCount'] }} {{ __('large-animals.comparison.items') }}</span>
                            <span x-show="diffOnly" x-cloak>{{ $tab['diffCount'] }} {{ __('large-animals.comparison.different_items') }}</span>
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right [&_th:not(:first-child)]:border-s [&_th:not(:first-child)]:border-default-medium dark:[&_th:not(:first-child)]:border-slate-700 [&_td:not(:first-child)]:border-s [&_td:not(:first-child)]:border-default-medium dark:[&_td:not(:first-child)]:border-slate-700">
                            <thead class="bg-neutral-secondary-soft dark:bg-slate-700">
                                <tr>
                                    <th class="px-5 py-4 text-xs uppercase tracking-wider text-body dark:text-slate-400 font-semibold w-48">
                                        {{ __('large-animals.comparison.attribute') }}
                                    </th>
                                    @foreach ($this->selectedDiseases as $disease)
                                        <th class="px-5 py-4 min-w-[200px] text-sm font-bold text-heading dark:text-white">
                                            <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate class="hover:text-brand dark:hover:text-sky-400 transition-colors">{{ $disease->name }}</a>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-default-medium dark:divide-slate-700">
                                @foreach ($tab['rows'] as $item)
                                    <tr x-show="!diffOnly || @js($item['differs'])">
                                        @if ($tab['type'] === 'overview')
                                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                                {{ __('large-animals.comparison.' . $item['name']) }}
                                            </td>
                                            @foreach ($this->selectedDiseases as $disease)
                                                <td class="px-5 py-4">
                                                    @if ($item['type'] === 'boolean')
                                                        <span class="text-sm font-medium text-heading dark:text-white">
                                                            {{ ($item['values'][$disease->id] ?? false) ? __('large-animals.disease.yes') : __('large-animals.disease.no') }}
                                                        </span>
                                                    @else
                                                        <span class="text-sm text-heading dark:text-white">{{ ($item['values'][$disease->id] ?? '') ?: '—' }}</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @else
                                            <td class="px-5 py-4 text-sm font-medium text-heading dark:text-white bg-neutral-secondary-soft/50 dark:bg-slate-700/50">
                                                {{ $item['name'] }}
                                            </td>
                                            @foreach ($this->selectedDiseases as $disease)
                                                <td class="px-5 py-4">
                                                    @if ($item['presence'][$disease->id] ?? false)
                                                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-success-strong dark:text-emerald-400" title="{{ __('large-animals.comparison.present') }}">
                                                            <x-lucide-check-circle class="w-4 h-4" />
                                                            {{ __('large-animals.comparison.present') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 text-sm text-body/60 dark:text-slate-500" title="{{ __('large-animals.comparison.absent') }}">
                                                            <x-lucide-minus-circle class="w-4 h-4" />
                                                            {{ __('large-animals.comparison.absent') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p x-show="diffOnly && @js($tab['diffCount'] === 0)" x-cloak class="text-sm text-body dark:text-slate-400 py-8 text-center">
                            {{ __('large-animals.comparison.no_differences') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    @elseif ($this->totalCount === 1)
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs px-6 py-10 text-center">
            <x-lucide-git-compare class="w-10 h-10 text-body dark:text-slate-400 mx-auto mb-3" />
            <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.comparison.select_more') }}</p>
        </div>

    @else
        <div class="bg-neutral-primary-soft dark:bg-slate-800 rounded-base shadow-xs py-16 text-center">
            <x-lucide-columns-3 class="w-16 h-16 text-body dark:text-slate-400 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-heading dark:text-white mb-2">
                {{ __('large-animals.comparison.empty_title') }}
            </h3>
            <p class="text-sm text-body dark:text-slate-400 mb-6">
                {{ __('large-animals.comparison.empty_desc') }}
            </p>
        </div>
    @endif
</div>
