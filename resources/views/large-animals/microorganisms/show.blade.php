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
        $typeIcon = match ($microorganism->microorganism_type) {
            'bacteria' => 'bug',
            'virus' => 'dna',
            'fungus' => 'sprout',
            'parasite' => 'worm',
            default => 'microscope',
        };
    @endphp

    <div class="space-y-4">

        {{-- Breadcrumb --}}
        <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-body dark:text-slate-400 pt-4" aria-label="{{ __('messages.nav.breadcrumb') }}">
            <a href="{{ route('large-animals.home') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.drugs') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <a href="{{ route('large-animals.microorganisms.index') }}" wire:navigate class="hover:text-fg-brand dark:hover:text-brand transition-colors">{{ __('large-animals.breadcrumb.microorganisms') }}</a>
            <x-lucide-chevron-right class="w-4 h-4 rtl:rotate-180 shrink-0" />
            <span class="text-heading dark:text-white font-medium">{{ $microorganism->name }}</span>
        </nav>

        {{-- Hero --}}
        <x-large-animals.page-hero
            :heading="$microorganism->name"
            :subtitle="$microorganism->normalized_name && $microorganism->normalized_name !== $microorganism->name ? $microorganism->normalized_name : ''"
            :badge="$microorganism->microorganism_type ? __('large-animals.microorganisms.types.' . $microorganism->microorganism_type) : __('large-animals.hero.badge.microorganisms')"
            :badgeIcon="$typeIcon"
            :stats="[
                ['count' => $microorganism->diseases->count(), 'label' => __('large-animals.hero.stats.linked_diseases'), 'icon' => 'activity'],
            ]"
        >
            @if ($microorganism->is_pathogenic)
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-base bg-red-500/25 border border-red-300/50 text-white">
                    <x-lucide-alert-triangle class="w-3.5 h-3.5 me-1" />
                    {{ __('large-animals.microorganisms.pathogenic') }}
                </span>
            @endif
            @if (filled($microorganism->tags))
                <div class="flex flex-wrap gap-1.5">
                    @foreach ((array) $microorganism->tags as $tag)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-base bg-white/15 border border-white/30 text-white/90">
                            <x-lucide-tag class="w-3 h-3" />
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif
            <a href="{{ route('large-animals.microorganisms.index') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white/15 text-white border border-white/30 rounded-base hover:bg-white/25 transition-colors">
                <x-lucide-arrow-left class="w-4 h-4 rtl:rotate-180" />
                {{ __('large-animals.microorganisms.back') }}
            </a>
        </x-large-animals.page-hero>

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
