@extends('app.layouts.master')

@section('title', __('messages.drugs_hub.title'))

@section('meta_description', __('messages.drugs_hub.meta_description'))

@section('meta_keywords', 'veterinary drugs, veterinary pharmaceuticals, animal health, veterinary medicine')

{{--
    Part hub.

    Same skeleton as /large-animals and /poultry — gradient hero, stats band,
    tool cards, CTA bar — built from the shared x-part.* components so the three
    parts look like one site.

    Three sections came out in the rebuild:

      * The four "features" cards and the four "browse by category" cards were
        the same four routes with the same four descriptions, one screen apart.
        They are now one grid.
      * "How it works" and "Why VetPedia?" restated what the library grid and
        the stats band already said, and their cards all pointed at
        products.index because there was nowhere else for them to go.
      * The animated orbs, grid textures and shimmer overlays are gone. They
        were decoration standing in for hierarchy the sections now provide.
--}}

@section('content')
    @php
        $libraries = [
            [
                'route' => 'products.index',
                'icon' => 'package',
                'title' => __('messages.drugs_hub.feature_products_title'),
                'description' => __('messages.drugs_hub.feature_products_desc'),
            ],
            [
                'route' => 'diseases.index',
                'icon' => 'activity',
                'title' => __('messages.drugs_hub.feature_diseases_title'),
                'description' => __('messages.drugs_hub.feature_diseases_desc'),
            ],
            [
                'route' => 'active-ingredients.index',
                'icon' => 'flask-conical',
                'title' => __('messages.drugs_hub.feature_ingredients_title'),
                'description' => __('messages.drugs_hub.feature_ingredients_desc'),
            ],
            [
                'route' => 'companies.index',
                'icon' => 'building-2',
                'title' => __('messages.drugs_hub.feature_companies_title'),
                'description' => __('messages.drugs_hub.feature_companies_desc'),
            ],
        ];
    @endphp

    <div class="space-y-6">

        {{-- Hero --}}
        <x-part.part-hero
            :badge="__('messages.drugs_hub.badge')"
            badge-icon="pill"
            :heading="__('messages.drugs_hub.heading')"
            :subtitle="__('messages.drugs_hub.subtitle')"
        >
            <form action="{{ route('search') }}" method="GET" class="w-full sm:w-auto">
                <label for="drugs-search" class="sr-only">{{ __('messages.drugs_hub.search_placeholder') }}</label>
                <div class="relative flex items-center bg-white dark:bg-slate-800 rounded-base border border-default-medium dark:border-slate-600 focus-within:border-brand dark:focus-within:border-brand transition-all duration-300 shadow-sm">
                    <div class="ps-4">
                        <x-lucide-search class="w-4 h-4 text-body dark:text-slate-400" />
                    </div>
                    <input id="drugs-search" type="search" name="q" autocomplete="off"
                        class="w-full bg-transparent text-heading dark:text-white text-sm px-3 py-3 focus:outline-none placeholder:text-body dark:placeholder:text-slate-400 border-0 ring-0"
                        placeholder="{{ __('messages.drugs_hub.search_placeholder') }}">
                    <div class="pe-1">
                        <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-brand hover:bg-brand-strong rounded-full transition-all duration-200 shadow-lg shadow-brand/25 active:scale-95">
                            {{ __('messages.drugs_hub.search_button') }}
                        </button>
                    </div>
                </div>
            </form>

            <x-part.hero-action route="products.index" icon="package">
                {{ __('messages.drugs_hub.cta_browse') }}
            </x-part.hero-action>
        </x-part.part-hero>

        {{-- Stats --}}
        <section aria-label="{{ __('messages.drugs_hub.stats_heading') }}">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <x-part.stat-tile icon="package" :value="$productsCount" :label="__('messages.drugs_hub.products_badge')" />
                <x-part.stat-tile icon="activity" :value="$diseasesCount" :label="__('messages.drugs_hub.diseases_badge')" />
                <x-part.stat-tile icon="flask-conical" :value="$ingredientsCount" :label="__('messages.drugs_hub.ingredients_badge')" />
                <x-part.stat-tile icon="building-2" :value="$companiesCount" :label="__('messages.drugs_hub.companies_badge')" />
            </div>
        </section>

        {{-- Libraries --}}
        <section class="pt-2">
            <x-part.section-title
                :heading="__('messages.drugs_hub.features_heading')"
                :subtitle="__('messages.drugs_hub.features_subtitle')"
            />

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($libraries as $library)
                    <x-part.tool-card
                        :route="$library['route']"
                        :icon="$library['icon']"
                        :title="$library['title']"
                        :description="$library['description']"
                        :cta="__('messages.drugs_hub.cta_browse')"
                    />
                @endforeach
            </div>
        </section>

        {{-- Blog --}}
        @if ($latestBlogs->isNotEmpty())
            <section class="pt-2">
                <x-part.section-title
                    :heading="__('messages.drugs_hub.latest_blog')"
                    :subtitle="__('messages.drugs_hub.blog_subtitle')"
                />

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($latestBlogs as $blog)
                        <x-part.post-card :post="$blog" />
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('blog.index') }}"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 text-sm font-semibold text-fg-brand dark:text-brand border border-brand/40 rounded-full hover:bg-brand-soft transition-all duration-200">
                        {{ __('messages.drugs_hub.view_all') }}
                        <x-lucide-arrow-right class="w-3.5 h-3.5 rtl:rotate-180" />
                    </a>
                </div>
            </section>
        @endif

        {{-- CTA --}}
        <section class="pb-2">
            <x-part.cta-bar
                route="products.index"
                :heading="__('messages.drugs_hub.cta_heading')"
                :subtitle="__('messages.drugs_hub.cta_subtitle')"
                :button="__('messages.drugs_hub.cta_browse')"
            />
        </section>

    </div>
@endsection