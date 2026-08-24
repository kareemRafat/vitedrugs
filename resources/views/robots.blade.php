@if(config('app.noindex'))
User-agent: *
Disallow: /
@else
User-agent: *

Allow: /

Sitemap: {{ url('/sitemap.xml') }}
@endif
