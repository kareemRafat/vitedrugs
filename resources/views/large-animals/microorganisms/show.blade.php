@extends('app.layouts.master')

@section('title', $microorganism->name)

@section('content')
    @php
        $taxonomy = [
            'Kingdom' => $microorganism->kingdom,
            'Phylum' => $microorganism->phylum,
            'Class' => $microorganism->class,
            'Order' => $microorganism->order,
            'Family' => $microorganism->family,
            'Genus' => $microorganism->genus,
            'Species' => $microorganism->species,
        ];
        $causes = $microorganism->diseases->filter(fn ($d) => ($d->pivot->role ?? null) === 'cause');
        $associated = $microorganism->diseases->filter(fn ($d) => ($d->pivot->role ?? null) !== 'cause');
    @endphp

    <div class="space-y-4">

        {{-- Breadcrumb --}}
        <nav class="flex mb-4 pt-4 flex-wrap items-center gap-x-2 gap-y-1 text-sm text-body dark:text-slate-400" aria-label="{{ __('messages.nav.breadcrumb') }}">
            <a href="{{ route('large-animals.home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('messages.parts.large_animals') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <a href="{{ route('large-animals.microorganisms.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.microorganisms') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium">{{ $microorganism->name }}</span>
        </nav>

        {{-- Hero --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-sm p-4 sm:p-6 dark:bg-slate-800">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-7 xl:col-span-8">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <h1 class="text-2xl sm:text-3xl font-bold text-heading dark:text-white mb-1">{{ $microorganism->display_name ?? $microorganism->name }}</h1>
                            @if ($microorganism->normalized_name && $microorganism->normalized_name !== $microorganism->name)
                                <p class="text-body dark:text-slate-400 text-sm mb-2">{{ $microorganism->normalized_name }}</p>
                            @endif
                        </div>
                        <a href="{{ route('large-animals.microorganisms.index') }}" wire:navigate
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-base transition-all duration-200 text-xs font-medium whitespace-nowrap text-body hover:text-brand hover:bg-brand/10 dark:text-slate-400 dark:hover:text-brand dark:hover:bg-brand/20">
                            <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                            <span>{{ __('large-animals.microorganisms.back') }}</span>
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if ($microorganism->microorganism_type)
                            <span class="inline-flex items-center px-3 py-1 rounded-base text-base font-medium bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand">{{ __('large-animals.microorganisms.types.' . $microorganism->microorganism_type) }}</span>
                        @endif
                        @if ($microorganism->is_pathogenic)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-base text-sm font-medium bg-danger-soft text-fg-danger-strong dark:bg-red-950 dark:text-red-300">
                                <x-lucide-alert-triangle class="w-3.5 h-3.5" />
                                {{ __('large-animals.microorganisms.pathogenic') }}
                            </span>
                        @endif
                    </div>
                    @if (filled($microorganism->tags))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ((array) $microorganism->tags as $tag)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-base text-sm font-medium bg-neutral-secondary-soft text-heading border border-default-medium dark:bg-slate-700 dark:text-white dark:border-slate-600">
                                    <x-lucide-tag class="w-3.5 h-3.5" />
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-5 xl:col-span-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                            <x-lucide-activity class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                            <div class="relative">
                                <p class="text-xs font-semibold uppercase text-blue-600 dark:text-blue-400 mb-1">{{ __('large-animals.microorganisms.disease_count') }}</p>
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ $microorganism->diseases->count() }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                            <x-lucide-pill class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                            <div class="relative">
                                <p class="text-xs font-semibold uppercase text-emerald-600 dark:text-emerald-400 mb-1">{{ __('large-animals.microorganisms.drug_count') }}</p>
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ $microorganism->activeIngredients->count() }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                            <x-lucide-microscope class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                            <div class="relative">
                                <p class="text-xs font-semibold uppercase text-amber-600 dark:text-amber-400 mb-1">{{ __('large-animals.microorganisms.type') }}</p>
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ $microorganism->microorganism_type ? __('large-animals.microorganisms.types.' . $microorganism->microorganism_type) : __('large-animals.microorganisms.no') }}</p>
                            </div>
                        </div>
                        <div class="relative overflow-hidden bg-slate-50 dark:bg-slate-800 rounded-lg p-4 shadow-sm border border-slate-200 dark:border-slate-700">
                            <x-lucide-alert-triangle class="absolute -bottom-3 -end-3 w-20 h-20 text-slate-300 dark:text-slate-700" />
                            <div class="relative">
                                <p class="text-xs font-semibold uppercase text-rose-600 dark:text-rose-400 mb-1">{{ __('large-animals.microorganisms.pathogenic') }}</p>
                                <p class="text-base font-bold text-slate-900 dark:text-white">{{ $microorganism->is_pathogenic ? __('large-animals.microorganisms.yes') : __('large-animals.microorganisms.no') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            {{-- Diseases --}}
            <div class="lg:col-span-8 space-y-4">

                {{-- Caused --}}
                @if ($causes->isNotEmpty())
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-lucide-biohazard class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.microorganisms.causes') }}</h2>
                            <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-danger-soft text-fg-danger-strong rounded-base dark:bg-red-950 dark:text-red-300">{{ $causes->count() }}</span>
                        </div>
                        <div class="p-5">
                            <div class="space-y-2">
                                @foreach ($causes as $disease)
                                    <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate
                                        class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                        <span class="text-sm font-medium text-heading dark:text-white">{{ $disease->name }}</span>
                                        <x-lucide-arrow-right class="w-4 h-4 shrink-0 text-body rtl:rotate-180 dark:text-slate-400" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Associated --}}
                @if ($associated->isNotEmpty())
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                            <x-lucide-link-2 class="w-4 h-4 text-body dark:text-slate-400" />
                            <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.microorganisms.associated') }}</h2>
                            <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $associated->count() }}</span>
                        </div>
                        <div class="p-5">
                            <div class="space-y-2">
                                @foreach ($associated as $disease)
                                    <a href="{{ route('large-animals.diseases.show', $disease->slug) }}" wire:navigate
                                        class="flex items-center justify-between gap-3 p-3 rounded-base border border-default-medium bg-neutral-secondary-soft hover:border-brand transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:border-brand">
                                        <span class="text-sm font-medium text-heading dark:text-white">{{ $disease->name }}</span>
                                        <x-lucide-arrow-right class="w-4 h-4 shrink-0 text-body rtl:rotate-180 dark:text-slate-400" />
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if ($microorganism->diseases->isEmpty())
                    <div class="bg-neutral-primary-soft rounded-base shadow-xs p-10 text-center dark:bg-slate-800">
                        <x-lucide-microscope class="w-12 h-12 text-body mx-auto mb-3 dark:text-slate-500" />
                        <p class="text-base text-body dark:text-slate-400">{{ __('large-animals.microorganisms.no_diseases') }}</p>
                    </div>
                @endif

                {{-- Antibiotics --}}
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-pill class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.microorganisms.drugs') }}</h2>
                        @if ($microorganism->activeIngredients->isNotEmpty())
                            <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">{{ $microorganism->activeIngredients->count() }}</span>
                        @endif
                    </div>
                    <div class="p-5">
                        @forelse ($microorganism->activeIngredients as $ingredient)
                            <div class="flex items-center justify-between gap-3 py-2.5 border-b border-default-medium last:border-b-0">
                                <span class="text-sm font-medium text-heading dark:text-white">{{ $ingredient->name }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-base
                                    {{ $ingredient->pivot->sensitivity === 'sensitive' ? 'bg-success-soft text-fg-success-strong dark:bg-green-950 dark:text-green-300'
                                        : ($ingredient->pivot->sensitivity === 'moderate' ? 'bg-brand-soft text-fg-brand dark:bg-brand/20 dark:text-brand'
                                        : 'bg-danger-soft text-fg-danger-strong dark:bg-red-950 dark:text-red-300') }}">
                                    {{ __('large-animals.microorganisms.sensitivity.' . $ingredient->pivot->sensitivity) }}
                                </span>
                            </div>
                            @if (filled($ingredient->pivot->notes))
                                <p class="text-sm text-body dark:text-slate-400 mt-1">{{ $ingredient->pivot->notes }}</p>
                            @endif
                        @empty
                            <div class="text-center py-6">
                                <x-lucide-pill class="w-10 h-10 text-body mx-auto mb-2 dark:text-slate-500" />
                                <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.microorganisms.no_drugs') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Taxonomy sidebar --}}
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-neutral-primary-soft rounded-base shadow-xs dark:bg-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                        <x-lucide-tree-pine class="w-4 h-4 text-body dark:text-slate-400" />
                        <h2 class="text-base font-semibold text-heading dark:text-white">{{ __('large-animals.microorganisms.taxonomy') }}</h2>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        @php $hasTaxonomy = false; @endphp
                        @foreach ($taxonomy as $rank => $value)
                            @if (filled($value))
                                @php $hasTaxonomy = true; @endphp
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-body dark:text-slate-400">{{ $rank }}</span>
                                    <span class="font-medium text-heading dark:text-white">{{ $value }}</span>
                                </div>
                            @endif
                        @endforeach
                        @unless ($hasTaxonomy)
                            <p class="text-sm text-body dark:text-slate-400">{{ __('large-animals.disease.no_data') }}</p>
                        @endunless
                    </div>
                </div>

                <a href="{{ route('large-animals.microorganisms.index') }}" wire:navigate
                    class="inline-flex items-center gap-2 text-sm font-medium text-fg-brand hover:underline dark:text-brand">
                    <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                    {{ __('large-animals.microorganisms.back') }}
                </a>
            </div>
        </div>
    </div>
@endsection
