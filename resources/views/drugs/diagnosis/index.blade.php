@extends('app.layouts.master')

@section('title', __('drugs.diagnosis.title'))

@section('content')
    <div class="space-y-4">

        {{-- Warning --}}
        @if (session('warning'))
            <div class="bg-danger-soft border border-danger-subtle text-fg-danger-strong dark:bg-red-950 dark:border-red-900 dark:text-red-300 rounded-base px-5 py-4 flex items-start gap-3">
                <x-lucide-alert-triangle class="w-5 h-5 shrink-0 mt-0.5" />
                <p class="text-sm font-medium">{{ session('warning') }}</p>
            </div>
        @endif

        {{-- Header --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-heading dark:text-white">{{ __('drugs.diagnosis.heading') }}</h1>
                    <p class="text-body dark:text-slate-400 text-base mt-1">{{ __('drugs.diagnosis.subtitle') }}</p>
                </div>
                <a href="{{ route('drugs.filter') }}" wire:navigate
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-fg-brand border border-brand rounded-base hover:bg-brand-soft transition-colors dark:text-brand dark:hover:bg-brand/20">
                    <x-lucide-sliders-horizontal class="w-4 h-4" />
                    {{ __('drugs.nav.filter') }}
                </a>
            </div>
        </div>

        @php
            $groupedSigns = $allSigns->groupBy('body_system')->sortKeys();
        @endphp

        <form method="POST" action="{{ route('drugs.diagnosis.run') }}" x-data="diagnosisPicker()">
            @csrf

            {{-- Controls --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="host_species_id" class="block text-sm font-semibold text-heading dark:text-white mb-2">{{ __('drugs.diagnosis.species_label') }}</label>
                        <select id="host_species_id" name="host_species_id"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">{{ __('drugs.diagnosis.species_placeholder') }}</option>
                            @foreach ($hostSpecies as $species)
                                <option value="{{ $species->id }}">{{ $species->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 relative">
                        <label for="signFilter" class="block text-sm font-semibold text-heading dark:text-white mb-2">{{ __('drugs.diagnosis.signs_heading') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                            </div>
                            <input type="text" id="signFilter" x-model="search"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                placeholder="{{ __('drugs.diagnosis.search_placeholder') }}">
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm">
                            <span class="inline-flex items-center gap-1.5 text-body dark:text-slate-400">
                                <span x-text="selectedCount" class="font-bold text-fg-brand dark:text-brand"></span>
                                {{ __('drugs.diagnosis.selected') }}
                            </span>
                            <button type="button" @click="clearAll()"
                                class="inline-flex items-center gap-1 text-sm font-medium text-fg-brand hover:underline dark:text-brand">
                                <x-lucide-x class="w-3.5 h-3.5" />
                                {{ __('drugs.diagnosis.clear') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sign checklist --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
                @forelse ($groupedSigns as $bodySystem => $signs)
                    <div class="mb-5 last:mb-0" x-show='groupVisible({{ json_encode((string) $bodySystem) }}, {{ json_encode($signs->pluck('display_name')->merge($signs->pluck('canonical_name'))->unique()->map(fn ($n) => (string) $n)->values()->toArray()) }})'>
                        <div class="flex items-center gap-2 mb-2">
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $bodySystem }}</h2>
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ __('drugs.diagnosis.group_count', ['count' => $signs->count()]) }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($signs as $sign)
                                <label x-show="signVisible({{ json_encode((string) $sign['display_name']) }}, {{ json_encode((string) $sign['canonical_name']) }})"
                                    class="flex items-start gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft cursor-pointer hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand" :class="{ 'border-brand bg-brand-soft dark:bg-brand/20': isChecked('{{ $sign['id'] }}') }">
                                    <input type="checkbox" name="clinical_signs[]" value="{{ $sign['id'] }}"
                                        x-model="selected"
                                        class="mt-0.5 w-4 h-4 rounded-xs text-brand focus:ring-2 focus:ring-brand-soft dark:bg-slate-600 dark:border-slate-500">
                                    <span>
                                        <span class="block text-sm font-medium text-heading dark:text-white">{{ $sign['display_name'] }}</span>
                                        @if ($sign['canonical_name'] && $sign['canonical_name'] !== $sign['display_name'])
                                            <span class="block text-xs text-body dark:text-slate-400 mt-0.5">{{ $sign['canonical_name'] }}</span>
                                        @endif
                                        <span class="block text-xs text-body/70 dark:text-slate-500 mt-1">{{ $sign['clinical_category'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-base text-body dark:text-slate-400 text-center py-8">{{ __('drugs.diagnosis.none_selected') }}</p>
                @endforelse

                {{-- No match empty state --}}
                <div x-show="selectedCount === 0 && search.length > 0" x-cloak class="text-center py-8">
                    <x-lucide-search-x class="w-12 h-12 text-body mx-auto mb-3 dark:text-slate-500" />
                    <p class="text-base text-body dark:text-slate-400">{{ __('drugs.diagnosis.no_signs') }}</p>
                </div>

                <div class="mt-6 pt-5 border-t border-default-medium dark:border-slate-700 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors">
                        <x-lucide-activity class="w-4 h-4" />
                        {{ __('drugs.diagnosis.submit') }}
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('css')
    <script>
        function diagnosisPicker() {
            return {
                search: '',
                selected: [],
                get selectedCount() {
                    return this.selected.length;
                },
                isChecked(id) {
                    return this.selected.includes(String(id));
                },
                clearAll() {
                    this.selected = [];
                    const inputs = document.querySelectorAll('input[name="clinical_signs[]"]');
                    inputs.forEach((input) => { input.checked = false; });
                },
                groupVisible(name, names) {
                    const q = this.search.trim().toLowerCase();
                    if (!q) return true;
                    return names.some((n) => n && n.toLowerCase().includes(q));
                },
                signVisible(displayName, canonicalName) {
                    const q = this.search.trim().toLowerCase();
                    if (!q) return true;
                    return (displayName && displayName.toLowerCase().includes(q)) || (canonicalName && canonicalName.toLowerCase().includes(q));
                },
            };
        }
    </script>
@endsection
