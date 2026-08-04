@extends('app.layouts.master')

@section('title', __('drugs.filter.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-drugs.page-hero
            :heading="__('drugs.filter.heading')"
            :subtitle="__('drugs.filter.subtitle')"
            :badge="__('drugs.hero.badge.filter')"
            badgeIcon="sliders-horizontal"
            :stats="[
                ['count' => $species->count(), 'label' => __('drugs.hero.stats.species'), 'icon' => 'paw-print'],
                ['count' => $bodySystems->count(), 'label' => __('drugs.hero.stats.body_systems'), 'icon' => 'stethoscope'],
                ['count' => $etiologies->count(), 'label' => __('drugs.hero.stats.etiologies'), 'icon' => 'bug'],
            ]"
        />

        <form method="POST" action="{{ route('drugs.diagnosis.run') }}" x-data="filterPicker(@js(route('drugs.filter.suggestions')))">
            @csrf

            {{-- Criteria --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="host_species_id" class="block text-sm font-semibold text-heading dark:text-white mb-2">{{ __('drugs.filter.species_label') }}</label>
                        <select id="host_species_id" name="host_species_id"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">{{ __('drugs.filter.species_placeholder') }}</option>
                            @foreach ($species as $item)
                                <option value="{{ $item->id }}">{{ $item->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="etiology_type" class="block text-sm font-semibold text-heading dark:text-white mb-2">{{ __('drugs.filter.etiology_label') }}</label>
                        <select id="etiology_type" x-model="etiology"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">{{ __('drugs.filter.etiology_placeholder') }}</option>
                            @foreach ($etiologies as $item)
                                <option value="{{ $item->etiology_type }}">{{ $item->etiology_type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="body_system_id" class="block text-sm font-semibold text-heading dark:text-white mb-2">{{ __('drugs.filter.body_system_label') }}</label>
                        <select id="body_system_id" x-model="bodySystem"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">{{ __('drugs.filter.body_system_placeholder') }}</option>
                            @foreach ($bodySystems as $item)
                                <option value="{{ $item->display_name }}">{{ $item->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label class="mt-4 inline-flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" x-model="zoonotic"
                        class="w-4 h-4 rounded-xs text-brand focus:ring-2 focus:ring-brand-soft dark:bg-slate-600 dark:border-slate-500">
                    <span class="text-sm font-medium text-heading dark:text-white">{{ __('drugs.filter.zoonotic_label') }}</span>
                </label>
            </div>

            {{-- Token picker --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
                <h2 class="text-base font-semibold text-heading dark:text-white mb-4">{{ __('drugs.filter.tokens_label') }}</h2>

                {{-- Autocomplete --}}
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input type="text" x-model="query" @input.debounce.250ms="search()" @focus="if (query.length >= 2) { search(); }" @click.outside="open = false"
                        class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        placeholder="{{ __('drugs.filter.search_placeholder') }}">

                    <div x-show="open && suggestions.length > 0" x-cloak
                        class="absolute z-20 mt-2 w-full bg-neutral-primary-soft rounded-base shadow-lg border border-default-medium dark:bg-slate-700 dark:border-slate-600 overflow-hidden">
                        <template x-for="s in suggestions" :key="s.id">
                            <button type="button" @click="add(s)"
                                class="flex w-full items-center justify-between gap-3 px-4 py-2.5 text-start text-sm hover:bg-neutral-secondary-soft dark:hover:bg-slate-600 transition-colors">
                                <span class="font-medium text-heading dark:text-white" x-text="s.title"></span>
                                <span class="text-xs text-body dark:text-slate-400">{{ __('drugs.filter.add') }}</span>
                            </button>
                        </template>
                    </div>

                    <p x-show="open && suggestions.length === 0 && query.trim().length >= 2" x-cloak
                        class="mt-2 text-sm text-body dark:text-slate-400">{{ __('drugs.search.no_results') }}</p>
                </div>

                {{-- Tokens --}}
                <div class="mt-4 flex flex-wrap gap-2">
                    <template x-for="token in tokens" :key="token.id">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-brand-soft text-fg-brand border border-brand-subtle dark:bg-brand/20 dark:text-brand dark:border-brand-subtle">
                            <x-lucide-stethoscope class="w-3.5 h-3.5" />
                            <span x-text="token.title"></span>
                            <button type="button" @click="remove(token.id)" class="hover:text-fg-danger-strong dark:hover:text-red-400" aria-label="remove">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                        </span>
                    </template>
                </div>

                <template x-for="token in tokens" :key="'hidden-' + token.id">
                    <input type="hidden" name="clinical_signs[]" :value="token.id">
                </template>

                <div class="mt-4 flex items-center justify-between">
                    <button type="button" @click="clear()"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-fg-brand hover:underline dark:text-brand">
                        <x-lucide-trash-2 class="w-4 h-4" />
                        {{ __('drugs.filter.clear') }}
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors">
                        <x-lucide-activity class="w-4 h-4" />
                        {{ __('drugs.filter.run_diagnosis') }}
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('css')
    <script>
        function filterPicker(url) {
            return {
                query: '',
                suggestions: [],
                tokens: [],
                open: false,
                etiology: '',
                bodySystem: '',
                zoonotic: false,
                async search() {
                    const q = this.query.trim();
                    if (q.length < 2) {
                        this.suggestions = [];
                        this.open = false;
                        return;
                    }
                    try {
                        const res = await fetch(url + '?q=' + encodeURIComponent(q));
                        const json = await res.json();
                        const taken = this.tokens.map((t) => t.id);
                        this.suggestions = (json.data || [])
                            .filter((s) => !taken.includes(s.real_id))
                            .map((s) => ({ id: s.real_id, title: s.title }));
                        this.open = true;
                    } catch (e) {
                        this.suggestions = [];
                        this.open = false;
                    }
                },
                add(s) {
                    if (!this.tokens.some((t) => t.id === s.id)) {
                        this.tokens.push({ id: s.id, title: s.title });
                    }
                    this.query = '';
                    this.suggestions = [];
                    this.open = false;
                },
                remove(id) {
                    this.tokens = this.tokens.filter((t) => t.id !== id);
                },
                clear() {
                    this.tokens = [];
                },
            };
        }
    </script>
@endsection
