<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\LargeAnimals\MedicalArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The three part hubs — /drugs, /large-animals, /poultry.
 *
 * These pages were rebuilt onto a shared set of x-part.* components so the three
 * of them look like one site. That is a structural promise rather than a visual
 * one, so it is asserted structurally: same components, same shared chrome, and
 * no link that leaves the part.
 *
 * The counts themselves are covered by the parts' own tests
 * (PoultryLandingTest and friends). What is checked here is what the rebuild
 * changed: the shared build, and the plain lists.
 */
class PartLandingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The source file behind each hub, so the shared-build assertions read the
     * templates rather than the rendered HTML.
     *
     * @return array<string, array{0: string, 1: string}>
     */
    public static function partViews(): array
    {
        return [
            'drugs' => ['/drugs', 'app/drugs.blade.php'],
            'large animals' => ['/large-animals', 'large-animals/landing.blade.php'],
            'poultry' => ['/poultry', 'poultry/landing.blade.php'],
        ];
    }

    /** @return array<string, array{0: string}> */
    public static function partPaths(): array
    {
        return [
            'drugs' => ['/drugs'],
            'large animals' => ['/large-animals'],
            'poultry' => ['/poultry'],
        ];
    }

    // -------------------------------------------------------------- structure

    #[Test]
    #[DataProvider('partPaths')]
    public function it_renders_in_both_locales(string $path): void
    {
        $this->get($path)->assertOk();

        app()->setLocale('ar');
        $this->get($path)->assertOk()->assertSee('dir="rtl"', escape: false);
    }

    /**
     * The rebuild must not have become a second redesign of the site chrome.
     * All three keep app.layouts.master, so they keep the part-aware navbar, its
     * mobile drawer and the `data-part` attribute that themes the whole page.
     */
    #[Test]
    #[DataProvider('partPaths')]
    public function it_keeps_the_shared_chrome(string $path): void
    {
        $content = $this->get($path)->assertOk()->getContent();

        $this->assertStringContainsString('data-part=', $content);
        $this->assertStringContainsString('mobileSidebar', $content);

        // The homepage's own floating chrome stayed over there.
        $this->assertStringNotContainsString('id="site-header"', $content);
    }

    /**
     * The whole point of the rebuild: one set of components, three pages. If a
     * page is ever rebuilt without them it will drift away from the other two.
     *
     * Asserted against the template source, not the response — an anonymous
     * component is compiled away, so its name never reaches the HTML.
     */
    #[Test]
    #[DataProvider('partViews')]
    public function it_is_built_from_the_shared_part_components(string $path, string $view): void
    {
        $source = (string) file_get_contents(resource_path('views/'.$view));

        $this->assertNotEmpty($source, "Missing view: {$view}");

        foreach (['part-hero', 'hero-action', 'stat-tile', 'section-title', 'tool-card', 'cta-bar'] as $component) {
            $this->assertStringContainsString(
                '<x-part.'.$component,
                $source,
                "[{$path}] is not built from x-part.{$component}."
            );
        }
    }

    #[Test]
    #[DataProvider('partPaths')]
    public function it_ships_no_unresolved_translation_keys(string $path): void
    {
        foreach (['en', 'ar'] as $locale) {
            app()->setLocale($locale);

            $content = $this->get($path)->assertOk()->getContent();

            preg_match_all('/(?:messages|large-animals|poultry)\.[a-zA-Z0-9_.]+/', $content, $matches);

            $this->assertSame(
                [],
                array_values(array_unique($matches[0])),
                "[{$path}] renders unresolved keys in [{$locale}]."
            );
        }
    }

    /**
     * A dead link on a landing page is worse than a missing section, and these
     * pages gained a dozen new ones. Every internal href has to resolve.
     */
    #[Test]
    #[DataProvider('partPaths')]
    public function every_internal_link_resolves_to_a_route(string $path): void
    {
        $content = $this->get($path)->assertOk()->getContent();

        preg_match_all('~href="(https?://[^"]+|/[^"#?]*)"~', $content, $matches);

        $known = Collection::make(Route::getRoutes()->getRoutes())
            ->map(fn (RoutingRoute $route): string => $route->uri())
            ->all();

        foreach ($matches[1] as $href) {
            // Absolute URLs to other hosts (fonts, CDNs) are not our problem.
            if (str_starts_with($href, 'http')) {
                continue;
            }

            $uri = ltrim(explode('?', $href)[0], '/');

            // Strip the locale prefix the same way the framework's matcher does.
            if (preg_match('#^(en|ar)/(.+)$#', $uri, $m)) {
                $uri = $m[2];
            }

            $resolves = collect($known)->contains(
                fn (string $knownUri): bool => $knownUri === $uri
                    || $knownUri === $uri.'/{slug}'
                    || $knownUri === $uri.'/{product}'
            );

            $this->assertTrue($resolves, "[{$path}] links to [{$href}], which matches no route.");
        }
    }

    // ------------------------------------------------------------- article band

    /**
     * The article band renders real published rows as teaser cards, built from
     * the same x-part.content-card the /drugs blog band uses.
     */
    #[Test]
    public function large_animals_lists_its_latest_articles_as_teaser_cards(): void
    {
        $article = MedicalArticle::factory()->create([
            'title' => 'Bovine Mastitis: A Practical Guide',
            'title_ar' => 'التهاب الضرع في الأبقار: دليل عملي',
            'slug' => 'bovine-mastitis',
            'species' => 'Cattle',
            'article_type' => 'review',
            'summary' => 'Recognising the quarters, the streaks and the cow.',
            'is_published' => true,
        ]);

        $html = $this->get('/large-animals')->assertOk()->getContent() ?: '';

        $this->assertStringContainsString('Bovine Mastitis: A Practical Guide', $html);
        $this->assertStringContainsString('Recognising the quarters, the streaks and the cow.', $html);
        $this->assertStringContainsString('Cattle', $html);

        // The chip reads the same type map the full index uses.
        $this->assertStringContainsString(__('large-animals.medical_articles.types.review'), $html);

        $this->assertStringContainsString(
            route('large-animals.medical-articles.show', ['slug' => $article->slug]),
            $html
        );
    }

    /**
     * The band and the /drugs blog band are the same component, so a change to
     * one cannot silently leave the other behind.
     */
    #[Test]
    public function both_teaser_bands_are_built_from_the_same_card(): void
    {
        Blog::factory()->create(['is_active' => true, 'published_at' => now()->subDay()]);
        MedicalArticle::factory()->create(['is_published' => true]);

        foreach (['/drugs', '/large-animals'] as $path) {
            $source = (string) file_get_contents(resource_path('views/'.match ($path) {
                '/drugs' => 'app/drugs.blade.php',
                '/large-animals' => 'large-animals/landing.blade.php',
            }));

            // Each page delegates to its own model-mapping wrapper...
            $this->assertMatchesRegularExpression(
                '/<x-part\.(post-card|article-card)/',
                $source,
                "[{$path}] renders its teaser band through a card wrapper."
            );
        }

        // ...and both of those wrappers delegate to the one shared card.
        foreach (['post-card', 'article-card'] as $wrapper) {
            $source = (string) file_get_contents(resource_path('views/app/components/part/'.$wrapper.'.blade.php'));

            $this->assertStringContainsString(
                '<x-part.content-card',
                $source,
                "x-part.{$wrapper} must render x-part.content-card."
            );
        }
    }

    /**
     * An unpublished row is invisible everywhere else on the site, so it has to
     * stay invisible here too.
     */
    #[Test]
    public function large_animals_hides_unpublished_articles(): void
    {
        MedicalArticle::factory()->create([
            'title' => 'Draft Working Notes',
            'slug' => 'draft-notes',
            'is_published' => false,
        ]);

        $this->get('/large-animals')
            ->assertOk()
            ->assertDontSee('Draft Working Notes');
    }

    /**
     * An empty table must shorten the page, not render an empty shell. This is
     * the case a fresh install hits.
     */
    #[Test]
    public function the_article_band_disappears_when_there_is_nothing_to_show(): void
    {
        $html = $this->get('/large-animals')->assertOk()->getContent() ?: '';

        $this->assertStringNotContainsString(__('large-animals.landing.articles_heading'), $html);
    }

    // ------------------------------------------------------------- blog band

    /**
     * The teaser card is built from title, excerpt, category, date and read
     * time — there is no cover image on any row, so those five fields are all
     * the evidence the band has to work with.
     */
    #[Test]
    public function the_drugs_blog_band_renders_the_data_a_post_actually_has(): void
    {
        $category = BlogCategory::factory()->create(['name' => 'Pharmacology', 'name_ar' => 'علم الأدوية']);

        $post = Blog::factory()->create([
            'blog_category_id' => $category->id,
            'title' => 'Withdrawal Periods Explained',
            'excerpt' => 'Why a meat withdrawal period is measured from the last dose.',
            'excerpt_ar' => 'لماذا تُقاس فترة السحب من آخر جرعة.',
            'is_active' => true,
            'published_at' => now()->subDays(2),
        ]);

        $html = $this->get('/drugs')->assertOk()->getContent() ?: '';

        $this->assertStringContainsString('Withdrawal Periods Explained', $html);
        $this->assertStringContainsString('Why a meat withdrawal period is measured from the last dose.', $html);
        $this->assertStringContainsString('Pharmacology', $html);
        $this->assertStringContainsString(route('blog.show', $post), $html);
        $this->assertStringContainsString(
            $post->published_at->toDateString(),
            $html,
            'The card footer should carry a machine-readable <time datetime>.'
        );
    }

    /**
     * The card picks the Arabic column when the locale is Arabic. `Blog` has
     * three separate `_ar` columns and picking the wrong one renders English
     * copy on an Arabic page.
     */
    #[Test]
    public function the_drugs_blog_band_serves_arabic_copy_on_the_arabic_page(): void
    {
        Blog::factory()->create([
            'title' => 'Withdrawal Periods Explained',
            'title_ar' => 'شرح فترات السحب',
            'excerpt_ar' => 'لماذا تُقاس فترة السحب من آخر جرعة.',
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        app()->setLocale('ar');

        $html = $this->get('/drugs')->assertOk()->getContent() ?: '';

        $this->assertStringContainsString('شرح فترات السحب', $html);
        $this->assertStringContainsString('لماذا تُقاس فترة السحب من آخر جرعة.', $html);
    }

    /**
     * The band hides itself rather than rendering an empty shell — the case a
     * site with no published posts hits.
     */
    #[Test]
    public function the_drugs_blog_band_disappears_when_nothing_is_published(): void
    {
        Blog::factory()->draft()->create();

        $html = $this->get('/drugs')->assertOk()->getContent() ?: '';

        $this->assertStringNotContainsString(__('messages.drugs_hub.latest_blog'), $html);
    }

    // ------------------------------------------------------- translation parity

    /**
     * The three landing groups gained keys together. A key added to English and
     * forgotten in Arabic renders the raw key string to an Arabic visitor.
     *
     * @return array<string, array{0: string}>
     */
    public static function landingGroups(): array
    {
        return [
            'drugs' => ['messages.drugs_hub'],
            'large animals' => ['large-animals.landing'],
            'poultry' => ['poultry.landing'],
        ];
    }

    #[Test]
    #[DataProvider('landingGroups')]
    public function every_landing_key_exists_in_both_locales(string $group): void
    {
        $english = $this->flatten((array) trans($group, [], 'en'));
        $arabic = $this->flatten((array) trans($group, [], 'ar'));

        $this->assertSame(
            [],
            array_values(array_diff($english, $arabic)),
            'Missing from Arabic: '.implode(', ', array_diff($english, $arabic))
        );

        $this->assertSame(
            [],
            array_values(array_diff($arabic, $english)),
            'Missing from English: '.implode(', ', array_diff($arabic, $english))
        );
    }

    /**
     * Flatten a nested translation array to dotted paths.
     *
     * @param  array<mixed>  $values
     * @return list<string>
     */
    private function flatten(array $values, string $prefix = ''): array
    {
        $flat = [];

        foreach ($values as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $flat = [...$flat, ...$this->flatten($value, $path)];
            } else {
                $flat[] = $path;
            }
        }

        return $flat;
    }
}
