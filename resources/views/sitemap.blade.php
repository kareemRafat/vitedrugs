<?xml version="1.0" encoding="UTF-8"?>

<urlset
xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>

        <loc>{{ url('/') }}</loc>

    </url>

    @foreach($products as $product)

        <url>

            <loc>
                {{ route('products.show', $product) }}
            </loc>

        </url>

    @endforeach

    @foreach($diseases as $disease)

        <url>

            <loc>
                {{ route('diseases.show', $disease) }}
            </loc>

        </url>

    @endforeach

    @foreach($companies as $company)

        <url>

            <loc>
                {{ route('companies.show', $company) }}
            </loc>

        </url>

    @endforeach

    @foreach($ingredients as $ingredient)

        <url>

            <loc>
                {{ route('active-ingredients.show', $ingredient) }}
            </loc>

        </url>

    @endforeach

</urlset>