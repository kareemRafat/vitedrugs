@extends('app.layouts.master')

@section('title', __('large-animals.landing.heading'))

@section('meta_description', __('large-animals.landing.subtitle'))

{{--
    Part hub.

    Same skeleton as /drugs and /poultry — gradient hero, stats band, tool cards,
    plain lists, CTA bar — built from the same x-part.* components, so the three
    parts look like one site rather than three hand-written pages.

    The article band below is the addition: it shows real published rows from
    the article table as teaser cards, the same shape as the /drugs blog band.
    It hides itself entirely when nothing is published, so a fresh install
    renders a shorter page rather than an empty shell.
--}}

@section('content')
    @php
        $statIcons = [
            'diseases' => 'bug',
            'clinical_signs' => 'activity',
            'microorganisms' => 'microscope',
            'articles' => 'file-text',
            'projects' => 'folder-kanban',
            'species' => 'paw-print',
        ];

        $serviceIcons = [
            'diagnosis' => 'stethoscope',
            'diseases' => 'bug',
            'filter' => 'sliders-horizontal',
            'comparison' => 'columns-3',
            'microorganisms' => 'microscope',
            'articles' => 'file-text',
            'specializations' => 'layers',
            'projects' => 'folder-kanban',
            'products' => 'pill',
            'search' => 'search',
        ];

        // Route name per tool, in the order the grid shows them. Declared here
        // as names rather than URLs so a renamed route fails in one place.
        $serviceRoutes = [
            'diagnosis' => 'large-animals.diagnosis',
            'diseases' => 'large-animals.diseases.index',
            'filter' => 'large-animals.filter',
            'comparison' => 'large-animals.comparison',
            'microorganisms' => 'large-animals.microorganisms.index',
            'articles' => 'large-animals.medical-articles.index',
            'specializations' => 'large-animals.specializations.index',
            'projects' => 'large-animals.projects.index',
            'products' => 'large-animals.products.index',
            'search' => 'large-animals.search',
        ];

        /*
         * `latestArticles` is rendered by x-part.article-card, which owns the
         * row's shape. Nothing is pre-built here: the card wraps
         * x-part.content-card, the same component the /drugs blog band uses, so
         * the two cannot drift apart.
         */
    @endphp

    <div class="space-y-6">

        {{-- Hero --}}
        <x-part.part-hero
            :badge="__('large-animals.landing.badge')"
            badge-icon="paw-print"
            :heading="__('large-animals.landing.heading')"
            :subtitle="__('large-animals.landing.subtitle')"
        >
            <x-part.hero-action route="large-animals.diagnosis" icon="stethoscope">
                {{ __('large-animals.landing.cta_primary') }}
            </x-part.hero-action>

            <x-part.hero-action route="large-animals.specializations.index" variant="secondary">
                {{ __('large-animals.landing.cta_secondary') }}
                <x-lucide-arrow-right class="w-4 h-4 rtl:rotate-180" />
            </x-part.hero-action>
        </x-part.part-hero>

        {{-- Stats --}}
        <section aria-label="{{ __('large-animals.landing.stats_heading') }}">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                @foreach ($stats as $key => $value)
                    <x-part.stat-tile
                        :icon="$statIcons[$key]"
                        :value="$value"
                        :label="__('large-animals.landing.stats.'.$key)"
                    />
                @endforeach
            </div>
        </section>

        {{-- Tools --}}
        <section class="pt-2">
            <x-part.section-title
                :heading="__('large-animals.landing.services_heading')"
                :subtitle="__('large-animals.landing.services_subtitle')"
            />

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($serviceRoutes as $key => $route)
                    <x-part.tool-card
                        :route="$route"
                        :icon="$serviceIcons[$key]"
                        :title="__('large-animals.landing.services.'.$key.'.title')"
                        :description="__('large-animals.landing.services.'.$key.'.description')"
                        :cta="__('large-animals.landing.open')"
                    />
                @endforeach
            </div>
        </section>

        {{-- Latest articles --}}
        @if ($latestArticles->isNotEmpty())
            <section class="pt-2">
                <x-part.section-title
                    :heading="__('large-animals.landing.articles_heading')"
                    :subtitle="__('large-animals.landing.articles_subtitle')"
                />

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($latestArticles as $article)
                        <x-part.article-card :article="$article" />
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('large-animals.medical-articles.index') }}"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-fg-brand dark:text-brand border border-brand/40 rounded-full hover:bg-brand-soft transition-all duration-200">
                        {{ __('large-animals.landing.articles_view_all') }}
                        <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                    </a>
                </div>
            </section>
        @endif

        {{-- CTA --}}
        <section class="pb-2">
            <x-part.cta-bar
                route="large-animals.specializations.index"
                :heading="__('large-animals.landing.cta_title')"
                :subtitle="__('large-animals.landing.cta_subtitle')"
                :button="__('large-animals.landing.cta_button')"
            />
        </section>

    </div>
@endsection