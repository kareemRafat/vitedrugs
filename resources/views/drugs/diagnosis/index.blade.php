@extends('app.layouts.master')

@section('title', __('drugs.diagnosis.title'))

@section('content')
    <div class="space-y-4" x-data="diagnosisForm()">
        <x-drugs.page-hero
            :heading="__('drugs.diagnosis.heading')"
            :subtitle="__('drugs.diagnosis.subtitle')"
            :badge="__('drugs.hero.badge.diagnosis')"
            badgeIcon="stethoscope"
        />

        <form method="POST" action="{{ route('drugs.diagnosis.run') }}" class="bg-neutral-primary-soft rounded-base shadow-xs p-5 space-y-6 dark:bg-slate-800">
            @csrf

            <div>
                <label for="host_species_id" class="block text-sm font-semibold text-heading dark:text-white mb-2">{{ __('drugs.diagnosis.species_label') }}</label>
                <select id="host_species_id" name="host_species_id" required class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    <option value="">{{ __('drugs.diagnosis.species_placeholder') }}</option>
                    @foreach ($hostSpecies as $species)
                        <option value="{{ $species->id }}" @selected(old('host_species_id') === $species->id)>{{ $species->localized_display_name }}</option>
                    @endforeach
                </select>
                @error('host_species_id') <p class="mt-2 text-sm text-fg-danger-strong">{{ $message }}</p> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div>
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('drugs.diagnosis.signs_heading') }}</h2>
                        <p class="text-sm text-body dark:text-slate-400">{{ __('drugs.diagnosis.signs_help') }}</p>
                    </div>
                    <span class="text-sm font-medium text-fg-brand" x-text="`${signs.length} {{ __('drugs.diagnosis.selected') }}`"></span>
                </div>
                <div class="space-y-3">
                    <template x-for="(sign, index) in signs" :key="sign.key">
                        <div class="relative">
                            <input type="hidden" name="clinical_signs[]" :value="sign.id">
                            <label class="sr-only" :for="`clinical-sign-${sign.key}`">{{ __('drugs.diagnosis.clinical_sign') }}</label>
                            <div class="flex gap-2">
                                <div class="relative grow">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none"><x-lucide-search class="w-4 h-4 text-body" /></div>
                                    <input type="text" :id="`clinical-sign-${sign.key}`" x-model="sign.text" @input.debounce.200ms="search(index)" @focus="search(index)" autocomplete="off" required class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full ps-10 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="{{ __('drugs.diagnosis.search_placeholder') }}">
                                    <div x-show="sign.open && sign.suggestions.length" x-cloak class="absolute z-20 mt-1 w-full overflow-hidden rounded-base border border-default-medium bg-neutral-primary-soft shadow-xs dark:bg-slate-800">
                                        <template x-for="suggestion in sign.suggestions" :key="suggestion.id">
                                            <button type="button" @click="select(index, suggestion)" class="block w-full px-3 py-2.5 text-start text-sm text-heading hover:bg-neutral-secondary-soft dark:text-white dark:hover:bg-slate-700" x-text="suggestion.name"></button>
                                        </template>
                                    </div>
                                </div>
                                <button type="button" x-show="index >= 3" @click="signs.splice(index, 1)" class="inline-flex items-center justify-center w-10 rounded-base border border-default-medium text-body hover:bg-neutral-secondary-soft" aria-label="Remove symptom"><x-lucide-x class="w-4 h-4" /></button>
                            </div>
                        </div>
                    </template>
                </div>
                @error('clinical_signs') <p class="mt-2 text-sm text-fg-danger-strong">{{ $message }}</p> @enderror
                @error('clinical_signs.*') <p class="mt-2 text-sm text-fg-danger-strong">{{ $message }}</p> @enderror
                <button type="button" @click="add()" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-fg-brand hover:underline"><x-lucide-plus class="w-4 h-4" />{{ __('drugs.diagnosis.add_symptom') }}</button>
            </div>

            <div class="pt-5 border-t border-default-medium flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors"><x-lucide-search class="w-4 h-4" />{{ __('drugs.diagnosis.submit') }}</button>
            </div>
        </form>
    </div>
@endsection

@section('css')
<script>
function diagnosisForm() {
    return {
        signs: Array.from({ length: 3 }, (_, index) => ({ key: index, id: '', text: '', open: false, suggestions: [] })),
        nextKey: 3,
        add() { this.signs.push({ key: this.nextKey++, id: '', text: '', open: false, suggestions: [] }); },
        async search(index) {
            const sign = this.signs[index];
            sign.id = '';
            if (!sign.text.trim()) { sign.open = false; sign.suggestions = []; return; }
            const response = await fetch(`{{ route('drugs.diagnosis.suggestions') }}?q=${encodeURIComponent(sign.text)}`);
            sign.suggestions = await response.json();
            sign.open = true;
        },
        select(index, suggestion) { Object.assign(this.signs[index], { id: suggestion.id, text: suggestion.name, open: false, suggestions: [] }); },
    };
}
</script>
@endsection
