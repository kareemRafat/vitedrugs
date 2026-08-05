@extends('app.layouts.master')

@section('title', __('large-animals.disease.title', ['name' => $disease->name]))

@section('meta_description')
    {{ \Illuminate\Support\Str::limit(strip_tags(app()->getLocale() === 'ar' && $disease->description_ar ? $disease->description_ar : ($disease->description ?? '')), 160) }}
@endsection

@section('og_title', app()->getLocale() === 'ar' && $disease->name_ar ? $disease->name_ar : $disease->name)

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
            'postmortem_findings' => ['label' => __('large-animals.disease.postmortem_findings'), 'icon' => 'microscope', 'items' => collect($payload['postmortem_findings'] ?? [])->filter(fn ($i) => filled($i['display_name'] ?? null))],
            'diagnosis' => ['label' => __('large-animals.disease.diagnosis'), 'icon' => 'test-tube-2', 'items' => collect($payload['diagnosis'] ?? [])->filter(fn ($i) => filled($i['method'] ?? null))],
            'treatment' => ['label' => __('large-animals.disease.treatment'), 'icon' => 'syringe', 'items' => collect($payload['treatment'] ?? [])->filter(fn ($i) => filled($i['intervention'] ?? null))],
            'prevention_control' => ['label' => __('large-animals.disease.prevention_control'), 'icon' => 'shield-check', 'items' => collect($payload['prevention_control'] ?? [])->filter(fn ($i) => filled($i['measure'] ?? null))],
        ];
    @endphp

    <div class="space-y-4">

        {{-- Breadcrumb --}}
        <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-body dark:text-slate-400 pt-4" aria-label="{{ __('messages.nav.breadcrumb') }}">
            <a href="{{ route('large-animals.home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.drugs') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium">{{ $isArabic && $disease->name_ar ? $disease->name_ar : $disease->name }}</span>
        </nav>

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="$isArabic && $disease->name_ar ? $disease->name_ar : $disease->name"
            :subtitle="\Illuminate\Support\Str::limit(strip_tags($isArabic && $disease->description_ar ? $disease->description_ar : ($disease->description ?? '')), 220)"
            :badge="__('large-animals.hero.badge.disease')"
            badgeIcon="activity"
            :stats="[
                ['count' => $disease->clinicalSigns->count(), 'label' => __('large-animals.hero.stats.clinical_signs'), 'icon' => 'stethoscope'],
                ['count' => $disease->hostSpecies->count(), 'label' => __('large-animals.hero.stats.species'), 'icon' => 'paw-print'],
            ]"
        >
            @if ($disease->name_ar && app()->getLocale() === 'ar')
                <p class="text-white/85 text-base">{{ $disease->name_ar }}</p>
            @endif
            <a href="{{ route('large-animals.diseases.article', $disease->slug) }}" wire:navigate
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-white/15 hover:bg-white/25 border border-white/30 rounded-base transition-colors">
                <x-lucide-file-text class="w-4 h-4" />
                {{ __('large-animals.disease.read_article') }}
            </a>
        </x-large-animals.page-hero>

        {{-- Classification chips --}}
        @if ($classification)
            <div class="bg-neutral-primary-soft rounded-base shadow-xs px-5 py-3 flex flex-wrap gap-2 dark:bg-slate-800">
                @if ($classification->etiology_type)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-base bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">
                        <x-lucide-bug class="w-3.5 h-3.5" />
                        {{ $classification->etiology_type }}
                    </span>
                @endif
                @if ($classification->zoonotic)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-base bg-danger-soft border border-danger-subtle text-fg-danger-strong dark:bg-red-950 dark:border-red-900 dark:text-red-300">
                        <x-lucide-users class="w-3.5 h-3.5" />
                        {{ __('large-animals.disease.zoonotic') }}
                    </span>
                @endif
                @if ($classification->notifiable)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-base bg-success-soft border border-success-subtle text-fg-success-strong dark:bg-green-950 dark:border-green-900 dark:text-green-300">
                        <x-lucide-bell-ring class="w-3.5 h-3.5" />
                        {{ __('large-animals.disease.notifiable') }}
                    </span>
                @endif
                @if ($classification->oie_category)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-base bg-neutral-secondary-soft border border-default-medium text-body dark:bg-slate-700 dark:border-slate-600 dark:text-slate-300">
                        {{ __('large-animals.disease.oie_category') }}: {{ $classification->oie_category }}
                    </span>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Main column --}}
            <div class="lg:col-span-8 space-y-4">

                {{-- Clinical signs (grouped) --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-stethoscope class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.disease.clinical_signs') }}</h2>
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
                                                    <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand text-white">{{ __('large-animals.disease.pathognomonic') }}</span>
                                                @elseif ($sign->pivot?->is_required)
                                                    <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.disease.required') }}</span>
                                                @elseif ($sign->pivot?->is_specific)
                                                    <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.disease.specific') }}</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @elseif ($payloadSigns->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($payloadSigns as $sign)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-base bg-neutral-secondary-soft border border-default-medium text-heading dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                        {{ $isArabic && filled($sign['display_name_ar'] ?? null) ? $sign['display_name_ar'] : ($sign['display_name'] ?? $sign['canonical_name']) }}
                                        @if (! empty($sign['is_pathognomonic']))
                                            <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand text-white">{{ __('large-animals.disease.pathognomonic') }}</span>
                                        @elseif (! empty($sign['is_required']))
                                            <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.disease.required') }}</span>
                                        @elseif (! empty($sign['is_specific']))
                                            <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.disease.specific') }}</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.disease.no_data') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Knowledge payload sections --}}
                @foreach ($payloadSections as $key => $section)
                    @if ($section['items']->isNotEmpty())
                        <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
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
                @if (collect($payload['references'] ?? [])->filter(fn ($r) => filled($r['title'] ?? null))->isNotEmpty())
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-lucide-book-marked class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.disease.references') }}</h2>
                        </div>
                        <div class="p-5">
                            <ul class="space-y-2">
                                @foreach ($payload['references'] as $reference)
                                    @if (filled($reference['title'] ?? null))
                                        <li>
                                            <a href="{{ $reference['url'] ?? '#' }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-medium text-fg-brand hover:underline dark:text-brand">
                                                <x-lucide-external-link class="w-3.5 h-3.5 shrink-0" />
                                                {{ $reference['title'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Related products --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-package class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.disease.related_products') }}</h2>
                    </div>
                    <div class="p-5">
                        @if ($disease->products->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.disease.no_related_products') }}</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($disease->products as $product)
                                    <a href="{{ route('products.show', $product) }}" wire:navigate
                                        class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                        <span class="text-sm font-medium text-heading dark:text-white">{{ $product->trade_name }}</span>
                                        <x-lucide-arrow-right class="w-4 h-4 shrink-0 text-body rtl:rotate-180 dark:text-slate-400" />
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-4 space-y-4">

                {{-- Classification --}}
                @if ($classification)
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-lucide-clipboard-list class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.disease.classification') }}</h2>
                        </div>
                        <div class="p-5 space-y-3 text-sm">
                            @if ($classification->etiology_type)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.etiology_type') }}</span>
                                    <span class="font-medium text-heading dark:text-white">{{ $classification->etiology_type }}</span>
                                </div>
                            @endif
                            @if ($classification->zoonotic)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.zoonotic') }}</span>
                                    <span class="font-medium text-heading dark:text-white">{{ $classification->zoonotic ? __('large-animals.disease.yes') : __('large-animals.disease.no') }}</span>
                                </div>
                            @endif
                            @if ($classification->notifiable)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.notifiable') }}</span>
                                    <span class="font-medium text-heading dark:text-white">{{ $classification->notifiable ? __('large-animals.disease.yes') : __('large-animals.disease.no') }}</span>
                                </div>
                            @endif
                            @if ($classification->transmissibility)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.transmissibility') }}</span>
                                    <span class="font-medium text-heading dark:text-white">{{ $classification->transmissibility }}</span>
                                </div>
                            @endif
                            @if ($classification->infectiousness)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.infectiousness') }}</span>
                                    <span class="font-medium text-heading dark:text-white">{{ $classification->infectiousness }}</span>
                                </div>
                            @endif
                            @if ($classification->oie_category)
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.oie_category') }}</span>
                                    <span class="font-medium text-heading dark:text-white">{{ $classification->oie_category }}</span>
                                </div>
                            @endif
                            @if ($classification->occurrence_patterns)
                                <div>
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.occurrence_patterns') }}</span>
                                    <p class="mt-1 font-medium text-heading dark:text-white">{{ is_array($classification->occurrence_patterns) ? implode(', ', $classification->occurrence_patterns) : $classification->occurrence_patterns }}</p>
                                </div>
                            @endif
                            @if ($classification->disease_courses)
                                <div>
                                    <span class="text-body dark:text-slate-400">{{ __('large-animals.disease.disease_courses') }}</span>
                                    <p class="mt-1 font-medium text-heading dark:text-white">{{ is_array($classification->disease_courses) ? implode(', ', $classification->disease_courses) : $classification->disease_courses }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Host species --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-paw-print class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.disease.host_species') }}</h2>
                    </div>
                    <div class="p-5">
                        @if ($disease->hostSpecies->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.disease.no_data') }}</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($disease->hostSpecies as $species)
                                    <div class="flex flex-wrap items-center gap-2 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft dark:bg-slate-700 dark:border-slate-600">
                                        <span class="text-sm font-medium text-heading dark:text-white">{{ $species->localized_display_name }}</span>
                                        @php
                                            $tags = [];
                                            if ($species->pivot?->is_primary_host) { $tags[] = __('large-animals.disease.primary_host'); }
                                            if ($species->pivot?->is_reservoir) { $tags[] = __('large-animals.disease.reservoir'); }
                                            if ($species->pivot?->is_vector) { $tags[] = __('large-animals.disease.vector'); }
                                            if ($species->pivot?->is_carrier) { $tags[] = __('large-animals.disease.carrier'); }
                                            if ($species->pivot?->is_incidental_host) { $tags[] = __('large-animals.disease.incidental'); }
                                        @endphp
                                        @foreach ($tags as $tag)
                                            <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ $tag }}</span>
                                        @endforeach
                                        @if ($species->pivot?->susceptibility)
                                            <span class="ms-auto text-xs text-body dark:text-slate-400">{{ __('large-animals.disease.susceptibility') }}: {{ $species->pivot->susceptibility }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Microorganisms --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-microscope class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.disease.microorganisms') }}</h2>
                    </div>
                    <div class="p-5">
                        @if ($disease->microorganisms->isEmpty())
                            <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.disease.no_data') }}</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($disease->microorganisms as $microorganism)
                                    <a href="{{ route('large-animals.microorganisms.show', $microorganism->slug) }}" wire:navigate
                                        class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                        <span class="text-sm font-medium text-heading dark:text-white">{{ $microorganism->display_name ?? $microorganism->name }}</span>
                                        @if ($microorganism->pivot?->role === 'cause')
                                            <span class="px-1.5 py-0.5 text-xs font-semibold rounded-xs bg-danger-soft text-fg-danger-strong dark:bg-red-950 dark:text-red-300">{{ __('large-animals.microorganisms.cause') }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
