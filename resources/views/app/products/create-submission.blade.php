@extends('app.layouts.master')

@section('title', __('messages.pages.products.submission.title'))

@section('content')
<x-toast id="submissionToast" type="success" title="" message="" />

<div class="max-w-4xl mx-auto space-y-8 py-8 sm:py-12">

  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand to-brand-strong dark:from-sky-800 dark:to-sky-950 px-8 sm:px-12 lg:px-16 py-14 sm:py-18 shadow-sm mt-6">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium text-blue-200 dark:text-sky-200 border border-white/10 dark:border-sky-700 mb-5">
        <x-lucide-package-plus class="w-4 h-4" />
        <span>{{ __('messages.pages.products.submission.badge') }}</span>
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] tracking-tight">
        {{ __('messages.pages.products.submission.heading') }}
      </h1>
      <div class="w-16 h-1 bg-blue-400 dark:bg-sky-400 rounded-full mt-6 mb-6"></div>
      <p class="text-lg sm:text-xl text-slate-300 dark:text-sky-200 max-w-xl leading-relaxed">
        {{ __('messages.pages.products.submission.subtitle') }}
      </p>
    </div>
  </div>

  @if (session('success'))
    <div class="bg-success-soft border border-success-subtle text-fg-success-strong rounded-base p-4 text-sm" role="alert">
      <span class="font-medium">{{ session('success') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="bg-danger-soft border border-danger-subtle text-fg-danger-strong rounded-base p-4 text-sm" role="alert">
      <ul class="list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form
    method="POST"
    action="{{ route('products.submission.store') }}"
    class="space-y-6"
    x-data="{
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
    }"
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
          <label for="submitted_by_name" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.name_label') }}</label>
          <input type="text" name="submitted_by_name" id="submitted_by_name" value="{{ old('submitted_by_name') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('submitted_by_name') border-danger-subtle @enderror"
            placeholder="{{ __('messages.pages.products.submission.name_placeholder') }}">
        </div>
        <div>
          <label for="submitted_by_email" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.email_label') }}</label>
          <input type="email" name="submitted_by_email" id="submitted_by_email" value="{{ old('submitted_by_email') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('submitted_by_email') border-danger-subtle @enderror"
            placeholder="{{ __('messages.pages.products.submission.email_placeholder') }}">
        </div>
        <div>
          <label for="submitted_by_phone" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.phone_label') }}</label>
          <input type="text" name="submitted_by_phone" id="submitted_by_phone" value="{{ old('submitted_by_phone') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('submitted_by_phone') border-danger-subtle @enderror"
            placeholder="{{ __('messages.pages.products.submission.phone_placeholder') }}">
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
          <label for="trade_name" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.trade_name_label') }}</label>
          <input type="text" name="trade_name" id="trade_name" value="{{ old('trade_name') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('trade_name') border-danger-subtle @enderror"
            placeholder="{{ __('messages.pages.products.submission.trade_name_placeholder') }}">
        </div>
        <div>
          <label for="trade_name_ar" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.trade_name_ar_label') }}</label>
          <input type="text" name="trade_name_ar" id="trade_name_ar" value="{{ old('trade_name_ar') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('trade_name_ar') border-danger-subtle @enderror"
            placeholder="{{ __('messages.pages.products.submission.trade_name_ar_placeholder') }}">
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="product_type" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.product_type_label') }}</label>
          <select name="product_type" id="product_type"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            <option value="pharmaceutical" @selected(old('product_type') === 'pharmaceutical')>{{ __('messages.pages.products.submission.type_pharmaceutical') }}</option>
            <option value="vaccine" @selected(old('product_type') === 'vaccine')>{{ __('messages.pages.products.submission.type_vaccine') }}</option>
            <option value="supplement" @selected(old('product_type') === 'supplement')>{{ __('messages.pages.products.submission.type_supplement') }}</option>
            <option value="feed_additive" @selected(old('product_type') === 'feed_additive')>{{ __('messages.pages.products.submission.type_feed_additive') }}</option>
            <option value="disinfectant" @selected(old('product_type') === 'disinfectant')>{{ __('messages.pages.products.submission.type_disinfectant') }}</option>
            <option value="biological" @selected(old('product_type') === 'biological')>{{ __('messages.pages.products.submission.type_biological') }}</option>
          </select>
        </div>
        <div>
          <label for="package_size" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.package_size_label') }}</label>
          <input type="text" name="package_size" id="package_size" value="{{ old('package_size') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white"
            placeholder="{{ __('messages.pages.products.submission.package_size_placeholder') }}">
        </div>
      </div>
      <div>
        <label for="storage_conditions" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.storage_label') }}</label>
        <textarea name="storage_conditions" id="storage_conditions" rows="2"
          class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y">{{ old('storage_conditions') }}</textarea>
      </div>
      <div>
        <label for="description" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.description_label') }}</label>
        <textarea name="description" id="description" rows="4"
          class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y">{{ old('description') }}</textarea>
      </div>
      <div>
        <label for="description_ar" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.description_ar_label') }}</label>
        <textarea name="description_ar" id="description_ar" rows="3"
          class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y">{{ old('description_ar') }}</textarea>
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
          <label for="company" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.company_label') }}</label>
          <input type="text" name="company" id="company" value="{{ old('company') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('company') border-danger-subtle @enderror"
            placeholder="{{ __('messages.pages.products.submission.company_placeholder') }}">
        </div>
        <div>
          <label for="dosage_form" class="block text-sm font-medium text-heading mb-1.5">{{ __('messages.pages.products.submission.dosage_form_label') }}</label>
          <input type="text" name="dosage_form" id="dosage_form" value="{{ old('dosage_form') }}"
            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white @error('dosage_form') border-danger-subtle @enderror"
            placeholder="{{ __('messages.pages.products.submission.dosage_form_placeholder') }}">
        </div>
      </div>
    </div>

    {{-- Active Ingredients --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-heading flex items-center gap-2">
          <x-lucide-flask-conical class="w-5 h-5 text-brand" />
          {{ __('messages.pages.products.submission.active_ingredients') }}
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="text" x-model="ing.strength" :name="`active_ingredients[${i}][strength]`" :placeholder="'{{ __('messages.pages.products.submission.strength_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="text" x-model="ing.unit" :name="`active_ingredients[${i}][unit]`" :placeholder="'{{ __('messages.pages.products.submission.unit_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
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
              class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"></textarea>
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"></textarea>
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"></textarea>
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"></textarea>
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
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-heading flex items-center gap-2">
          <x-lucide-syringe class="w-5 h-5 text-brand" />
          {{ __('messages.pages.products.submission.dosages') }}
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="text" x-model="d.dosage" :name="`dosages[${i}][dosage]`" :placeholder="'{{ __('messages.pages.products.submission.dosage_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="text" x-model="d.route" :name="`dosages[${i}][route]`" :placeholder="'{{ __('messages.pages.products.submission.route_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="text" x-model="d.duration" :name="`dosages[${i}][duration]`" :placeholder="'{{ __('messages.pages.products.submission.duration_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
          </div>
          <div>
            <textarea x-model="d.notes" :name="`dosages[${i}][notes]`" rows="1" :placeholder="'{{ __('messages.pages.products.submission.notes_placeholder') }}'"
              class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"></textarea>
          </div>
        </div>
      </template>
    </div>

    {{-- Withdrawal Periods --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-heading flex items-center gap-2">
          <x-lucide-clock class="w-5 h-5 text-brand" />
          {{ __('messages.pages.products.submission.withdrawal_periods') }}
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="number" x-model="w.meat_days" :name="`withdrawal_periods[${i}][meat_days]`" :placeholder="'{{ __('messages.pages.products.submission.meat_days_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="number" x-model="w.milk_days" :name="`withdrawal_periods[${i}][milk_days]`" :placeholder="'{{ __('messages.pages.products.submission.milk_days_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
            <div>
              <input type="number" x-model="w.egg_days" :name="`withdrawal_periods[${i}][egg_days]`" :placeholder="'{{ __('messages.pages.products.submission.egg_days_placeholder') }}'"
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
            </div>
          </div>
          <div>
            <textarea x-model="w.notes" :name="`withdrawal_periods[${i}][notes]`" rows="1" :placeholder="'{{ __('messages.pages.products.submission.notes_placeholder') }}'"
              class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white resize-y"></textarea>
          </div>
        </div>
      </template>
    </div>

    {{-- Media --}}
    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-6 space-y-6">
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
                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
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
                  class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
              </div>
              <div>
                <input type="url" x-model="doc.url" :name="`documents[${i}][url]`" :placeholder="'{{ __('messages.pages.products.submission.document_url_placeholder') }}'"
                  class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2 shadow-xs placeholder:text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
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
      <button type="submit"
        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base shadow-xs transition-colors">
        <x-lucide-send class="w-4 h-4" />
        {{ __('messages.pages.products.submission.submit') }}
      </button>
    </div>
  </form>
</div>
@endsection
