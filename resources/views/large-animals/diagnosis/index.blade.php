@extends('app.layouts.master')

@section('title', __('large-animals.diagnosis.title'))

@section('content')
    <div class="space-y-4" x-data="diagnosisForm()">
        <x-large-animals.page-hero
            :heading="__('large-animals.diagnosis.heading')"
            :subtitle="__('large-animals.diagnosis.subtitle')"
            :badge="__('large-animals.hero.badge.diagnosis')"
            badgeIcon="stethoscope"
        />
        <x-large-animals.stepper :current="1" />

        <form method="POST" action="{{ route('large-animals.diagnosis.run') }}" @submit="onSubmit" class="bg-neutral-primary-soft rounded-base shadow-xs p-5 space-y-6 dark:bg-slate-800">
            @csrf

            <div>
                <label for="host_species_id" class="block text-sm font-semibold text-heading dark:text-white mb-2">
                    {{ __('large-animals.diagnosis.species_label') }} <span class="text-fg-danger-strong" aria-hidden="true">*</span>
                </label>
                <select id="host_species_id" name="host_species_id" required aria-required="true" class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    <option value="">{{ __('large-animals.diagnosis.species_placeholder') }}</option>
                    @foreach ($hostSpecies as $species)
                        <option value="{{ $species->id }}" @selected(old('host_species_id') === $species->id)>{{ $species->localized_display_name }}</option>
                    @endforeach
                </select>
                @error('host_species_id')
                    <div class="mt-2 flex items-center gap-2 bg-danger-soft border border-danger-subtle text-fg-danger-strong text-sm font-medium rounded-base px-4 py-3 dark:bg-red-950/40 dark:border-danger-subtle dark:text-red-300" role="alert">
                        <x-lucide-alert-triangle class="w-5 h-5 shrink-0" />
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div>
                        <h2 class="text-base font-semibold text-heading dark:text-white">
                            {{ __('large-animals.diagnosis.signs_heading') }} <span class="text-fg-danger-strong" aria-hidden="true">*</span>
                        </h2>
                        <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.diagnosis.signs_help') }}</p>
                    </div>
                    <span class="text-sm font-medium text-fg-brand" x-text="`${signs.length} {{ __('large-animals.diagnosis.selected') }}`"></span>
                </div>

                {{-- Client-side guard: free text without a selected suggestion --}}
                <div x-show="showInvalidSigns" x-cloak class="mb-3 flex items-center gap-2 bg-danger-soft border border-danger-subtle text-fg-danger-strong text-sm font-medium rounded-base px-4 py-3 dark:bg-red-950/40 dark:border-danger-subtle dark:text-red-300" role="alert">
                    <x-lucide-alert-triangle class="w-5 h-5 shrink-0" />
                    <span>{{ __('validation.diagnosis.sign_invalid') }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="(sign, index) in signs" :key="sign.key">
                        <div class="relative">
                            <input type="hidden" name="clinical_signs[]" :value="sign.id">
                            <label class="sr-only" :for="`clinical-sign-${sign.key}`">{{ __('large-animals.diagnosis.clinical_sign') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none"><x-lucide-search class="w-4 h-4 text-body" /></div>
                                <input type="text" :id="`clinical-sign-${sign.key}`" x-model="sign.text"
                                    @input.debounce.200ms="search(index)" @focus="search(index)"
                                    :class="sign.invalid ? 'border-danger-subtle focus:border-danger-subtle' : 'border-default-medium focus:border-brand'"
                                    @click.outside="sign.open = false"
                                    autocomplete="off" required
                                    class="bg-neutral-secondary-medium text-heading text-sm rounded-base focus:ring-brand block w-full ps-10 pe-9 px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:text-white"
                                    :placeholder="`${index + 1}. {{ __('large-animals.diagnosis.search_placeholder') }}`">

                                <button type="button" x-show="signs.length > 3" @click="remove(index)"
                                    class="absolute top-1/2 end-1.5 -translate-y-1/2 flex h-6 w-6 items-center justify-center rounded-xs text-body hover:bg-neutral-secondary-medium hover:text-fg-danger-strong transition-colors"
                                    aria-label="{{ __('large-animals.diagnosis.remove_symptom') }}">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>

                                <div x-show="sign.open && sign.suggestions.length" x-cloak class="absolute z-20 mt-1 w-full overflow-hidden rounded-base border border-default-medium bg-neutral-primary-soft shadow-xs dark:bg-slate-800">
                                    <template x-for="suggestion in sign.suggestions" :key="suggestion.id">
                                        <button type="button" @click="select(index, suggestion)" class="block w-full px-3 py-2.5 text-start text-sm text-heading hover:bg-neutral-secondary-soft dark:text-white dark:hover:bg-slate-700" x-text="suggestion.label"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                @error('clinical_signs')
                    <div class="mt-3 flex items-center gap-2 bg-danger-soft border border-danger-subtle text-fg-danger-strong text-sm font-medium rounded-base px-4 py-3 dark:bg-red-950/40 dark:border-danger-subtle dark:text-red-300" role="alert">
                        <x-lucide-alert-triangle class="w-5 h-5 shrink-0" />
                        <span>{{ $message }}</span>
                    </div>
                @enderror
                @error('clinical_signs.*')
                    <div class="mt-3 flex items-center gap-2 bg-danger-soft border border-danger-subtle text-fg-danger-strong text-sm font-medium rounded-base px-4 py-3 dark:bg-red-950/40 dark:border-danger-subtle dark:text-red-300" role="alert">
                        <x-lucide-alert-triangle class="w-5 h-5 shrink-0" />
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <button type="button" @click="add()" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-fg-brand hover:underline"><x-lucide-plus class="w-4 h-4" />{{ __('large-animals.diagnosis.add_symptom') }}</button>
            </div>

            <div class="pt-5 border-t border-default-medium flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base transition-colors"><x-lucide-search class="w-4 h-4" />{{ __('large-animals.diagnosis.submit') }}</button>
            </div>
        </form>

        @if ($errors->any())
            <script>
                (function () {
                    var alertEl = document.querySelector('[role="alert"]');
                    if (alertEl) {
                        alertEl.scrollIntoView({ block: 'center' });
                    }
                })();
            </script>
        @endif
    </div>
@endsection

@section('css')
<script>
function diagnosisForm() {
    const emptySign = (key) => ({ key, id: '', text: '', open: false, suggestions: [], invalid: false });

    return {
        signs: Array.from({ length: 3 }, (_, index) => emptySign(index)),
        nextKey: 3,
        showInvalidSigns: false,
        add() {
            this.signs.push(emptySign(this.nextKey++));
        },
        remove(index) {
            this.signs.splice(index, 1);
        },
        async search(index) {
            const sign = this.signs[index];
            sign.id = '';
            if (!sign.text.trim()) { sign.open = false; sign.suggestions = []; return; }
            const response = await fetch(`{{ route('large-animals.diagnosis.suggestions') }}?q=${encodeURIComponent(sign.text)}`);
            sign.suggestions = await response.json();
            sign.open = true;
        },
        select(index, suggestion) {
            Object.assign(this.signs[index], { id: suggestion.id, text: suggestion.label, open: false, suggestions: [], invalid: false });
            this.showInvalidSigns = false;
        },
        validate() {
            let firstInvalidInput = null;
            this.signs.forEach((sign) => {
                sign.invalid = sign.text.trim() !== '' && !sign.id;
                if (sign.invalid && !firstInvalidInput) {
                    firstInvalidInput = document.getElementById(`clinical-sign-${sign.key}`);
                }
            });

            this.showInvalidSigns = firstInvalidInput !== null;
            if (firstInvalidInput) {
                firstInvalidInput.focus();
            }

            return !this.showInvalidSigns;
        },
        onSubmit(event) {
            if (!this.validate()) {
                event.preventDefault();
            }
        },
    };
}
</script>
@endsection
