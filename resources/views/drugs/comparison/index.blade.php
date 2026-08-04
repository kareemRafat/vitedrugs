@extends('app.layouts.master')

@section('title', __('drugs.comparison.title'))

@section('content')
    <div class="space-y-4">

        {{-- Hero --}}
        <x-drugs.page-hero
            :heading="__('drugs.comparison.heading')"
            :subtitle="__('drugs.comparison.subtitle')"
            :badge="__('drugs.hero.badge.comparison')"
            badgeIcon="columns-3"
            :stats="[
                ['count' => $allDiseases->count(), 'label' => __('drugs.hero.stats.diseases'), 'icon' => 'activity'],
            ]"
        />

        <form method="GET" action="{{ route('drugs.comparison.results') }}" x-data="comparisonPicker(@js($selectedIds))">
            <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.comparison.select_diseases') }}</h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                        <span x-text="selected.length"></span>/4 {{ __('drugs.comparison.up_to') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach ($allDiseases as $disease)
                        <label class="flex items-center gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft cursor-pointer hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand"
                            :class="{ 'border-brand bg-brand-soft dark:bg-brand/20': isSelected('{{ $disease->id }}') }">
                            <input type="checkbox" name="disease_ids[]" value="{{ $disease->id }}"
                                x-model="selected"
                                @change="enforceLimit()"
                                class="mt-0.5 w-4 h-4 rounded-xs text-brand focus:ring-2 focus:ring-brand-soft dark:bg-slate-600 dark:border-slate-500">
                            <span class="text-sm font-medium text-heading dark:text-white">{{ $disease->name }}</span>
                        </label>
                    @endforeach
                </div>

                <p x-show="selected.length >= 4" x-cloak class="mt-3 text-sm text-body dark:text-slate-400">
                    {{ __('drugs.comparison.max_reached') }}
                </p>

                <div class="mt-6 pt-5 border-t border-default-medium dark:border-slate-700 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors">
                        <x-lucide-columns-3 class="w-4 h-4" />
                        {{ __('drugs.comparison.compare') }}
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('css')
    <script>
        function comparisonPicker(initial) {
            return {
                selected: initial || [],
                isSelected(id) {
                    return this.selected.includes(String(id));
                },
                enforceLimit() {
                    if (this.selected.length > 4) {
                        this.selected = this.selected.slice(0, 4);
                    }
                },
            };
        }
    </script>
@endsection
