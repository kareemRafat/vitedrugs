@extends('app.layouts.master')

@section('title', $disease->name . ' | VetPedia')

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags($disease->description ?? ''), 160) }}
@endsection

@section('meta_keywords')
    {{ $disease->name }}, veterinary disease
@endsection

@section('og_title', $disease->name)

@section('og_description')
    {{ \Illuminate\Support\Str::limit($disease->description, 160) }}
@endsection

@section('content')
    @php
        $payload = is_array($disease->knowledge_payload) ? $disease->knowledge_payload : [];
        $classification = $disease->diseaseClassification;
        $isArabic = app()->getLocale() === 'ar';
        $payloadSigns = collect($payload['clinical_signs'] ?? []);
        $localizedName = fn (string $field, array $item): string => $isArabic && filled($item[$field.'_ar'] ?? null)
            ? $item[$field.'_ar']
            : ($item[$field] ?? '');
        $payloadSections = [
            'postmortem_findings' => ['label' => __('messages.diseases.postmortem_findings'), 'icon' => 'microscope', 'items' => collect($payload['postmortem_findings'] ?? [])->filter(fn ($i) => filled($i['display_name'] ?? null))],
            'diagnosis' => ['label' => __('messages.diseases.diagnosis'), 'icon' => 'test-tube-2', 'items' => collect($payload['diagnosis'] ?? [])->filter(fn ($i) => filled($i['method'] ?? null))],
            'treatment' => ['label' => __('messages.diseases.treatment'), 'icon' => 'syringe', 'items' => collect($payload['treatment'] ?? [])->filter(fn ($i) => filled($i['intervention'] ?? null))],
            'prevention_control' => ['label' => __('messages.diseases.prevention_control'), 'icon' => 'shield-check', 'items' => collect($payload['prevention_control'] ?? [])->filter(fn ($i) => filled($i['measure'] ?? null))],
        ];
        $payloadReferences = collect($payload['references'] ?? [])->filter(fn ($r) => filled($r['title'] ?? null));
    @endphp

    {{-- Breadcrumb --}}
    <nav class="flex mb-4 pt-4" aria-label="Breadcrumb">
        <ol class="flex items-start flex-wrap gap-x-1 text-base text-body dark:text-slate-400">
            <li class="inline-flex items-center">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.parts.drugs') }}</a>
            </li>
            <li>
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <a href="{{ route('diseases.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-white">{{ __('messages.diseases.diseases') }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center gap-1">
                    <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180" />
                    <span class="text-heading dark:text-white font-medium">{{ $disease->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

        {{-- Main Column --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">

            {{-- Disease Overview --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 dark:bg-slate-800">
                <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $disease->name }}</h1>
                @if ($disease->name_ar && app()->getLocale() === 'ar')
                    <p class="text-body dark:text-slate-400 text-base mb-2">{{ $disease->name_ar }}</p>
                @endif
                @if ($disease->description)
                    <p class="text-base text-body dark:text-slate-400">{{ $disease->description }}</p>
                @endif

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-package class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-400 mb-1">{{ __('messages.diseases.related_products') }}</p>
                            <p class="text-base font-bold text-slate-900 dark:text-white">{{ $disease->products->count() }}</p>
                        </div>
                    </div>
                    <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                        <x-lucide-flask-conical class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                        <div class="relative">
                            <p class="text-xs font-semibold uppercase text-emerald-600 dark:text-emerald-400 mb-1">{{ __('messages.diseases.ingredients_count') }}</p>
                            <p class="text-base font-bold text-slate-900 dark:text-white">{{ $ingredients->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Clinical Signs --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-stethoscope class="w-4 h-4 text-body dark:text-slate-400" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.clinical_signs') }}</h2>
                </div>
                <div class="p-5 space-y-5">
                    @if ($clinicalSigns->isNotEmpty())
                        @foreach ($clinicalSigns as $group => $signs)
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wider text-body dark:text-slate-400 mb-2">{{ $group }}</h3>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($signs as $sign)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-neutral-secondary-soft border border-default-medium text-heading dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                            {{ $sign->localized_display_name }}
                                            @if ($sign->pivot?->is_pathognomonic)
                                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-brand text-white">{{ __('messages.diseases.pathognomonic') }}</span>
                                            @elseif ($sign->pivot?->is_required)
                                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('messages.diseases.required') }}</span>
                                            @elseif ($sign->pivot?->is_specific)
                                                <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('messages.diseases.specific') }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @elseif ($payloadSigns->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($payloadSigns as $sign)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded bg-neutral-secondary-soft border border-default-medium text-body dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    {{ $isArabic && filled($sign['display_name_ar'] ?? null) ? $sign['display_name_ar'] : ($sign['display_name'] ?? $sign['canonical_name']) }}
                                    @if (! empty($sign['is_pathognomonic']))
                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-brand text-white">{{ __('messages.diseases.pathognomonic') }}</span>
                                    @elseif (! empty($sign['is_required']))
                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('messages.diseases.required') }}</span>
                                    @elseif (! empty($sign['is_specific']))
                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('messages.diseases.specific') }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-body dark:text-slate-400">{{ __('messages.diseases.no_data') }}</p>
                    @endif
                </div>
            </div>

            {{-- Knowledge payload sections --}}
            @foreach ($payloadSections as $key => $section)
                @if ($section['items']->isNotEmpty())
                    <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-dynamic-component :component="'lucide-' . $section['icon']" class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ $section['label'] }}</h2>
                        </div>
                        <div class="p-5 space-y-4">
                            @foreach ($section['items'] as $item)
                                <div class="flex items-start gap-3">
                                    <x-lucide-check-circle class="w-4 h-4 shrink-0 mt-0.5 text-brand dark:text-brand" />
                                    <div>
                                        @php
                                            $field = $key === 'diagnosis' ? 'method' : ($key === 'treatment' ? 'intervention' : ($key === 'prevention_control' ? 'measure' : 'display_name'));
                                            $title = $localizedName($field, $item);
                                            $description = $isArabic && filled($item['description_ar'] ?? null) ? $item['description_ar'] : ($item['description'] ?? null);
                                        @endphp
                                        <h3 class="text-sm font-semibold text-heading dark:text-white">{{ $title }}</h3>
                                        @if (filled($description))
                                            <p class="text-sm text-body dark:text-slate-400 mt-0.5 leading-relaxed">{{ $description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- References --}}
            @if ($payloadReferences->isNotEmpty())
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-book-marked class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.references') }}</h2>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2">
                            @foreach ($payloadReferences as $reference)
                                <li>
                                    <a href="{{ $reference['url'] ?? '#' }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-medium text-fg-brand hover:underline dark:text-brand">
                                        <x-lucide-external-link class="w-3.5 h-3.5 shrink-0" />
                                        {{ $reference['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Related Products --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-package class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.products_title') }}</h2>
                </div>
                <div class="overflow-x-auto">
                    @if ($disease->products->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-8">{{ __('messages.diseases.no_products') }}</p>
                    @else
                        <table class="w-full text-base text-left rtl:text-right text-heading dark:text-white">
                            <thead class="text-base uppercase text-body bg-neutral-secondary-soft dark:bg-slate-700 dark:text-slate-400">
                                <tr>
                                    <th scope="col" class="px-5 py-3">{{ __('messages.diseases.trade_name') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.diseases.dosage_form') }}</th>
                                    <th scope="col" class="px-5 py-3 hidden md:table-cell">{{ __('messages.diseases.manufacturer') }}</th>
                                    <th scope="col" class="px-5 py-3 w-20">{{ __('messages.diseases.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($disease->products as $product)
                                    <tr class="border-b border-default-medium dark:border-slate-700">
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" wire:navigate class="font-medium text-fg-brand hover:underline">
                                                {{ $product->trade_name }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell text-body dark:text-slate-400 text-base">
                                            {{ $product->dosageForm?->name ?? __('messages.diseases.na') }}
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell">
                                            @php
                                                $manufacturer = $product->companies->first(fn($c) => $c->pivot?->role === 'manufacturer');
                                            @endphp
                                            @if ($manufacturer)
                                                <a href="{{ route('companies.show', $manufacturer) }}" wire:navigate class="font-medium text-fg-brand hover:underline">
                                                    {{ $manufacturer->name }}
                                                </a>
                                            @else
                                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.na') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('products.show', $product) }}" wire:navigate
                                                class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm">
                                                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-5 xl:col-span-4 space-y-4">

            {{-- Active Ingredients --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-pill class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.active_ingredients') }}</h2>
                </div>
                <div class="p-5">
                    @if ($ingredients->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.diseases.no_active_ingredients') }}</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($ingredients as $ingredient)
                                <li>
                                    <a href="{{ route('active-ingredients.show', $ingredient) }}" wire:navigate class="text-base font-medium text-fg-brand hover:underline">
                                        {{ $ingredient->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Host Species --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-paw-print class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.host_species') }}</h2>
                </div>
                <div class="p-5">
                    @if ($disease->hostSpecies->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.diseases.no_data') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($disease->hostSpecies as $species)
                                <div class="flex flex-wrap items-center gap-2 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft dark:bg-slate-700 dark:border-slate-600">
                                    <span class="text-sm font-medium text-heading dark:text-white">{{ $species->localized_display_name }}</span>
                                    @php
                                        $tags = [];
                                        if ($species->pivot?->is_primary_host) { $tags[] = __('messages.diseases.primary_host'); }
                                        if ($species->pivot?->is_reservoir) { $tags[] = __('messages.diseases.reservoir'); }
                                        if ($species->pivot?->is_vector) { $tags[] = __('messages.diseases.vector'); }
                                        if ($species->pivot?->is_carrier) { $tags[] = __('messages.diseases.carrier'); }
                                        if ($species->pivot?->is_incidental_host) { $tags[] = __('messages.diseases.incidental'); }
                                    @endphp
                                    @foreach ($tags as $tag)
                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ $tag }}</span>
                                    @endforeach
                                    @if ($species->pivot?->susceptibility)
                                        <span class="ms-auto text-xs text-body dark:text-slate-400">{{ __('messages.diseases.susceptibility') }}: {{ $species->pivot->susceptibility }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Microorganisms --}}
            <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                    <x-lucide-microscope class="w-4 h-4 text-body" />
                    <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.microorganisms') }}</h2>
                </div>
                <div class="p-5">
                    @if ($disease->microorganisms->isEmpty())
                        <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ __('messages.diseases.no_data') }}</p>
                    @else
                        <div class="space-y-2">
                            @foreach ($disease->microorganisms as $microorganism)
                                <a href="{{ route('large-animals.microorganisms.show', $microorganism->slug) }}" wire:navigate
                                    class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                    <span class="text-sm font-medium text-heading dark:text-white">{{ $microorganism->display_name ?? $microorganism->name }}</span>
                                    @if ($microorganism->pivot?->role === 'cause')
                                        <span class="px-1.5 py-0.5 text-xs font-semibold rounded bg-danger-soft text-fg-danger-strong dark:bg-red-950 dark:text-red-300">{{ __('messages.diseases.cause') }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Classification --}}
            @if ($classification)
                <div class="bg-neutral-primary-soft rounded-base shadow-sm dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-clipboard-list class="w-4 h-4 text-body" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('messages.diseases.classification') }}</h2>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        @if ($classification->etiology_type)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.etiology_type') }}</span>
                                <span class="font-medium text-heading dark:text-white">{{ $classification->etiology_type }}</span>
                            </div>
                        @endif
                        @if ($classification->zoonotic)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.zoonotic') }}</span>
                                <span class="font-medium text-heading dark:text-white">{{ $classification->zoonotic ? __('messages.diseases.yes') : __('messages.diseases.no') }}</span>
                            </div>
                        @endif
                        @if ($classification->notifiable)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.notifiable') }}</span>
                                <span class="font-medium text-heading dark:text-white">{{ $classification->notifiable ? __('messages.diseases.yes') : __('messages.diseases.no') }}</span>
                            </div>
                        @endif
                        @if ($classification->transmissibility)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.transmissibility') }}</span>
                                <span class="font-medium text-heading dark:text-white">{{ $classification->transmissibility }}</span>
                            </div>
                        @endif
                        @if ($classification->infectiousness)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.infectiousness') }}</span>
                                <span class="font-medium text-heading dark:text-white">{{ $classification->infectiousness }}</span>
                            </div>
                        @endif
                        @if ($classification->oie_category)
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.oie_category') }}</span>
                                <span class="font-medium text-heading dark:text-white">{{ $classification->oie_category }}</span>
                            </div>
                        @endif
                        @if ($classification->occurrence_patterns)
                            <div>
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.occurrence_patterns') }}</span>
                                <p class="mt-1 font-medium text-heading dark:text-white">{{ is_array($classification->occurrence_patterns) ? implode(', ', $classification->occurrence_patterns) : $classification->occurrence_patterns }}</p>
                            </div>
                        @endif
                        @if ($classification->disease_courses)
                            <div>
                                <span class="text-body dark:text-slate-400">{{ __('messages.diseases.disease_courses') }}</span>
                                <p class="mt-1 font-medium text-heading dark:text-white">{{ is_array($classification->disease_courses) ? implode(', ', $classification->disease_courses) : $classification->disease_courses }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>
@endsection