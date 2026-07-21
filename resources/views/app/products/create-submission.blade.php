@extends('app.layouts.master')

@section('title', __('messages.pages.products.submission.title'))

@section('content')
<x-toast id="submissionToast" type="success" title="" message="" />

<div class="space-y-8 pb-8 sm:pb-12">

  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm mt-8">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-200 dark:text-sky-200 border border-white/10 dark:border-sky-700 mb-5">
        <x-lucide-package-plus class="w-4 h-4" />
        <span>{{ __('messages.pages.products.submission.badge') }}</span>
      </div>
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.products.submission.heading') }}
      </h1>
      <div class="w-16 h-1 bg-blue-400 dark:bg-sky-400 rounded-full mt-6 mb-6"></div>
      <p class="text-base sm:text-lg text-slate-300 dark:text-sky-200 max-w-xl leading-relaxed">
        {{ __('messages.pages.products.submission.subtitle') }}
      </p>
    </div>
  </div>

  @if (session('success'))
    <div class="bg-success-soft border border-success-subtle text-fg-success-strong rounded-base p-4 text-sm" role="alert">
      <span class="font-medium">{{ session('success') }}</span>
    </div>
  @endif

  <form
    method="POST"
    action="{{ route('products.submission.store') }}"
    class="space-y-6"
    x-data="{
      errors: {},
      submitting: false,
      submitted_by_name: '{{ old('submitted_by_name') }}',
      submitted_by_email: '{{ old('submitted_by_email') }}',
      submitted_by_phone: '{{ old('submitted_by_phone') }}',
      trade_name: '{{ old('trade_name') }}',
      trade_name_ar: '{{ old('trade_name_ar') }}',
      product_type: '{{ old('product_type') }}',
      package_size: '{{ old('package_size') }}',
      storage_conditions: '{{ old('storage_conditions') }}',
      description: '{{ old('description') }}',
      description_ar: '{{ old('description_ar') }}',
      company: '{{ old('company') }}',
      dosage_form: '{{ old('dosage_form') }}',
      activeIngredients: [],
      diseases: [],
      indications: [],
      contraindications: [],
      precautions: [],
      sideEffects: [],
      dosages: [],
      withdrawalPeriods: [],
      documents: [],
      imageUrls: [],
      addItem(list, item) { this[list].push(item); },
      removeItem(list, index) { this[list].splice(index, 1); },
      addIngredient() { this.activeIngredients.push({ name: '', strength: '', unit: '' }); },
      addDisease() { this.diseases.push(''); },
      addMedical(list) { this[list].push(''); },
      addDosage() { this.dosages.push({ species: '', dosage: '', route: '', duration: '', notes: '' }); },
      addWithdrawal() { this.withdrawalPeriods.push({ species: '', meat_days: '', milk_days: '', egg_days: '', notes: '' }); },
      addDocument() { this.documents.push({ title: '', url: '' }); },
      addImageUrl() { this.imageUrls.push(''); },
      clearError(field) { delete this.errors[field]; },
      hasSectionError(prefix) {
        return Object.keys(this.errors).some(k => k.startsWith(prefix));
      },
      async submitForm() {
        this.submitting = true;
        this.errors = {};
        try {
          const resp = await fetch(this.$el.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: new FormData(this.$el)
          });
          const json = await resp.json();
          if (resp.ok) {
            this.submitting = false;
            window.Toast.show('submissionToast', 'success', json.message || '');
            setTimeout(() => { Livewire.navigate('{{ route('products.index') }}'); }, 2000);
          } else if (resp.status === 422) {
            this.errors = json.errors || {};
            window.Toast.show('submissionToast', 'error', '{{ __('messages.pages.products.submission.error_toast') }}');
            window.scrollTo({ top: 0, behavior: 'smooth' });
          } else {
            if (json.message) window.Toast.show('submissionToast', 'error', json.message);
          }
        } catch (e) {
          window.Toast.show('submissionToast', 'error', '{{ __('messages.pages.products.submission.network_error') }}');
        } finally {
          this.submitting = false;
        }
      }
    }"
    x-on:submit.prevent="submitForm"
  >
    @csrf

    {{-- Submitter Info --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-5">
      <h2 class="text-lg font-bold text-heading flex items-center gap-2">
        <x-lucide-user class="w-5 h-5 text-brand" />
        {{ __('messages.pages.products.submission.submitter_info') }}
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label for="submitted_by_name" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.name_label') }} <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" /></label>
          <input type="text" name="submitted_by_name" id="submitted_by_name" x-model="submitted_by_name"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.submitted_by_name }"
            x-on:input="clearError('submitted_by_name')"
            placeholder="{{ __('messages.pages.products.submission.name_placeholder') }}">
            <template x-if="errors.submitted_by_name">
              <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                <span x-text="errors.submitted_by_name[0]"></span>
              </p>
            </template>
        </div>
        <div>
          <label for="submitted_by_email" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.email_label') }} <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" /></label>
          <input type="email" name="submitted_by_email" id="submitted_by_email" x-model="submitted_by_email"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.submitted_by_email }"
            x-on:input="clearError('submitted_by_email')"
            placeholder="{{ __('messages.pages.products.submission.email_placeholder') }}">
            <template x-if="errors.submitted_by_email">
              <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                <span x-text="errors.submitted_by_email[0]"></span>
              </p>
            </template>
        </div>
        <div>
          <label for="submitted_by_phone" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.phone_label') }}</label>
          <input type="text" name="submitted_by_phone" id="submitted_by_phone" x-model="submitted_by_phone"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.submitted_by_phone }"
            x-on:input="clearError('submitted_by_phone')"
            placeholder="{{ __('messages.pages.products.submission.phone_placeholder') }}">
            <template x-if="errors.submitted_by_phone">
              <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                <span x-text="errors.submitted_by_phone[0]"></span>
              </p>
            </template>
        </div>
      </div>
    </div>

    {{-- Basic Info --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-5">
      <h2 class="text-lg font-bold text-heading flex items-center gap-2">
        <x-lucide-package class="w-5 h-5 text-brand" />
        {{ __('messages.pages.products.submission.basic_info') }}
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="trade_name" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.trade_name_label') }} <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" /></label>
          <input type="text" name="trade_name" id="trade_name" x-model="trade_name"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.trade_name }"
            x-on:input="clearError('trade_name')"
            placeholder="{{ __('messages.pages.products.submission.trade_name_placeholder') }}">
          <template x-if="errors.trade_name">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.trade_name[0]"></span>
            </p>
          </template>
        </div>
        <div>
          <label for="trade_name_ar" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.trade_name_ar_label') }}</label>
          <input type="text" name="trade_name_ar" id="trade_name_ar" x-model="trade_name_ar"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.trade_name_ar }"
            x-on:input="clearError('trade_name_ar')"
            placeholder="{{ __('messages.pages.products.submission.trade_name_ar_placeholder') }}">
          <template x-if="errors.trade_name_ar">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.trade_name_ar[0]"></span>
            </p>
          </template>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="product_type" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.product_type_label') }} <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" /></label>
          <select name="product_type" id="product_type" x-model="product_type"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.product_type }"
            x-on:change="clearError('product_type')">
            <option value="">{{ __('messages.pages.products.submission.select_type') }}</option>
            <option value="pharmaceutical">{{ __('messages.pages.products.submission.type_pharmaceutical') }}</option>
            <option value="vaccine">{{ __('messages.pages.products.submission.type_vaccine') }}</option>
            <option value="supplement">{{ __('messages.pages.products.submission.type_supplement') }}</option>
            <option value="feed_additive">{{ __('messages.pages.products.submission.type_feed_additive') }}</option>
            <option value="disinfectant">{{ __('messages.pages.products.submission.type_disinfectant') }}</option>
            <option value="biological">{{ __('messages.pages.products.submission.type_biological') }}</option>
          </select>
          <template x-if="errors.product_type">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.product_type[0]"></span>
            </p>
          </template>
        </div>
        <div>
          <label for="package_size" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.package_size_label') }}</label>
          <input type="text" name="package_size" id="package_size" x-model="package_size"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.package_size }"
            x-on:input="clearError('package_size')"
            placeholder="{{ __('messages.pages.products.submission.package_size_placeholder') }}">
          <template x-if="errors.package_size">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.package_size[0]"></span>
            </p>
          </template>
        </div>
      </div>
      <div>
        <label for="storage_conditions" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.storage_label') }}</label>
        <textarea name="storage_conditions" id="storage_conditions" rows="2" x-model="storage_conditions"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
            :class="{ 'border-red-600': errors.storage_conditions }"
            x-on:input="clearError('storage_conditions')"></textarea>
          <template x-if="errors.storage_conditions">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.storage_conditions[0]"></span>
            </p>
          </template>
      </div>
      <div>
        <label for="description" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.description_label') }}</label>
        <textarea name="description" id="description" rows="4" x-model="description"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
            :class="{ 'border-red-600': errors.description }"
            x-on:input="clearError('description')"></textarea>
          <template x-if="errors.description">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.description[0]"></span>
            </p>
          </template>
      </div>
      <div>
        <label for="description_ar" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.description_ar_label') }}</label>
        <textarea name="description_ar" id="description_ar" rows="3" x-model="description_ar"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
            :class="{ 'border-red-600': errors.description_ar }"
            x-on:input="clearError('description_ar')"></textarea>
          <template x-if="errors.description_ar">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.description_ar[0]"></span>
            </p>
          </template>
      </div>
    </div>

    {{-- Classification --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-5">
      <h2 class="text-lg font-bold text-heading flex items-center gap-2">
        <x-lucide-tags class="w-5 h-5 text-brand" />
        {{ __('messages.pages.products.submission.classification') }}
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="company" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.company_label') }} <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" /></label>
          <input type="text" name="company" id="company" x-model="company"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.company }"
            x-on:input="clearError('company')"
            placeholder="{{ __('messages.pages.products.submission.company_placeholder') }}">
          <template x-if="errors.company">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.company[0]"></span>
            </p>
          </template>
        </div>
        <div>
          <label for="dosage_form" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.dosage_form_label') }} <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" /></label>
          <input type="text" name="dosage_form" id="dosage_form" x-model="dosage_form"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            :class="{ 'border-red-600': errors.dosage_form }"
            x-on:input="clearError('dosage_form')"
            placeholder="{{ __('messages.pages.products.submission.dosage_form_placeholder') }}">
          <template x-if="errors.dosage_form">
<p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
              <span x-text="errors.dosage_form[0]"></span>
            </p>
          </template>
        </div>
      </div>
    </div>

    {{-- Active Ingredients --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-4">
      <template x-if="hasSectionError('active_ingredients')">
        <div class="border-s-2 border-red-600 ps-3 py-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1.5">
          <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
          <span>{{ __('messages.pages.products.submission.section_required') }}</span>
        </div>
      </template>
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-heading flex items-center gap-2">
          <x-lucide-flask-conical class="w-5 h-5 text-brand" />
          {{ __('messages.pages.products.submission.active_ingredients') }}
          <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" />
        </h2>
        <button type="button" @click="addIngredient()"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
          <x-lucide-plus class="w-4 h-4" />
          {{ __('messages.pages.products.submission.add') }}
        </button>
      </div>
      <template x-for="(ing, i) in activeIngredients" :key="i">
        <div class="flex items-start gap-3 p-3 bg-neutral-secondary-soft rounded-base border border-default-medium">
          <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <input type="text" x-model="ing.name" :name="`active_ingredients[${i}][name]`" :placeholder="'{{ __('messages.pages.products.submission.ingredient_name_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`active_ingredients.${i}.name`] }"
                @input="clearError(`active_ingredients.${i}.name`)">
              <template x-if="errors[`active_ingredients.${i}.name`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`active_ingredients.${i}.name`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="text" x-model="ing.strength" :name="`active_ingredients[${i}][strength]`" :placeholder="'{{ __('messages.pages.products.submission.strength_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`active_ingredients.${i}.strength`] }"
                @input="clearError(`active_ingredients.${i}.strength`)">
              <template x-if="errors[`active_ingredients.${i}.strength`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`active_ingredients.${i}.strength`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="text" x-model="ing.unit" :name="`active_ingredients[${i}][unit]`" :placeholder="'{{ __('messages.pages.products.submission.unit_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`active_ingredients.${i}.unit`] }"
                @input="clearError(`active_ingredients.${i}.unit`)">
              <template x-if="errors[`active_ingredients.${i}.unit`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`active_ingredients.${i}.unit`][0]"></span>
                </p>
              </template>
            </div>
          </div>
          <button type="button" @click="removeItem('activeIngredients', i)"
            class="shrink-0 p-1.5 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
            <x-lucide-x class="w-4 h-4" />
          </button>
        </div>
      </template>
    </div>

    {{-- Diseases --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-heading flex items-center gap-2">
          <x-lucide-bug class="w-5 h-5 text-brand" />
          {{ __('messages.pages.products.submission.diseases') }}
        </h2>
        <button type="button" @click="addDisease()"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
          <x-lucide-plus class="w-4 h-4" />
          {{ __('messages.pages.products.submission.add') }}
        </button>
      </div>
      <template x-for="(d, i) in diseases" :key="i">
        <div class="flex items-start gap-3">
          <div class="flex-1">
            <input type="text" x-model="diseases[i]" :name="`diseases[${i}]`" :placeholder="'{{ __('messages.pages.products.submission.disease_placeholder') }}'"
              class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
              :class="{ 'border-red-600': errors[`diseases.${i}`] }"
              @input="clearError(`diseases.${i}`)">
            <template x-if="errors[`diseases.${i}`]">
              <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                <span x-text="errors[`diseases.${i}`][0]"></span>
              </p>
            </template>
          </div>
          <button type="button" @click="removeItem('diseases', i)"
            class="shrink-0 p-1.5 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
            <x-lucide-x class="w-4 h-4" />
          </button>
        </div>
      </template>
    </div>

    {{-- Medical Details --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-6">
      <h2 class="text-lg font-bold text-heading flex items-center gap-2">
        <x-lucide-stethoscope class="w-5 h-5 text-brand" />
        {{ __('messages.pages.products.submission.medical_details') }}
      </h2>

      {{-- Indications --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-heading">{{ __('messages.pages.products.submission.indications') }}</h3>
          <button type="button" @click="addMedical('indications')"
            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
            <x-lucide-plus class="w-3.5 h-3.5" /> {{ __('messages.pages.products.submission.add') }}
          </button>
        </div>
        <template x-for="(_, i) in indications" :key="i">
          <div class="flex items-start gap-3">
            <div class="flex-1">
              <textarea x-model="indications[i]" :name="`indications[${i}]`" rows="2"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
                :class="{ 'border-red-600': errors[`indications.${i}`] }"
                @input="clearError(`indications.${i}`)"></textarea>
              <template x-if="errors[`indications.${i}`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`indications.${i}`][0]"></span>
                </p>
              </template>
            </div>
            <button type="button" @click="removeItem('indications', i)"
              class="shrink-0 p-1.5 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
              <x-lucide-x class="w-4 h-4" />
            </button>
          </div>
        </template>
      </div>

      {{-- Contraindications --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-heading">{{ __('messages.pages.products.submission.contraindications') }}</h3>
          <button type="button" @click="addMedical('contraindications')"
            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
            <x-lucide-plus class="w-3.5 h-3.5" /> {{ __('messages.pages.products.submission.add') }}
          </button>
        </div>
        <template x-for="(_, i) in contraindications" :key="i">
          <div class="flex items-start gap-3">
            <div class="flex-1">
              <textarea x-model="contraindications[i]" :name="`contraindications[${i}]`" rows="2"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
                :class="{ 'border-red-600': errors[`contraindications.${i}`] }"
                @input="clearError(`contraindications.${i}`)"></textarea>
              <template x-if="errors[`contraindications.${i}`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`contraindications.${i}`][0]"></span>
                </p>
              </template>
            </div>
            <button type="button" @click="removeItem('contraindications', i)"
              class="shrink-0 p-1.5 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
              <x-lucide-x class="w-4 h-4" />
            </button>
          </div>
        </template>
      </div>

      {{-- Precautions --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-heading">{{ __('messages.pages.products.submission.precautions') }}</h3>
          <button type="button" @click="addMedical('precautions')"
            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
            <x-lucide-plus class="w-3.5 h-3.5" /> {{ __('messages.pages.products.submission.add') }}
          </button>
        </div>
        <template x-for="(_, i) in precautions" :key="i">
          <div class="flex items-start gap-3">
            <div class="flex-1">
              <textarea x-model="precautions[i]" :name="`precautions[${i}]`" rows="2"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
                :class="{ 'border-red-600': errors[`precautions.${i}`] }"
                @input="clearError(`precautions.${i}`)"></textarea>
              <template x-if="errors[`precautions.${i}`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`precautions.${i}`][0]"></span>
                </p>
              </template>
            </div>
            <button type="button" @click="removeItem('precautions', i)"
              class="shrink-0 p-1.5 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
              <x-lucide-x class="w-4 h-4" />
            </button>
          </div>
        </template>
      </div>

      {{-- Side Effects --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-heading">{{ __('messages.pages.products.submission.side_effects') }}</h3>
          <button type="button" @click="addMedical('sideEffects')"
            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
            <x-lucide-plus class="w-3.5 h-3.5" /> {{ __('messages.pages.products.submission.add') }}
          </button>
        </div>
        <template x-for="(_, i) in sideEffects" :key="i">
          <div class="flex items-start gap-3">
            <div class="flex-1">
              <textarea x-model="sideEffects[i]" :name="`side_effects[${i}]`" rows="2"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
                :class="{ 'border-red-600': errors[`side_effects.${i}`] }"
                @input="clearError(`side_effects.${i}`)"></textarea>
              <template x-if="errors[`side_effects.${i}`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`side_effects.${i}`][0]"></span>
                </p>
              </template>
            </div>
            <button type="button" @click="removeItem('sideEffects', i)"
              class="shrink-0 p-1.5 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
              <x-lucide-x class="w-4 h-4" />
            </button>
          </div>
        </template>
      </div>
    </div>

    {{-- Dosages --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-4">
      <template x-if="hasSectionError('dosages')">
        <div class="border-s-2 border-red-600 ps-3 py-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1.5">
          <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
          <span>{{ __('messages.pages.products.submission.section_required') }}</span>
        </div>
      </template>
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-heading flex items-center gap-2">
          <x-lucide-syringe class="w-5 h-5 text-brand" />
          {{ __('messages.pages.products.submission.dosages') }}
          <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" />
        </h2>
        <button type="button" @click="addDosage()"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
          <x-lucide-plus class="w-4 h-4" />
          {{ __('messages.pages.products.submission.add') }}
        </button>
      </div>
      <template x-for="(d, i) in dosages" :key="i">
        <div class="p-4 bg-neutral-secondary-soft rounded-base border border-default-medium space-y-3">
          <div class="flex items-start justify-between">
            <span class="text-xs font-semibold text-body uppercase tracking-wide" x-text="`{{ __('messages.pages.products.submission.dosage_entry') }} #${i + 1}`"></span>
            <button type="button" @click="removeItem('dosages', i)"
              class="p-1 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
              <x-lucide-trash-2 class="w-4 h-4" />
            </button>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <input type="text" x-model="d.species" :name="`dosages[${i}][species]`" :placeholder="'{{ __('messages.pages.products.submission.species_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`dosages.${i}.species`] }"
                @input="clearError(`dosages.${i}.species`)">
              <template x-if="errors[`dosages.${i}.species`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`dosages.${i}.species`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="text" x-model="d.dosage" :name="`dosages[${i}][dosage]`" :placeholder="'{{ __('messages.pages.products.submission.dosage_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`dosages.${i}.dosage`] }"
                @input="clearError(`dosages.${i}.dosage`)">
              <template x-if="errors[`dosages.${i}.dosage`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`dosages.${i}.dosage`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="text" x-model="d.route" :name="`dosages[${i}][route]`" :placeholder="'{{ __('messages.pages.products.submission.route_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`dosages.${i}.route`] }"
                @input="clearError(`dosages.${i}.route`)">
              <template x-if="errors[`dosages.${i}.route`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`dosages.${i}.route`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="text" x-model="d.duration" :name="`dosages[${i}][duration]`" :placeholder="'{{ __('messages.pages.products.submission.duration_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`dosages.${i}.duration`] }"
                @input="clearError(`dosages.${i}.duration`)">
              <template x-if="errors[`dosages.${i}.duration`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`dosages.${i}.duration`][0]"></span>
                </p>
              </template>
            </div>
          </div>
          <div>
            <textarea x-model="d.notes" :name="`dosages[${i}][notes]`" rows="1" :placeholder="'{{ __('messages.pages.products.submission.notes_placeholder') }}'"
              class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
              :class="{ 'border-red-600': errors[`dosages.${i}.notes`] }"
              @input="clearError(`dosages.${i}.notes`)"></textarea>
            <template x-if="errors[`dosages.${i}.notes`]">
              <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                <span x-text="errors[`dosages.${i}.notes`][0]"></span>
              </p>
            </template>
          </div>
        </div>
      </template>
    </div>

    {{-- Withdrawal Periods --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-4">
      <template x-if="hasSectionError('withdrawal_periods')">
        <div class="border-s-2 border-red-600 ps-3 py-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1.5">
          <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
          <span>{{ __('messages.pages.products.submission.section_required') }}</span>
        </div>
      </template>
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-heading flex items-center gap-2">
          <x-lucide-clock class="w-5 h-5 text-brand" />
          {{ __('messages.pages.products.submission.withdrawal_periods') }}
          <x-lucide-asterisk class="w-3 h-3 text-red-600 inline -mt-0.5" />
        </h2>
        <button type="button" @click="addWithdrawal()"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
          <x-lucide-plus class="w-4 h-4" />
          {{ __('messages.pages.products.submission.add') }}
        </button>
      </div>
      <template x-for="(w, i) in withdrawalPeriods" :key="i">
        <div class="p-4 bg-neutral-secondary-soft rounded-base border border-default-medium space-y-3">
          <div class="flex items-start justify-between">
            <span class="text-xs font-semibold text-body uppercase tracking-wide" x-text="`{{ __('messages.pages.products.submission.withdrawal_entry') }} #${i + 1}`"></span>
            <button type="button" @click="removeItem('withdrawalPeriods', i)"
              class="p-1 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
              <x-lucide-trash-2 class="w-4 h-4" />
            </button>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <input type="text" x-model="w.species" :name="`withdrawal_periods[${i}][species]`" :placeholder="'{{ __('messages.pages.products.submission.species_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`withdrawal_periods.${i}.species`] }"
                @input="clearError(`withdrawal_periods.${i}.species`)">
              <template x-if="errors[`withdrawal_periods.${i}.species`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`withdrawal_periods.${i}.species`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="number" x-model="w.meat_days" :name="`withdrawal_periods[${i}][meat_days]`" :placeholder="'{{ __('messages.pages.products.submission.meat_days_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`withdrawal_periods.${i}.meat_days`] }"
                @input="clearError(`withdrawal_periods.${i}.meat_days`)">
              <template x-if="errors[`withdrawal_periods.${i}.meat_days`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`withdrawal_periods.${i}.meat_days`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="number" x-model="w.milk_days" :name="`withdrawal_periods[${i}][milk_days]`" :placeholder="'{{ __('messages.pages.products.submission.milk_days_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`withdrawal_periods.${i}.milk_days`] }"
                @input="clearError(`withdrawal_periods.${i}.milk_days`)">
              <template x-if="errors[`withdrawal_periods.${i}.milk_days`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`withdrawal_periods.${i}.milk_days`][0]"></span>
                </p>
              </template>
            </div>
            <div>
              <input type="number" x-model="w.egg_days" :name="`withdrawal_periods[${i}][egg_days]`" :placeholder="'{{ __('messages.pages.products.submission.egg_days_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`withdrawal_periods.${i}.egg_days`] }"
                @input="clearError(`withdrawal_periods.${i}.egg_days`)">
              <template x-if="errors[`withdrawal_periods.${i}.egg_days`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`withdrawal_periods.${i}.egg_days`][0]"></span>
                </p>
              </template>
            </div>
          </div>
          <div>
            <textarea x-model="w.notes" :name="`withdrawal_periods[${i}][notes]`" rows="1" :placeholder="'{{ __('messages.pages.products.submission.notes_placeholder') }}'"
              class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"
              :class="{ 'border-red-600': errors[`withdrawal_periods.${i}.notes`] }"
              @input="clearError(`withdrawal_periods.${i}.notes`)"></textarea>
            <template x-if="errors[`withdrawal_periods.${i}.notes`]">
              <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                <span x-text="errors[`withdrawal_periods.${i}.notes`][0]"></span>
              </p>
            </template>
          </div>
        </div>
      </template>
    </div>

    {{-- Media --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-6">
      <template x-if="hasSectionError('documents') || hasSectionError('image_urls')">
        <div class="border-s-2 border-red-600 ps-3 py-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1.5">
          <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
          <span>{{ __('messages.pages.products.submission.section_required') }}</span>
        </div>
      </template>
      <h2 class="text-lg font-bold text-heading flex items-center gap-2">
        <x-lucide-image class="w-5 h-5 text-brand" />
        {{ __('messages.pages.products.submission.media') }}
      </h2>

      {{-- Image URLs --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-heading">{{ __('messages.pages.products.submission.image_urls') }}</h3>
          <button type="button" @click="addImageUrl()"
            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
            <x-lucide-plus class="w-3.5 h-3.5" /> {{ __('messages.pages.products.submission.add') }}
          </button>
        </div>
        <template x-for="(_, i) in imageUrls" :key="i">
          <div class="flex items-start gap-3">
            <div class="flex-1">
              <input type="url" x-model="imageUrls[i]" :name="`image_urls[${i}]`" :placeholder="'{{ __('messages.pages.products.submission.image_url_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                :class="{ 'border-red-600': errors[`image_urls.${i}`] }"
                @input="clearError(`image_urls.${i}`)">
              <template x-if="errors[`image_urls.${i}`]">
                <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                  <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                  <span x-text="errors[`image_urls.${i}`][0]"></span>
                </p>
              </template>
            </div>
            <button type="button" @click="removeItem('imageUrls', i)"
              class="shrink-0 p-1.5 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
              <x-lucide-x class="w-4 h-4" />
            </button>
          </div>
        </template>
      </div>

      {{-- Documents --}}
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-heading">{{ __('messages.pages.products.submission.documents') }}</h3>
          <button type="button" @click="addDocument()"
            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base">
            <x-lucide-plus class="w-3.5 h-3.5" /> {{ __('messages.pages.products.submission.add') }}
          </button>
        </div>
        <template x-for="(doc, i) in documents" :key="i">
          <div class="p-4 bg-neutral-secondary-soft rounded-base border border-default-medium space-y-3">
            <div class="flex items-start justify-between">
              <span class="text-xs font-semibold text-body uppercase tracking-wide" x-text="`{{ __('messages.pages.products.submission.document_entry') }} #${i + 1}`"></span>
              <button type="button" @click="removeItem('documents', i)"
                class="p-1 text-body hover:text-fg-danger-strong hover:bg-danger-soft rounded-base transition-colors">
                <x-lucide-trash-2 class="w-4 h-4" />
              </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <input type="text" x-model="doc.title" :name="`documents[${i}][title]`" :placeholder="'{{ __('messages.pages.products.submission.document_title_placeholder') }}'"
                  class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                  :class="{ 'border-red-600': errors[`documents.${i}.title`] }"
                  @input="clearError(`documents.${i}.title`)">
                <template x-if="errors[`documents.${i}.title`]">
                    <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                      <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                    <span x-text="errors[`documents.${i}.title`][0]"></span>
                  </p>
                </template>
              </div>
              <div>
                <input type="url" x-model="doc.url" :name="`documents[${i}][url]`" :placeholder="'{{ __('messages.pages.products.submission.document_url_placeholder') }}'"
                  class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                  :class="{ 'border-red-600': errors[`documents.${i}.url`] }"
                  @input="clearError(`documents.${i}.url`)">
                <template x-if="errors[`documents.${i}.url`]">
                    <p class="mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1">
                      <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                    <span x-text="errors[`documents.${i}.url`][0]"></span>
                  </p>
                </template>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end gap-3">
      <a href="{{ route('products.index') }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-heading bg-neutral-primary-soft border border-default-medium rounded-base hover:bg-neutral-secondary-soft focus:ring-4 focus:ring-brand-soft dark:bg-slate-700 dark:text-white dark:border-slate-600 dark:hover:bg-slate-600">
        {{ __('messages.pages.products.submission.cancel') }}
      </a>
      <button type="submit" :disabled="submitting"
        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base shadow-xs transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
        <svg x-show="submitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <x-lucide-send class="w-4 h-4" x-show="!submitting" />
        <span x-show="!submitting">{{ __('messages.pages.products.submission.submit') }}</span>
        <span x-show="submitting">{{ __('messages.pages.products.submission.submitting') }}</span>
      </button>
    </div>
  </form>
</div>
@endsection
