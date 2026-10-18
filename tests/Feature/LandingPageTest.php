<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The redesigned landing page at `/`.
 *
 * Two failure modes this file exists to catch:
 *
 *  1. A translation key that exists in English but not Arabic. That renders a
 *     raw `messages.landing.hero.heading` string on the Arabic homepage and
 *     nothing else in the suite notices. `PartNavigationTest` exists for the
 *     same reason on the part navbars.
 *
 *  2. The redesign leaking outward. `/drugs` must keep the pre-redesign markup,
 *     and the shared `master` layout must stay untouched — both are pinned below
 *     because neither would fail on its own.
 */
class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Flatten a nested translation array to dot-paths.
     *
     * @param  array<mixed>  $messages
     * @return array<int, string>
     */
    private function flatten(array $messages, string $prefix = ''): array
    {
        $paths = [];

        foreach ($messages as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $paths = [...$paths, ...$this->flatten($value, $path)];

                continue;
            }

            $paths[] = $path;
        }

        return $paths;
    }

    /**
     * Render the landing page in a given locale.
     *
     * Uses `app()->setLocale()` rather than requesting `/ar`, following the
     * convention already used across this suite. The /{locale} URL prefix is
     * applied by LaravelLocalization from the request path when routes are
     * registered, and under the test kernel the app boots before any request
     * exists — so `/ar` is simply not a registered route there.
     */
    private function landingIn(string $locale): TestResponse
    {
        app()->setLocale($locale);

        return $this->get('/');
    }

    // ------------------------------------------------------------------ status

    #[Test]
    #[DataProvider('localeProvider')]
    public function it_renders_in_both_locales(string $locale, string $expectedLang): void
    {
        $response = $this->landingIn($locale);

        $response->assertOk();

        $this->assertStringContainsString('<html lang="'.$expectedLang.'"', $response->getContent());
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function localeProvider(): array
    {
        return [
            'english' => ['en', 'en'],
            'arabic' => ['ar', 'ar'],
        ];
    }

    #[Test]
    public function the_arabic_page_declares_rtl(): void
    {
        $content = $this->landingIn('ar')->assertOk()->getContent();

        $this->assertStringContainsString('dir="rtl"', $content);
        $this->assertStringContainsString('lang="ar"', $content);

        // English must NOT be RTL, or the direction logic is inverted.
        $english = $this->landingIn('en')->assertOk()->getContent();

        $this->assertStringNotContainsString('dir="rtl"', $english);
    }

    // ------------------------------------------------------------------ content

    #[Test]
    public function every_section_renders_in_the_reference_order(): void
    {
        // The blog section hides itself when nothing is published, so the order
        // cannot be checked for it on an empty database.
        Blog::factory()->create(['is_active' => true, 'published_at' => now()->subDay()]);

        $content = $this->get('/')->assertOk()->getContent();

        $sections = [
            'id="about"',           // pillars
            'id="browse-hub"',     // chapters
            'id="database-stats"',  // stats
            'id="contact"',         // contact
            'id="blog"',            // blog
        ];

        $cursor = -1;

        foreach ($sections as $id) {
            $at = strpos($content, $id);

            $this->assertNotFalse($at, "Section {$id} is missing from the page.");
            $this->assertGreaterThan($cursor, $at, "Section {$id} is out of order.");
            $cursor = $at;
        }
    }

    #[Test]
    public function the_stats_section_shows_live_counts(): void
    {
        $this->seedKnownRows();

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-entity="products" data-count="7"', $content);
        $this->assertStringContainsString('data-entity="diseases" data-count="3"', $content);
        $this->assertStringContainsString('data-entity="ingredients" data-count="4"', $content);
        $this->assertStringContainsString('data-entity="companies" data-count="2"', $content);
    }

    #[Test]
    public function the_stats_breakdown_accounts_for_all_six_entities(): void
    {
        // The hero's metric strip was removed, so this is the only place all six
        // counted entities are surfaced. The prominent card's total is only
        // auditable if every component of the sum is shown.
        $this->seedKnownRows();

        $content = $this->get('/')->assertOk()->getContent();

        foreach (['products' => 7, 'diseases' => 3, 'ingredients' => 4, 'companies' => 2, 'dosage-forms' => 6, 'species' => 5] as $entity => $expected) {
            $this->assertStringContainsString(
                'data-entity="'.$entity.'" data-count="'.$expected.'"',
                $content,
                "The stats breakdown does not account for [{$entity}]."
            );
        }

        // 7 + 3 + 4 + 2 + 6 + 5
        $this->assertStringContainsString('data-entity="total-records" data-count="27"', $content);
    }

    #[Test]
    public function the_hero_shows_no_metric_strip_or_eyebrow(): void
    {
        // Both were removed from the hero by request. The counts still appear
        // further down the page in the stats section — this only pins them out
        // of the hero, so a stray data-entity cannot creep back in.
        $content = $this->get('/')->assertOk()->getContent();

        // Not anchored on the section's padding utilities — those change whenever the
        // navbar clearance is retuned. Slice from the section opening before the
        // search field through to the end of the hero instead.
        $anchor = strpos($content, 'id="hero-search-input"');
        $start = strrpos(substr($content, 0, (int) $anchor), '<section');
        $end = strpos($content, 'id="about"');

        $this->assertNotFalse($anchor);
        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        $hero = substr($content, $start, $end - $start);

        $this->assertStringNotContainsString('data-entity', $hero);
        $this->assertStringNotContainsString('Veterinary Knowledge Platform', $hero);
        $this->assertStringNotContainsString('منصة المعرفة البيطرية', $hero);

        // The copy that survived must still be there.
        $this->assertStringContainsString('id="hero-search-input"', $hero);
        $this->assertStringContainsString('<h1', $hero);
    }

    #[Test]
    public function the_recent_delta_badge_is_hidden_when_nothing_was_added(): void
    {
        $this->seedKnownRows();

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('data-recent-badge', $content);
    }

    #[Test]
    public function the_recent_delta_badge_appears_when_rows_were_added(): void
    {
        $this->seedKnownRows();

        // Backdate everything, then add one fresh approved product: exactly one
        // delta is non-zero.
        DB::table('products')->update(['created_at' => now()->subYear()]);
        Product::factory()->create(['status' => 'approved']);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-recent-badge="products"', $content);
        $this->assertStringNotContainsString('data-recent-badge="diseases"', $content);
    }

    // --------------------------------------------------------------------- links

    #[Test]
    public function every_navigation_and_chapter_link_points_at_a_real_route(): void
    {
        $routes = collect(app('router')->getRoutes())->map->uri()->all();

        $content = $this->get('/')->assertOk()->getContent();

        preg_match_all('~href="(https?://[^/"]+)?(/[^"#?]*)~', $content, $matches, PREG_SET_ORDER);

        $checked = 0;

        foreach ($matches as $hit) {
            $host = preg_replace('~^https?://~', '', rtrim($hit[1] ?? '', '/'));
            $path = $hit[2] ?? '';

            // Google Fonts and Vite assets are not site routes.
            if (in_array($host, ['fonts.googleapis.com', 'fonts.gstatic.com'], true)) {
                continue;
            }

            if (preg_match('~^/(build|images|storage)/~', $path)) {
                continue;
            }

            // /en and /ar are the language switcher's targets, produced by
            // LaravelLocalization::getLocalizedURL(). Under the test kernel the
            // route table is registered before any request exists, so those
            // prefixed URIs are absent here even though they resolve at runtime.
            // Covered by the_language_switcher_offers_both_locales test instead.
            if (in_array($path, ['/en', '/ar'], true)) {
                continue;
            }

            $checked++;
            $this->assertTrue(
                $this->resolves($routes, $path),
                "The landing page links to [{$path}], which is not a registered route."
            );
        }

        $this->assertGreaterThan(
            10,
            $checked,
            'Expected the page to contain a meaningful number of internal links.'
        );
    }

    /**
     * A path resolves if it matches a route, either exactly or with a trailing
     * slash, ignoring the /{locale} prefix that mcamara applies at runtime.
     *
     * @param  array<int, string>  $routes
     */
    private function resolves(array $routes, string $path): bool
    {
        $candidates = [$path, rtrim($path, '/') ?: '/'];

        foreach ($routes as $uri) {
            $uri = preg_replace('~^\{locale\}~', '', $uri);

            foreach ($candidates as $candidate) {
                if ($candidate === '/'.$uri || rtrim($candidate, '/') === '/'.rtrim($uri, '/')) {
                    return true;
                }
            }
        }

        return false;
    }

    #[Test]
    public function the_hero_search_posts_to_the_real_search_route(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="hero-search-input"', $content);
        $this->assertStringContainsString('name="q"', $content);
        $this->assertMatchesRegularExpression('~<form action="[^"]*/search" method="GET"~', $content);
    }

    // ---------------------------------------------------------------------- blog

    #[Test]
    public function the_blog_section_is_hidden_when_nothing_is_published(): void
    {
        $category = BlogCategory::factory()->create();

        Blog::factory()->count(3)->create(['blog_category_id' => $category->id]);

        // All unpublished -> the section must not render at all.
        Blog::query()->update(['is_active' => false]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('id="blog"', $content);
    }

    #[Test]
    public function the_blog_section_shows_real_metadata_not_placeholders(): void
    {
        $category = BlogCategory::factory()->create(['name' => 'Case Studies', 'name_ar' => 'دراسات حالة']);

        $author = User::factory()->create(['name' => 'Dr Real Author']);

        Blog::factory()->create([
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'A Real Published Article',
            'title_ar' => 'مقال منشور حقيقي',
            'body' => str_repeat('withdrawal period guidance ', 120),
            'body_ar' => null,
            'cover_image' => null,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="blog"', $content);
        $this->assertStringContainsString('A Real Published Article', $content);
        $this->assertStringContainsString('Case Studies', $content);
        $this->assertStringContainsString('Dr Real Author', $content);

        // The reference showed a fabricated "3 Comments" on every card.
        $this->assertStringNotContainsString('3 Comments', $content);
    }

    #[Test]
    public function the_blog_section_renders_arabic_metadata_on_the_arabic_page(): void
    {
        $category = BlogCategory::factory()->create(['name' => 'Case Studies', 'name_ar' => 'دراسات حالة']);

        Blog::factory()->create([
            'blog_category_id' => $category->id,
            'title' => 'English Only Title',
            'title_ar' => 'عنوان بالعربية فقط',
            'body_ar' => str_repeat('مدة السحب ', 150),
            'cover_image' => null,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $content = $this->landingIn('ar')->assertOk()->getContent();

        $this->assertStringContainsString('عنوان بالعربية فقط', $content);
        $this->assertStringContainsString('دراسات حالة', $content);
    }

    #[Test]
    public function a_blog_without_a_cover_image_gets_a_placeholder(): void
    {
        Blog::factory()->create([
            'title' => 'No Cover Here',
            'cover_image' => null,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="blog"', $content);
        $this->assertStringContainsString('data-placeholder-cover', $content);
    }

    // -------------------------------------------------------------- translations

    #[Test]
    public function no_translation_key_is_missing_from_arabic(): void
    {
        $english = $this->flatten((array) trans('messages.landing', [], 'en'));
        $arabic = $this->flatten((array) trans('messages.landing', [], 'ar'));

        $missing = array_diff($english, $arabic);
        $orphan = array_diff($arabic, $english);

        $this->assertSame(
            [],
            array_values($missing),
            'Keys present in English but missing in Arabic: '.implode(', ', $missing)
        );

        $this->assertSame(
            [],
            array_values($orphan),
            'Keys present in Arabic but missing in English: '.implode(', ', $orphan)
        );
    }

    #[Test]
    public function no_translation_key_is_missing_from_arabic_for_the_drugs_hub(): void
    {
        $english = $this->flatten((array) trans('messages.drugs_hub', [], 'en'));
        $arabic = $this->flatten((array) trans('messages.drugs_hub', [], 'ar'));

        $this->assertSame([], array_values(array_diff($english, $arabic)));
        $this->assertSame([], array_values(array_diff($arabic, $english)));
    }

    #[Test]
    public function no_raw_translation_key_reaches_the_page(): void
    {
        foreach (['en', 'ar'] as $locale) {
            $content = $this->landingIn($locale)->assertOk()->getContent();

            preg_match_all('/messages\.[a-zA-Z0-9_.]+/', $content, $matches);

            $this->assertSame(
                [],
                array_values(array_unique($matches[0])),
                "Unresolved translation keys rendered on the {$locale} page."
            );
        }
    }

    #[Test]
    public function no_client_side_translation_attributes_are_emitted(): void
    {
        // The reference swapped translations in JS via data-ar/data-en. This
        // project renders server-side, so those attributes must never appear.
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('data-ar=', $content);
        $this->assertStringNotContainsString('data-en=', $content);
    }

    #[Test]
    public function the_language_switcher_offers_both_locales(): void
    {
        foreach (['en', 'ar'] as $locale) {
            $content = $this->landingIn($locale)->assertOk()->getContent();

            foreach (['en', 'ar'] as $target) {
                $expected = \LaravelLocalization::getLocalizedURL($target, null, [], true);

                $this->assertStringContainsString(
                    'href="'.$expected.'"',
                    $content,
                    "The {$locale} header does not offer a link to the {$target} locale."
                );
            }
        }
    }

    // ------------------------------------------------------------- hero and chrome

    #[Test]
    public function the_hero_photo_has_no_overlay(): void
    {
        // The reference stacked three scrims over this photograph. They were
        // removed deliberately, so a failure here means someone quietly re-added
        // one — not an accidental change.
        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, 'id="hero-search-input"');
        $end = strpos($content, 'id="about"');

        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        $hero = substr($content, 0, $end);

        $this->assertStringNotContainsString('mix-blend-multiply', $hero);
        $this->assertStringNotContainsString('hero-image-scrim', $hero);
        $this->assertStringNotContainsString('from-slate-950 via-slate-950/85', $hero);

        // ...but the photograph itself must still be there.
        $this->assertStringContainsString('images/landing/hero.webp', $hero);
        $this->assertStringContainsString('images/landing/hero.jpg', $hero);
    }

    #[Test]
    public function the_hero_fills_the_viewport(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        // `svh` rather than `vh`: 100vh includes the collapsing mobile URL bar,
        // which would push the bottom of the section below the fold.
        $this->assertStringContainsString('min-h-svh', $content);
        $this->assertStringNotContainsString('min-h-[85vh]', $content);
    }

    #[Test]
    public function the_header_is_a_floating_pill(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, '<header id="site-header"');
        $this->assertNotFalse($start);

        $header = substr($content, $start, 900);

        // Offset from the top edge, centred, and narrower than the sections.
        $this->assertStringContainsString('fixed top-4 inset-x-0', $header);
        $this->assertStringContainsString('max-w-6xl', $header);
        $this->assertStringContainsString('rounded-full', $header);
        $this->assertStringContainsString('h-14', $header);

        // Not a full-width bar: no top-anchored background or bottom rule.
        $this->assertStringNotContainsString('fixed top-0 inset-x-0', $header);
        $this->assertStringNotContainsString('border-b border-slate-200', $header);

        // Clicks must pass through the gaps around the pill.
        $this->assertStringContainsString('pointer-events-none', $header);
        $this->assertStringContainsString('pointer-events-auto', $header);
    }

    #[Test]
    public function the_header_is_translucent_and_darker_in_dark_mode(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, '<header id="site-header"');
        $header = substr($content, $start, 900);

        // Translucent + blurred, so the un-overlaid hero photo reads through it.
        $this->assertStringContainsString('bg-white/80', $header);
        $this->assertStringContainsString('backdrop-blur-md', $header);

        // Dark mode is the darker and more opaque of the two — slate-950, not -900.
        $this->assertStringContainsString('dark:bg-slate-950/85', $header);
        $this->assertStringNotContainsString('dark:bg-slate-900 border', $header);
    }

    #[Test]
    public function the_hero_clears_the_floating_pill(): void
    {
        // The pill occupies roughly top-4 (16px) through top-16 (72px), so the
        // hero needs at least pt-24 of clearance or its H1 collides with it.
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '~<section class="relative pt-(2[89]|[3-9]\d) ~',
            $content,
            'The hero no longer clears the floating navbar.'
        );
    }

    #[Test]
    public function the_main_element_adds_no_top_padding_of_its_own(): void
    {
        // The hero supplies its own clearance for the floating pill. Padding both
        // would double the gap under the navbar.
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('<main class="flex-1">', $content);
    }

    // -------------------------------------------------------------- navbar anchors

    #[Test]
    public function every_navbar_link_is_an_in_page_anchor(): void
    {
        // The navbar is chrome for this page: it scrolls within it rather than
        // navigating away. A route link here would be a regression.
        $content = $this->get('/')->assertOk()->getContent();

        $headerStart = strpos($content, '<header id="site-header"');
        $headerEnd = strpos($content, '<main');
        $header = substr($content, $headerStart, $headerEnd - $headerStart);

        preg_match('~<nav aria-label="[^"]+"(.*?)</nav>~s', $header, $nav);
        $this->assertNotEmpty($nav[1] ?? '', 'The desktop nav could not be isolated.');

        preg_match_all('~href="([^"]+)"~', $nav[1], $links);
        $links = $links[1];

        $this->assertNotEmpty($links);

        foreach ($links as $link) {
            $this->assertStringStartsWith('#', $link, "The navbar link [{$link}] navigates away from the page.");
            $this->assertStringContainsString(
                'id="'.substr($link, 1).'"',
                $content,
                "The navbar links to [{$link}], which no element on the page provides."
            );
        }
    }

    #[Test]
    public function the_navbar_hides_the_blog_link_when_nothing_is_published(): void
    {
        // The blog section renders only when there is something to show, so a
        // permanent link to #blog would be a dead anchor on an empty database.
        $empty = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('id="blog"', $empty);
        $this->assertStringNotContainsString('href="#blog"', $empty);

        Blog::factory()->create(['is_active' => true, 'published_at' => now()->subDay()]);

        $populated = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="blog"', $populated);
        $this->assertStringContainsString('href="#blog"', $populated);
    }

    #[Test]
    public function anchor_targets_land_flush_at_the_top_of_the_page(): void
    {
        // `scroll-smooth` must live on <html>, since scroll-behavior only
        // affects the element that actually scrolls — setting it on a
        // descendant does nothing for the window.
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('~<html[^>]*\bscroll-smooth\b~', $content);

        // The navbar floats rather than occupying a band, so an anchor puts the
        // section flush with the top of the page — no offset.
        foreach (['about', 'browse-hub', 'database-stats', 'contact'] as $id) {
            preg_match('~<section[^>]*id="'.$id.'"[^>]*>~', $content, $tag);

            $this->assertNotEmpty($tag, "No section provides id=\"{$id}\".");
            $this->assertStringContainsString('scroll-mt-0', $tag[0], "#{$id} does not land flush.");
        }
    }

    #[Test]
    public function every_anchor_target_has_enough_padding_to_clear_the_floating_pill(): void
    {
        // Flush landing is only safe because the pill ends 72px down and each
        // section pads its own top by at least that. If someone trims the
        // padding, the heading disappears under the navbar.
        $pillBottom = 72;

        $content = $this->get('/')->assertOk()->getContent();

        foreach (['about', 'browse-hub', 'database-stats', 'contact'] as $id) {
            preg_match('~<section[^>]*id="'.$id.'"[^>]*>~', $content, $tag);

            if (! preg_match('/\bpy-(\d+)/', $tag[0], $py)) {
                continue; // lg:py-N only; the base py-N is absent.
            }

            $this->assertGreaterThanOrEqual(
                $pillBottom,
                ((int) $py[1]) * 4,
                "#{$id} pads its top by less than the 72px the floating pill occupies."
            );
        }
    }

    // ---------------------------------------------------------------------- assets

    #[Test]
    public function the_pillar_cards_match_the_reference_structure(): void
    {
        Blog::factory()->create(['is_active' => true, 'published_at' => now()->subDay()]);

        $content = $this->get('/')->assertOk()->getContent();

        // Isolate the band so nothing else can satisfy an assertion.
        $start = strpos($content, 'id="about"');
        $end = strpos($content, 'id="browse-hub"');

        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        $pillars = substr($content, $start, $end - $start);

        // Four cards, each numbered, tagged and closed by a hairline rule.
        $this->assertSame(4, substr_count($pillars, '<article'));
        $this->assertSame(4, substr_count($pillars, 'vertical-label'));
        $this->assertSame(4, substr_count($pillars, 'w-px h-12'));

        // Card 03 keeps the reference's distinct sky treatment. Losing it would
        // be silent — the card still renders, just without the accent.
        $this->assertStringContainsString('bg-sky-600/75', $pillars);
        $this->assertStringContainsString('bg-sky-950/40', $pillars);

        // Every card's photograph is dimmed, as the reference does.
        $this->assertSame(4, substr_count($pillars, 'brightness-[0.7]'));
    }

    #[Test]
    public function the_pillar_carry_no_fabricated_claims(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, 'id="about"');
        $end = strpos($content, 'id="browse-hub"');
        $pillars = substr($content, $start, $end - $start);

        foreach (['FDA CVM', 'SFDA', 'ISO/HL7', 'EMA', 'board-certified', 'Direct pipeline from official'] as $claim) {
            $this->assertStringNotContainsString($claim, $pillars);
        }
    }

    #[Test]
    public function every_landing_image_declares_its_dimensions(): void
    {
        // Without width/height the browser cannot reserve space before the file
        // arrives, and the whole page jumps as the photographs stream in.
        $content = $this->get('/')->assertOk()->getContent();

        preg_match_all('~<img[^>]*>~', $content, $matches);

        $this->assertNotEmpty($matches[0]);

        foreach ($matches[0] as $tag) {
            $this->assertMatchesRegularExpression('~\bwidth="\d+"~', $tag, "Unsized image: {$tag}");
            $this->assertMatchesRegularExpression('~\bheight="\d+"~', $tag, "Unsized image: {$tag}");
        }
    }

    #[Test]
    public function every_referenced_landing_asset_exists_on_disk(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        preg_match_all('~(?:src|srcset)="(?:https?://[^/"]+)?(/[^"]+\.(?:webp|jpg|jpeg|png))"~', $content, $matches);

        $assets = array_values(array_unique($matches[1]));

        $this->assertNotEmpty($assets);

        foreach ($assets as $asset) {
            $this->assertFileExists(public_path(ltrim($asset, '/')), "Missing asset: {$asset}");
        }
    }

    // ------------------------------------------------------------------ isolation

    #[Test]
    public function the_landing_page_uses_its_own_chrome_not_the_shared_layout(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        // `data-part` themes every page using app.layouts.master.
        $this->assertStringNotContainsString('data-part=', $content);

        // The shared navbar and its mobile drawer must not leak in.
        $this->assertStringNotContainsString('mobileSidebar', $content);

        $this->assertStringContainsString('landing-scope', $content);
        $this->assertStringContainsString('site-header', $content);
        $this->assertStringContainsString('id="footer"', $content);
    }

    #[Test]
    public function the_drugs_hub_keeps_the_pre_redesign_design(): void
    {
        $content = $this->get('/drugs')->assertOk()->getContent();

        // Guard against the redesign leaking into /drugs: it must still use the
        // shared master layout, and must NOT pick up the landing scope.
        $this->assertStringContainsString('data-part=', $content);
        $this->assertStringNotContainsString('landing-scope', $content);
    }

    #[Test]
    public function the_drugs_hub_ships_no_unresolved_keys(): void
    {
        $content = $this->get('/drugs')->assertOk()->getContent();

        preg_match_all('/messages\.[a-zA-Z0-9_.]+/', $content, $matches);

        $this->assertSame(
            [],
            array_values(array_unique($matches[0])),
            'The /drugs hub renders unresolved keys — its translations were refactored out from under it.'
        );
    }

    // ---------------------------------------------------------------- honesty

    #[Test]
    public function no_fabricated_claims_survive_on_the_page(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        // Each of these came from the design reference and is not supported by
        // anything in this codebase.
        $forbidden = [
            'ISO/HL7',
            'FDA CVM',
            'SFDA',
            '9.4',                       // fabricated peer-review score
            '24 clinical hours',        // fabricated SLA
            'clinical@vetpedia.org',
            'partners@vetpedia.org',
            'Boston, USA',
            'London, UK',
            'Riyadh, KSA',
            'board-certified',
        ];

        foreach ($forbidden as $claim) {
            $this->assertStringNotContainsString(
                $claim,
                $content,
                "[{$claim}] is a fabricated claim from the design reference and must not ship."
            );
        }
    }

    // ------------------------------------------------------------------ contact

    #[Test]
    public function the_contact_form_is_mounted_and_wired_to_the_store(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('wire:submit', $content);
        $this->assertStringContainsString('contact.contact-form', $content);
        $this->assertStringContainsString('id="contact-form"', $content);
    }

    // -------------------------------------------------------------------- setup

    /**
     * Insert a known number of rows so the assertions below can state exact
     * counts instead of "greater than zero".
     *
     * Deliberately uses direct inserts rather than factories: the Faker pool is
     * shared across the whole suite, so a factory draw here would shift which
     * names other tests receive.
     */
    private function seedKnownRows(): void
    {
        $past = now()->subYear();

        DB::table('products')->insert($this->rows(
            ['trade_name', 'trade_name_ar', 'slug'],
            7,
            $past,
            // Product carries a global scope for approved + active, which is
            // exactly what the public site shows, so the seeds must satisfy it.
            ['status' => 'approved']
        ));

        DB::table('diseases')->insert($this->rows(
            ['name', 'name_ar', 'slug'],
            3,
            $past
        ));

        DB::table('active_ingredients')->insert($this->rows(
            ['name', 'name_ar', 'slug'],
            4,
            $past
        ));

        DB::table('companies')->insert($this->rows(
            ['name', 'slug'],
            2,
            $past,
            ['company_type' => 'manufacturer']
        ));

        DB::table('species')->insert($this->rows(
            ['name', 'name_ar', 'slug'],
            5,
            $past
        ));

        DB::table('dosage_forms')->insert($this->rows(
            ['name', 'name_ar', 'slug'],
            6,
            $past
        ));
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<string, mixed>  $constants
     * @return array<int, array<string, mixed>>
     */
    private function rows(array $columns, int $count, \DateTimeInterface $at, array $constants = []): array
    {
        $rows = [];

        for ($i = 0; $i < $count; $i++) {
            $row = [
                'id' => (string) Str::ulid(),
                'created_at' => $at,
                'updated_at' => $at,
                'deleted_at' => null,
                'is_active' => true,
                ...$constants,
            ];

            foreach ($columns as $column) {
                // Slugs must be unique per row, so they get a suffix rather than
                // sharing the bare label value.
                $row[$column] = $column === 'slug' ? 'seed-'.$i.'-slug' : 'seed-'.$i;
            }

            $rows[] = $row;
        }

        return $rows;
    }
}
