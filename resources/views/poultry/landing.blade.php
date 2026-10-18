@extends('app.layouts.master')

@section('title', __('poultry.landing.heading'))

@section('meta_description', __('poultry.landing.subtitle'))

{{--
    Part hub.

    Same skeleton as /drugs and /large-animals — gradient hero, stats band, tool
    cards, CTA bar — built from the same x-part.* components.

    Every route below stays inside the poultry part. The tools used to be shared
    with /large-animals and link out of this page; they are poultry-only now, and
    a "shared with Large Animals" notice used to stand here explaining that. It
    is gone because there is nothing left to explain.
--}}

@section('content')
    @php
        $statIcons = [
            'diseases' => 'bug',
            'findings' => 'activity',
            'lesions' => 'scan-search',
            'species' => 'egg',
        ];

        $serviceIcons = [
            'diagnosis' => 'stethoscope',
            'diseases' => 'bug',
            'filter' => 'sliders-horizontal',
            'search' => 'search',
            'compare' => 'columns-3',
            'products' => 'pill',
            'body_map' => 'scan-search',
            'atlas' => 'book-open-text',
        ];

        // Only sections that exist today. A card appears here the phase that
        // builds it lands, so the grid never links to a 404.
        $serviceRoutes = [
            'diagnosis' => 'poultry.diagnosis',
            'diseases' => 'poultry.diseases.index',
            'filter' => 'poultry.filter',
            'search' => 'poultry.search',
            'compare' => 'poultry.comparison',
            'products' => 'poultry.products.index',
            'body_map' => 'poultry.body-map',
            'atlas' => 'poultry.atlas',
        ];
    @endphp

    <div class="space-y-6">

        {{-- Hero --}}
        <x-part.part-hero
            :badge="__('poultry.landing.badge')"
            badge-icon="egg"
            :heading="__('poultry.landing.heading')"
            :subtitle="__('poultry.landing.subtitle')"
        >
            <x-part.hero-action route="poultry.diagnosis" icon="stethoscope">
                {{ __('poultry.landing.cta_primary') }}
            </x-part.hero-action>

            <x-part.hero-action route="poultry.filter" variant="secondary">
                {{ __('poultry.landing.cta_secondary') }}
                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
            </x-part.hero-action>
        </x-part.part-hero>

        {{-- Stats --}}
        <section aria-label="{{ __('poultry.landing.stats_heading') }}">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ($stats as $key => $value)
                    <x-part.stat-tile
                        :icon="$statIcons[$key]"
                        :value="$value"
                        :label="__('poultry.landing.stats.'.$key)"
                    />
                @endforeach
            </div>
        </section>

        {{-- Tools --}}
        <section class="pt-2">
            <x-part.section-title
                :heading="__('poultry.landing.services_heading')"
                :subtitle="__('poultry.landing.services_subtitle')"
            />

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($serviceRoutes as $key => $route)
                    <x-part.tool-card
                        :route="$route"
                        :icon="$serviceIcons[$key]"
                        :title="__('poultry.landing.services.'.$key.'.title')"
                        :description="__('poultry.landing.services.'.$key.'.description')"
                        :cta="__('poultry.landing.open')"
                    />
                @endforeach
            </div>
        </section>

        {{-- CTA --}}
        <section class="pb-2">
            <x-part.cta-bar
                route="poultry.diagnosis"
                :heading="__('poultry.landing.cta_title')"
                :subtitle="__('poultry.landing.cta_subtitle')"
                :button="__('poultry.landing.cta_button')"
            />
        </section>

    </div>
@endsection