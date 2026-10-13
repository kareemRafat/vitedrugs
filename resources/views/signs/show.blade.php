@extends('app.layouts.master')

@php
    // Shared by the large-animals and poultry controllers. $part doubles as both
    // the route-name prefix and the translation namespace because the two
    // vocabularies are spelled the same ('large-animals' / 'poultry').
    $t = fn (string $key, array $replace = []): string => __($part.'.'.$key, $replace);
@endphp

@section('title', $t('sign.page_title', ['name' => $sign->localized_display_name]))

@section('content')
    <div class="space-y-4">

        <x-large-animals.page-hero
            :heading="$sign->localized_display_name"
            :subtitle="$t('sign.subtitle')"
            :badge="$t('sign.badge')"
            badgeIcon="stethoscope"
            :stats="[
                ['count' => $diseases->count(), 'label' => $t('sign.disease_count'), 'icon' => 'activity'],
            ]"
        />

        {{-- What this sign is --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <h2 class="text-base font-semibold text-heading dark:text-white mb-4">{{ $t('sign.details') }}</h2>

            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ([
                    ['label' => $t('sign.canonical_name'), 'value' => $sign->canonical_name],
                    ['label' => $t('sign.anatomical_structure'), 'value' => $sign->anatomicalStructure?->localized_display_name],
                    ['label' => $t('sign.body_system'), 'value' => $sign->anatomicalStructure?->bodySystem?->localized_display_name],
                    ['label' => $t('sign.stage'), 'value' => $sign->stage],
                    ['label' => $t('sign.modifier'), 'value' => $sign->modifier?->display_name],
                    ['label' => $t('sign.finding'), 'value' => $sign->finding?->display_name],
                ] as $row)
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-body dark:text-slate-400 mb-1">{{ $row['label'] }}</dt>
                        <dd class="text-sm font-medium text-heading dark:text-white">
                            {{ $row['value'] ?: $t('sign.na') }}
                        </dd>
                    </div>
                @endforeach
            </dl>

            {{-- One concept, sometimes more than one record behind it. Only
                 mentioned when the records actually differ; when they agree the
                 reader is shown one block and told nothing. --}}
            @if ($variants !== [])
                <div class="mt-5 pt-4 border-t border-default-medium">
                    <p class="text-xs uppercase tracking-wide text-body dark:text-slate-400 mb-2">
                        {{ $t('sign.also_recorded_as') }}
                    </p>

                    <ul class="space-y-1.5">
                        @foreach ($variants as $variant)
                            <li class="text-sm text-heading dark:text-white">
                                {{ collect([
                                    $variant['anatomical_structure'],
                                    $variant['body_system'],
                                    $variant['stage'],
                                    $variant['modifier'],
                                    $variant['finding'],
                                ])->filter()->implode(' · ') ?: $t('sign.na') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Diseases that carry this sign --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs overflow-hidden dark:bg-slate-800">
            <div class="px-5 py-4 border-b border-default-medium flex items-center gap-2">
                <x-lucide-activity class="w-4 h-4 text-body dark:text-slate-400" />
                <h2 class="text-base font-semibold text-heading dark:text-white">{{ $t('sign.diseases_heading') }}</h2>
                <span class="ms-auto inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 text-sm font-semibold bg-brand-soft text-fg-brand rounded-base dark:bg-brand/20 dark:text-brand">
                    {{ $diseases->count() }}
                </span>
            </div>

            <div class="p-5">
                @forelse ($diseases as $disease)
                    <div class="flex items-center justify-between gap-3 py-3 border-b border-default-medium last:border-0">
                        <span class="min-w-0">
                            <a href="{{ route($part.'.diseases.show', $disease->slug) }}" wire:navigate
                                class="block text-base font-medium text-fg-brand hover:underline truncate">{{ $disease->name }}</a>

                            @if ($disease->name_ar && $disease->name_ar !== $disease->name)
                                <span class="block text-sm text-body dark:text-slate-400 truncate">{{ $disease->name_ar }}</span>
                            @endif

                            {{-- The pivot flags are the reason this page exists: they say
                                 how strongly this disease leans on this sign. --}}
                            <span class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-base bg-neutral-secondary-soft text-body dark:bg-slate-700 dark:text-slate-300">
                                    {{ $t('sign.weight') }}: {{ (int) ($disease->pivot->weight ?? 0) }}
                                </span>

                                @if ($disease->pivot->is_pathognomonic)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-base bg-danger-soft text-fg-danger-strong dark:bg-danger/20 dark:text-red-400">
                                        {{ $t('sign.pathognomonic') }}
                                    </span>
                                @endif

                                @if ($disease->pivot->is_required)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-base bg-warning-soft text-fg-warning-strong dark:bg-warning/20 dark:text-amber-400">
                                        {{ $t('sign.required') }}
                                    </span>
                                @endif

                                @if ($disease->pivot->is_specific)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-base bg-success-soft text-fg-success-strong dark:bg-success/20 dark:text-emerald-400">
                                        {{ $t('sign.specific') }}
                                    </span>
                                @endif
                            </span>
                        </span>

                        <a href="{{ route($part.'.diseases.show', $disease->slug) }}" wire:navigate
                            class="inline-flex items-center justify-center w-8 h-8 text-white bg-brand hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium rounded-base text-sm shrink-0">
                            <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
                        </a>
                    </div>
                @empty
                    <p class="text-base text-body dark:text-slate-400 text-center py-4">{{ $t('sign.no_diseases') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Onward. The page answers "what is this sign"; the two tools answer
             "what should I do with it". --}}
        <div class="bg-neutral-primary-soft rounded-base shadow-xs p-5 dark:bg-slate-800">
            <h2 class="text-base font-semibold text-heading dark:text-white mb-4">{{ $t('sign.next_heading') }}</h2>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route($part.'.filter', ['sign' => $sign->id]) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-fg-brand border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:text-white dark:hover:bg-slate-700">
                    <x-lucide-sliders-horizontal class="w-4 h-4" />
                    {{ $t('sign.run_filter') }}
                </a>

                <a href="{{ route($part.'.diagnosis', ['sign' => $sign->id]) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-fg-brand border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:text-white dark:hover:bg-slate-700">
                    <x-lucide-stethoscope class="w-4 h-4" />
                    {{ $t('sign.run_diagnosis') }}
                </a>

                <a href="{{ route($part.'.search', ['q' => $sign->display_name]) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-fg-brand border border-default-medium rounded-base hover:bg-neutral-secondary-soft dark:text-white dark:hover:bg-slate-700">
                    <x-lucide-search class="w-4 h-4" />
                    {{ $t('sign.more_results') }}
                </a>
            </div>
        </div>

    </div>
@endsection