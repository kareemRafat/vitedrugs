<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    @php
        $locales = ['en', 'ar'];
        $indexRoutes = ['home', 'products.index', 'companies.index', 'diseases.index', 'active-ingredients.index'];
        $staticRoutes = ['about', 'contact', 'privacy-policy', 'terms-of-service'];
        $defaultLocale = config('app.fallback_locale', 'en');
    @endphp

    {{-- Index pages --}}
    @foreach($indexRoutes as $route)
        @php $enUrl = LaravelLocalization::getLocalizedURL('en', route($route, [], false)); @endphp
        <url>
            <loc>{{ $enUrl }}</loc>
            <changefreq>weekly</changefreq>
            <priority>{{ $route === 'home' ? '1.0' : '0.8' }}</priority>
            @foreach($locales as $locale)
                <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, route($route, [], false)) }}" />
            @endforeach
        </url>
    @endforeach

    {{-- Static pages --}}
    @foreach($staticRoutes as $route)
        @php $enUrl = LaravelLocalization::getLocalizedURL('en', route($route, [], false)); @endphp
        <url>
            <loc>{{ $enUrl }}</loc>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
            @foreach($locales as $locale)
                <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, route($route, [], false)) }}" />
            @endforeach
        </url>
    @endforeach

    {{-- Products --}}
    @foreach($products as $product)
        @php $enUrl = LaravelLocalization::getLocalizedURL('en', route('products.show', $product, false)); @endphp
        <url>
            <loc>{{ $enUrl }}</loc>
            <lastmod>{{ $product->updated_at->toW3cString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
            @foreach($locales as $locale)
                <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, route('products.show', $product, false)) }}" />
            @endforeach
        </url>
    @endforeach

    {{-- Diseases --}}
    @foreach($diseases as $disease)
        @php $enUrl = LaravelLocalization::getLocalizedURL('en', route('diseases.show', $disease, false)); @endphp
        <url>
            <loc>{{ $enUrl }}</loc>
            <lastmod>{{ $disease->updated_at->toW3cString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
            @foreach($locales as $locale)
                <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, route('diseases.show', $disease, false)) }}" />
            @endforeach
        </url>
    @endforeach

    {{-- Companies --}}
    @foreach($companies as $company)
        @php $enUrl = LaravelLocalization::getLocalizedURL('en', route('companies.show', $company, false)); @endphp
        <url>
            <loc>{{ $enUrl }}</loc>
            <lastmod>{{ $company->updated_at->toW3cString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
            @foreach($locales as $locale)
                <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, route('companies.show', $company, false)) }}" />
            @endforeach
        </url>
    @endforeach

    {{-- Active Ingredients --}}
    @foreach($ingredients as $ingredient)
        @php $enUrl = LaravelLocalization::getLocalizedURL('en', route('active-ingredients.show', $ingredient, false)); @endphp
        <url>
            <loc>{{ $enUrl }}</loc>
            <lastmod>{{ $ingredient->updated_at->toW3cString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.5</priority>
            @foreach($locales as $locale)
                <xhtml:link rel="alternate" hreflang="{{ $locale }}" href="{{ LaravelLocalization::getLocalizedURL($locale, route('active-ingredients.show', $ingredient, false)) }}" />
            @endforeach
        </url>
    @endforeach

</urlset>
