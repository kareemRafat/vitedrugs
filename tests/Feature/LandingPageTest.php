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

    /**
     * One eyebrow treatment across the page: a mono `/ NN` index beside the
     * section label. The hero has none (pinned separately) and the closing CTA
     * band has none by choice, so the index runs 01-05 over the headed sections.
     *
     * Matched on the marker's own utility so the chapters' per-card indices -
     * `/ 01`-`/ 03` on `tracking-widest` spans - cannot satisfy the assertion.
     */
    #[Test]
    public function every_section_eyebrow_uses_the_same_indexed_marker(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        preg_match_all('~<span class="[^"]*tracking-wider">(/\s*0\d)</span>~', $content, $matches);

        $markers = array_map('trim', $matches[1]);

        $this->assertSame(['/ 01', '/ 02', '/ 03', '/ 04'], array_slice($markers, 0, 4));

        foreach ($markers as $index => $marker) {
            $this->assertSame(
                '/ 0'.($index + 1),
                $marker,
                "Section eyebrow #{$index} is out of sequence."
            );
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

    /**
     * The band was retoned from amber to the page's indigo primary. Pinned
     * because the swap is silent: an `amber-*` token left behind reads as a
     * deliberate accent rather than as drift.
     */
    #[Test]
    public function the_blog_band_uses_the_page_accent_rather_than_amber(): void
    {
        Blog::factory()->create([
            'blog_category_id' => BlogCategory::factory()->create()->id,
            'author_id' => User::factory()->create()->id,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, 'id="blog"');
        // The next `<section` is the CTA band. Bounded by markup rather than by an
        // id, because the CTA band has none — and slicing to the end of the
        // document would swallow the cookie banner, which is legitimately orange.
        $end = strpos($content, '<section', $start);

        $this->assertNotFalse($start, 'The blog band did not render.');
        $this->assertNotFalse($end);

        $band = substr($content, $start, $end - $start);

        $this->assertStringNotContainsString('amber-', $band);
        $this->assertStringNotContainsString('orange-', $band);
        // Step-agnostic on purpose: the accent has been retoned more than once
        // (amber to indigo, then the button fills darkened). What matters is that
        // the band carries the page's blue at all.
        $this->assertStringContainsString('bg-indigo-', $band);
        $this->assertStringContainsString('text-indigo-600 dark:text-indigo-400', $band);
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

    /**
     * The footer's language block renders each supported locale as a bordered
     * card, with the current one marked programmatically. Two things are pinned
     * because both failed silently before: the card must carry `aria-current`,
     * and the code beside each locale name must be the locale key rather than a
     * slice of the native name - `mb_substr('العربية', 0, 2)` rendered as "ال".
     */
    #[Test]
    public function the_footer_language_cards_mark_the_active_locale_and_show_real_codes(): void
    {
        foreach (['en', 'ar'] as $locale) {
            $footer = $this->landingIn($locale)->assertOk()->getContent();

            $start = strpos($footer, 'id="footer"');
            $this->assertNotFalse($start, 'The landing footer is missing.');
            $footer = substr($footer, $start);

            foreach (['en', 'ar'] as $target) {
                $this->assertStringContainsString(
                    'href="'.$this->localizedUrl($target).'"',
                    $footer,
                    "The {$locale} footer does not offer a link to the {$target} locale."
                );
            }

            // Exactly one card is current: the visitor's own locale.
            $this->assertSame(1, substr_count($footer, 'aria-current="true"'));
            $this->assertSame(1, substr_count($footer, 'aria-current="false"'));

            $this->assertStringContainsString('>EN</span>', $footer);
            $this->assertStringContainsString('>AR</span>', $footer);

            // The hack that produced "ال" instead of a language code.
            $this->assertStringNotContainsString('mb_substr', $footer);

            // No flag glyphs: regional-indicator emoji fall back to the letter
            // pair on Windows, which rendered as "GB" and "AE" beside the names.
            $this->assertStringNotContainsString("\u{1F1EC}\u{1F1E7}", $footer);
            $this->assertStringNotContainsString("\u{1F1E6}\u{1F1EA}", $footer);
        }
    }

    private function localizedUrl(string $locale): string
    {
        return \LaravelLocalization::getLocalizedURL($locale, null, [], true);
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
        // Slate-100 rather than white: the pill was darkened so it does not read
        // as a hole punched in the hero photograph.
        $this->assertStringContainsString('bg-slate-100/85', $header);
        $this->assertStringContainsString('backdrop-blur-md', $header);

        // Dark mode is the darker and more opaque of the two — slate-950, not -900.
        $this->assertStringContainsString('dark:bg-slate-950/90', $header);
        $this->assertStringNotContainsString('dark:bg-slate-900 border', $header);
    }

    /**
     * The nav links are semibold in both the desktop bar and the mobile drawer —
     * the drawer comment already claims it mirrors the desktop nav exactly.
     */
    #[Test]
    public function the_navigation_links_are_semibold_in_bar_and_drawer(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, '<header id="site-header"');
        $header = substr($content, $start);

        $this->assertMatchesRegularExpression(
            '~<nav[^>]*class="[^"]*font-semibold[^"]*"~',
            $header,
            'The desktop nav is not semibold.'
        );

        $this->assertMatchesRegularExpression(
            '~<nav class="space-y-1">.*?font-semibold~s',
            $header,
            'The drawer nav is not semibold.'
        );
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

        preg_match('~<main\b[^>]*>~', $content, $tag);

        $this->assertNotEmpty($tag, 'The landing page has no <main>.');

        // Asserted by class rather than by whole tag: <main> also carries the
        // tabindex the scroll-to-top control focuses, which is not what this
        // test is about.
        $this->assertStringContainsString('flex-1', $tag[0]);
        $this->assertDoesNotMatchRegularExpression('~\b[pt]?y-\d~', $tag[0]);
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

    /**
     * Arabic type is applied through a stylesheet rule, so nothing in the rendered
     * HTML can prove it — and the failure mode is silent: every section heading
     * carries `font-sans`, which Tailwind compiles to `var(--font-sans)`. If the
     * RTL block sets only `font-family`, the body text becomes Cairo while all
     * seven headings stay on Inter. Asserted against the source so the variable
     * cannot quietly drop out.
     */
    #[Test]
    public function the_rtl_scope_redeclares_the_sans_variable_so_headings_follow_the_arabic_face(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);

        preg_match('~\[dir=.rtl.\]\s*\.landing-scope,\s*\.landing-scope\[dir=.rtl.\]\s*\{(.*?)\}~s', $css, $rule);

        $this->assertNotEmpty($rule, 'The RTL .landing-scope rule is missing from app.css.');
        $this->assertStringContainsString('--font-sans:', $rule[1]);
        $this->assertStringContainsString("'Cairo'", $rule[1]);

        // Eyebrows are `font-mono`, and JetBrains Mono has no Arabic glyphs.
        $this->assertStringContainsString('--font-mono:', $rule[1]);
        $this->assertStringContainsString('Cairo', $rule[1]);
    }

    /**
     * Cairo is requested from Google Fonts on the landing page. A bad family or
     * weight list returns a 400 and the page silently falls back to a system
     * font, so the families are pinned by name.
     */
    #[Test]
    public function the_landing_page_requests_the_arabic_face_and_its_weights(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        preg_match('~<link href="(https://fonts\.googleapis\.com/[^"]+)"~', $content, $link);

        $this->assertNotEmpty($link, 'The landing page loads no Google Fonts stylesheet.');

        $this->assertStringContainsString('family=Cairo:', $link[1]);
        $this->assertStringContainsString('wght@400;500;600;700;800', $link[1]);
        $this->assertStringContainsString('display=swap', $link[1]);
        $this->assertStringNotContainsString('IBM+Plex', $link[1]);
    }

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

    /**
     * The hub is an asymmetric 2x2: two equal cards down the start side and one
     * card in the end column matching both. The height classes are what actually
     * produce that split, so they are asserted rather than the intent behind them.
     */
    #[Test]
    public function the_browse_hub_splits_into_two_stacked_cards_and_one_matching_card(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $start = strpos($content, 'id="browse-hub"');
        $end = strpos($content, 'id="database-stats"');

        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        $hub = substr($content, $start, $end - $start);

        $this->assertStringContainsString('grid grid-cols-1 md:grid-cols-2', $hub);
        $this->assertSame(3, substr_count($hub, '<article'));
        $this->assertSame(2, substr_count($hub, 'flex-1 min-h-[260px]'));
        $this->assertSame(1, substr_count($hub, 'h-full min-h-[300px]'));

        // Vet Drugs is the card that spans both rows, so it is the end column's.
        // `route('drugs.home')` renders as a URL, not as the route name.
        $this->assertMatchesRegularExpression(
            '~<div>\s*<article[^>]*h-full min-h-\[300px\].*?href="[^"]*/drugs"~s',
            $hub
        );
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

    /**
     * The contact form dispatches `contact-sent` / `contact-failed`, but the
     * toast script resolves those to nothing unless the layout actually renders
     * the element it looks up by id. Without this the homepage submit cleared the
     * fields in silence.
     */
    #[Test]
    public function the_landing_layout_renders_the_toast_the_contact_form_drives(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="contactToast"', $content);
        $this->assertStringContainsString("window.Toast.show('contactToast', 'success'", $content);
        $this->assertStringContainsString("window.Toast.show('contactToast', 'error'", $content);
    }

    /**
     * The contact band is permanently dark, like the browse hub, so the theme
     * toggle flips the page around it rather than the band itself.
     */
    #[Test]
    public function the_contact_band_is_dark_in_both_themes(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        preg_match('~<section[^>]*id="contact"[^>]*>~', $content, $tag);

        $this->assertNotEmpty($tag, 'No section provides id="contact".');
        $this->assertStringContainsString('bg-slate-950', $tag[0]);
        $this->assertStringNotContainsString('dark:', $tag[0]);
    }

    /**
     * The button's off-state lives in the markup, not in the script: without it
     * the control is visible for the first paint, before app.js has run.
     */
    #[Test]
    public function the_landing_page_offers_a_floating_back_to_top(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="backToTop"', $content);

        preg_match('~<button[^>]*id="backToTop"[^>]*>~', $content, $tag);

        $this->assertNotEmpty($tag);
        $this->assertStringContainsString('fixed bottom-10 end-6', $tag[0]);
        $this->assertStringContainsString('opacity-0', $tag[0]);
        $this->assertStringContainsString('pointer-events-none', $tag[0]);
    }

    /**
     * Landing only. The /drugs pages are long too, but promoting the control to
     * app.layouts.master is a separate decision with its own palette to pick.
     */
    #[Test]
    public function the_back_to_top_is_landing_only(): void
    {
        $this->get('/')->assertOk()->assertSee('id="backToTop"', false);

        $this->get('/drugs')->assertOk()->assertDontSee('id="backToTop"', false);
    }

    /**
     * Arabic gets a redrawn photograph whose animals and wordmark sit on the
     * start side, because the Latin one flipped in CSS would mirror the
     * baked-in "ARVETPEDIA" into unreadable text. The Arabic file ships as a
     * PNG, so its `<picture>` must not offer the Latin WebP either.
     */
    #[Test]
    public function the_hero_serves_a_different_photograph_in_arabic(): void
    {
        $english = $this->landingIn('en')->getContent();

        $this->assertStringContainsString('images/landing/hero.webp', $english);
        $this->assertStringContainsString('images/landing/hero.jpg', $english);
        $this->assertStringNotContainsString('ARVETPEDIA', $english);
        $this->assertStringNotContainsString('scale-x-[-1]', $english);

        $arabic = $this->landingIn('ar')->getContent();

        // Percent-encoded: `asset()` leaves the space in the filename alone.
        $this->assertStringContainsString('images/landing/ARVETPEDIA%20RTL.png', $arabic);
        $this->assertStringNotContainsString('images/landing/hero.webp', $arabic);
        $this->assertStringNotContainsString('images/landing/hero.jpg', $arabic);
    }

    /**
     * The headline is stacked over three lines by splitting the translation on
     * `|`, so translators keep control of the line composition.
     */
    #[Test]
    public function the_hero_heading_is_stacked_over_three_lines(): void
    {
        foreach (['en', 'ar'] as $locale) {
            $content = $this->landingIn($locale)->getContent();

            preg_match('~<h1\b.*?</h1>~s', $content, $tag);

            $this->assertNotEmpty($tag, "The hero headline is missing in [{$locale}].");
            $this->assertSame(3, substr_count($tag[0], '<span class="block">'));

            // The separator is markup, so it must never reach the visitor.
            $this->assertStringNotContainsString('|', $tag[0]);
        }
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
