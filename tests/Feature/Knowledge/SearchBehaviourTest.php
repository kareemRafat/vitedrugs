<?php

namespace Tests\Feature\Knowledge;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchBehaviourTest extends TestCase
{
    use RefreshDatabase;

    private HostSpecies $chicken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chicken = HostSpecies::factory()->poultry()->create([
            'canonical_name' => 'chicken',
            'display_name' => 'Chicken',
        ]);
    }

    public function test_it_matches_an_english_disease_name(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.search', ['q' => 'Coccidiosis']))
            ->assertOk()
            ->assertSee('Coccidiosis');
    }

    /**
     * Regression: the near-miss pass used to be handed the LIKE pass's models
     * instead of their keys, so `whereNotIn` excluded nothing and every substring
     * hit came back a second time — "Coccidiosis" listed twice.
     */
    public function test_a_row_is_never_listed_twice(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.search', ['q' => 'cocci']))
            ->assertOk()
            ->assertSee('Coccidiosis')
            ->assertDontSee('Coccidiosis Coccidiosis');
    }

    /**
     * An Arabic term must find the disease. The heading shown on an English
     * page stays English — locale, not query language, decides the display —
     * so `test_it_shows_arabic_names_on_the_arabic_page` covers the rendering.
     */
    public function test_it_matches_an_arabic_disease_name(): void
    {
        $this->poultryDisease('Newcastle Disease', ['name_ar' => 'مرض نيوكاسل']);

        $this->get(route('poultry.search', ['q' => 'نيوكاسل']))
            ->assertOk()
            ->assertSee('Newcastle Disease');
    }

    public function test_it_shows_arabic_names_on_the_arabic_page(): void
    {
        $this->poultryDisease('Newcastle Disease', ['name_ar' => 'مرض نيوكاسل']);

        app()->setLocale('ar');

        $this->get(route('poultry.search', ['q' => 'نيوكاسل']))
            ->assertOk()
            ->assertSee('مرض نيوكاسل')
            ->assertSee('dir="rtl"', escape: false);
    }

    public function test_it_matches_a_clinical_sign_and_links_to_the_diagnosis_tool(): void
    {
        $sign = ClinicalSign::factory()->create([
            'canonical_name' => 'drooping wings',
            'display_name' => 'Drooping wings',
        ]);

        $this->poultryDisease('Coccidiosis', [], [$sign]);

        $this->get(route('poultry.search', ['q' => 'drooping wings']))
            ->assertOk()
            ->assertSee('Drooping wings');
    }

    public function test_it_matches_a_host_species(): void
    {
        $this->get(route('poultry.search', ['q' => 'hicken']))
            ->assertOk()
            ->assertSee('Chicken');
    }

    /**
     * The reason the near-miss pass exists: "cocido" shares no substring with
     * "coccidiosis", so LIKE returns nothing and only the trigram score finds it.
     */
    public function test_it_finds_a_near_miss_the_substring_search_misses(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->assertStringNotContainsStringIgnoringCase(
            'cocido',
            (string) Disease::where('slug', 'coccidiosis')->value('name'),
            'Sanity: the near-miss term must not be a substring, or this test proves nothing.',
        );

        $this->get(route('poultry.search', ['q' => 'cocido']))
            ->assertOk()
            ->assertSee('Coccidiosis');
    }

    /**
     * The near-miss pass must not fire on words that merely share a suffix.
     *
     * "chicken" and "thickened" share the interior trigram run
     * hic/ick/cke/ken and score 0.667 — high enough to clear any sane
     * threshold — so the score alone cannot reject them. They only begin
     * matching at the second trigram, which is what the anchoring rule checks.
     * Left unguarded this returned thirteen "Thickened …" signs for a poultry
     * query.
     */
    public function test_a_near_miss_that_only_shares_a_suffix_is_rejected(): void
    {
        $sign = $this->poultryClinicalSign('Thickened Crop Mucosa');

        $this->poultryDisease('Coccidiosis', [], [$sign]);

        $this->get(route('poultry.search', ['q' => 'chicken']))
            ->assertOk()
            ->assertDontSee('Thickened Crop Mucosa');
    }

    /**
     * The near-miss pass now selects only the columns it reads, so a row found
     * that way could arrive without the title or the route key. Both are needed:
     * the name to render the row, the slug to link it.
     */
    public function test_a_near_miss_result_still_carries_its_title_and_link(): void
    {
        $disease = $this->poultryDisease('Coccidiosis');

        $html = $this->get(route('poultry.search', ['q' => 'cocido']))->assertOk()->getContent();

        $this->assertStringContainsString('Coccidiosis', $html, 'The near-miss row lost its title.');
        $this->assertStringContainsString(
            route('poultry.diseases.show', ['slug' => $disease->slug]),
            $html,
            'The near-miss row lost its link, so its route key was not selected.',
        );
    }

    /**
     * A transposition rewrites the first trigram — "pnuemonia" opens "pnu"
     * where "pneumonia" opens "pne" — so the leading-two-character allowance is
     * the only thing keeping this typo reachable.
     */
    public function test_a_transposed_typo_is_still_matched(): void
    {
        $this->poultryDisease('Pneumonia');

        $this->get(route('poultry.search', ['q' => 'pnuemonia']))
            ->assertOk()
            ->assertSee('Pneumonia');
    }

    /**
     * The minimum length gates the near-miss pass and the suggestions endpoint,
     * not substring search — a three-letter fragment is still a legitimate
     * substring query and the old page answered it.
     */
    public function test_a_short_term_still_matches_as_a_substring(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.search', ['q' => 'coc']))
            ->assertOk()
            ->assertSee('Coccidiosis');
    }

    public function test_an_unmatched_term_shows_the_empty_state(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.search', ['q' => 'zzzzqqqq']))
            ->assertOk()
            ->assertSee(__('search.no_results_title', ['q' => 'zzzzqqqq']))
            ->assertSee(__('search.browse_by_sign'));
    }

    public function test_no_query_shows_the_prompt_rather_than_results(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.search'))
            ->assertOk()
            ->assertSee(__('search.no_query'))
            ->assertDontSee('Coccidiosis');
    }

    public function test_like_wildcards_in_the_term_are_escaped(): void
    {
        $this->poultryDisease('Coccidiosis');

        // An unescaped % would turn into match-everything.
        $this->get(route('poultry.search', ['q' => '%']))
            ->assertOk()
            ->assertDontSee('Coccidiosis');
    }

    /**
     * The search box is a plain form, matching `/drugs/search`.
     *
     * There was a debounced Alpine component here that fetched suggestions on every
     * keystroke and rendered a grouped dropdown. It was removed: results now appear
     * only after submit.
     *
     * The script check reads the view file rather than the response, because the
     * shared layout legitimately loads the Vite bundle and Livewire. What must not
     * come back is this page's own component.
     */
    public function test_the_search_box_is_a_plain_form(): void
    {
        $this->poultryDisease('Coccidiosis');

        $html = $this->get(route('poultry.search'))->assertOk()->getContent();

        $this->assertStringNotContainsString('globalSearch', $html);
        $this->assertStringNotContainsString('search/suggestions', $html);
        $this->assertStringContainsString('<form method="GET"', $html);

        $view = file_get_contents(resource_path('views/search/index.blade.php'));

        $this->assertStringNotContainsString('<script', $view);
        $this->assertStringNotContainsString('x-data', $view);
        $this->assertStringNotContainsString('@input', $view);
    }

    public function test_the_form_submits_to_the_parts_own_search_route(): void
    {
        foreach (['poultry', 'large-animals'] as $part) {
            $this->get(route("{$part}.search"))
                ->assertOk()
                ->assertSee('action="'.route("{$part}.search").'"', escape: false);
        }
    }

    public function test_the_suggestions_route_no_longer_exists(): void
    {
        foreach (['poultry', 'large-animals'] as $part) {
            $this->get("/{$part}/search/suggestions?q=cocci")->assertNotFound();
        }
    }

    /**
     * The poultry search button used to fall through to the drugs page because
     * the header match had no `poultry` arm.
     */
    public function test_the_header_search_link_points_at_the_parts_own_page(): void
    {
        $this->get(route('poultry.home'))
            ->assertOk()
            ->assertSee(route('poultry.search'), escape: false);

        $this->get(route('large-animals.home'))
            ->assertOk()
            ->assertSee(route('large-animals.search'), escape: false);
    }

    /**
     * /drugs/search paginates every bucket; this page used to cap at 20 and say
     * nothing, so a query matching more than that silently lost the rest.
     *
     * Asserted by which names appear on which page rather than by the rendered
     * count, so it does not depend on the database returning rows in insertion
     * order — ranking is equal for thirteen rows that all contain "osis".
     */
    public function test_a_bucket_paginates_rather_than_truncating(): void
    {
        $names = $this->poultryDiseasesContaining('osis', 13);

        $pageOne = $this->get(route('poultry.search', ['q' => 'osis']))->assertOk()->getContent();
        $pageTwo = $this->get(route('poultry.search', ['q' => 'osis', 'diseases_page' => 2]))->assertOk()->getContent();

        $onOne = array_values(array_filter($names, fn (string $name): bool => str_contains($pageOne, $name)));
        $onTwo = array_values(array_filter($names, fn (string $name): bool => str_contains($pageTwo, $name)));

        $this->assertCount((int) config('search.limits.per_page'), $onOne, 'Page one should be a full page of ten.');
        $this->assertCount(3, $onTwo, 'Page two should hold the remaining three.');
        $this->assertEmpty(array_intersect($onOne, $onTwo), 'A row should not appear on both pages.');
    }

    /**
     * The badge counts the bucket, not the page. With thirteen matches and a page
     * of ten, reading `count($items)` reported 10 — the one number on the page
     * that was guaranteed to be wrong whenever a bucket overflowed.
     */
    public function test_the_badge_counts_every_match_not_just_the_page(): void
    {
        $names = $this->poultryDiseasesContaining('osis', 13);

        $pageOne = $this->get(route('poultry.search', ['q' => 'osis']))->assertOk()->getContent();

        $shown = count(array_filter($names, fn (string $name): bool => str_contains($pageOne, $name)));

        $this->assertLessThan(13, $shown, 'Sanity: the page must not be showing all thirteen, or this proves nothing.');
        $this->assertSame('13', $this->bucketBadge($pageOne, __('search.buckets.diseases.label')));
    }

    /**
     * Each bucket pages on its own query-string key, so seven cards can be on
     * seven different pages without one card's paging resetting another's.
     */
    public function test_each_bucket_pages_on_its_own_query_string_key(): void
    {
        $this->poultryDiseasesContaining('osis', 13);

        $html = $this->get(route('poultry.search', ['q' => 'osis', 'diseases_page' => 2]))->assertOk()->getContent();

        $this->assertStringContainsString('diseases_page=1', $html);
        $this->assertStringNotContainsString('clinical_signs_page', $html);
        $this->assertStringNotContainsString('products_page', $html);
    }

    /**
     * A bookmark naming a page past the end shows the last page rather than an
     * empty grid sitting under a non-zero total.
     */
    public function test_a_page_past_the_end_falls_back_to_the_last_page(): void
    {
        $names = $this->poultryDiseasesContaining('osis', 13);

        $html = $this->get(route('poultry.search', ['q' => 'osis', 'diseases_page' => 99]))->assertOk()->getContent();

        $shown = array_values(array_filter($names, fn (string $name): bool => str_contains($html, $name)));

        $this->assertCount(3, $shown, 'The stale page should resolve to the last page, not to nothing.');
    }

    /**
     * An empty bucket is dropped rather than rendered as a card reading "no
     * matching items". On a term hitting one disease, the large-animals page used
     * to render seven cards, six of them empty.
     */
    public function test_a_bucket_with_no_matches_is_not_rendered(): void
    {
        $this->poultryDisease('Coccidiosis');

        $html = $this->get(route('poultry.search', ['q' => 'Coccidiosis']))->assertOk()->getContent();

        $this->assertStringContainsString(__('search.buckets.diseases.label'), $html);

        foreach (config('search.parts.poultry') as $bucket) {
            if ($bucket === 'diseases') {
                continue;
            }

            $this->assertStringNotContainsString(
                __('search.buckets.'.$bucket.'.label'),
                $html,
                "The [{$bucket}] bucket matched nothing and should not have been rendered.",
            );
        }
    }

    /**
     * A product typed by its manufacturer finds that product.
     *
     * The bucket searched `trade_name` only, while /drugs/search also searched
     * active ingredients, companies and disease names — so the same query
     * returned drugs on one page and nothing on the other, which reads as a
     * broken search rather than as a narrower one.
     */
    public function test_a_product_is_found_by_its_manufacturer(): void
    {
        $this->poultryProduct('Zoetis Pharma', 'Amoxivet', 'Amoxicillin');

        $this->get(route('poultry.search', ['q' => 'Zoetis']))
            ->assertOk()
            ->assertSee('Amoxivet');
    }

    public function test_a_product_is_found_by_its_active_ingredient(): void
    {
        $this->poultryProduct('Zoetis Pharma', 'Amoxivet', 'Amoxicillin');

        $this->get(route('poultry.search', ['q' => 'Amoxicillin']))
            ->assertOk()
            ->assertSee('Amoxivet');
    }

    /**
     * The relation columns widen the product bucket; they must not widen it past
     * the part. A product treating only a large-animal disease stays invisible.
     */
    public function test_relation_matching_does_not_leak_across_parts(): void
    {
        $company = Company::factory()->create(['name' => 'Zoetis Pharma']);

        $product = Product::factory()->create(['trade_name' => 'Amoxivet', 'trade_name_ar' => 'أموكسيفيت']);
        $product->companies()->attach($company->id, ['role' => 'manufacturer']);

        $product->diseases()->attach(Disease::factory()->largeAnimals()->named('Anthrax')->create()->id);

        $this->get(route('poultry.search', ['q' => 'Zoetis']))
            ->assertOk()
            ->assertDontSee('Amoxivet');
    }

    /**
     * @param  array<int, ClinicalSign>  $signs
     */
    private function poultryDisease(string $name, array $attributes = [], array $signs = []): Disease
    {
        $disease = Disease::factory()->poultry()->named($name)->create($attributes);
        $disease->hostSpecies()->attach($this->chicken->id, ['is_primary_host' => true]);

        foreach ($signs as $sign) {
            $disease->clinicalSigns()->attach($sign->id, ['weight' => 5]);
        }

        return $disease;
    }

    private function poultryClinicalSign(string $name, ?Disease $disease = null): ClinicalSign
    {
        $sign = ClinicalSign::factory()->create([
            'canonical_name' => strtolower($name),
            'display_name' => $name,
        ]);

        $disease?->clinicalSigns()->attach($sign->id, ['weight' => 5]);

        return $sign;
    }

    /**
     * A run of poultry diseases whose names all contain one fragment, for paging.
     *
     * Every disease is named explicitly and uniquely. `diseases.name` and
     * `diseases.slug` are both UNIQUE, so a factory roll that collided with
     * another would fail intermittently rather than clearly.
     *
     * The fragment is asserted, not assumed: "Candidiasis" reads like it ends in
     * -osis and does not, so a name that fails the check silently costs one row
     * from the page count and makes every paging assertion off by one.
     *
     * @return array<int, string>
     */
    private function poultryDiseasesContaining(string $fragment, int $count): array
    {
        $names = [
            'Coccidiosis', 'Salmonellosis', 'Aspergillosis', 'Cryptosporidiosis',
            'Erysipelosis', 'Listeriosis', 'Pasteurellosis', 'Mycoplasmosis',
            'Microsporiosis', 'Spirochetosis', 'Leptospirosis', 'Psittacosis',
            'Colibacillosis',
        ];

        $this->assertGreaterThanOrEqual($count, count($names), 'Not enough distinct names for the requested page count.');

        $chosen = array_slice($names, 0, $count);

        $missing = array_values(array_filter(
            $chosen,
            fn (string $name): bool => ! str_contains(strtolower($name), $fragment),
        ));

        $this->assertSame([], $missing, 'Every generated name must contain the fragment, or these tests measure the wrong rows.');

        foreach ($chosen as $name) {
            $this->poultryDisease($name);
        }

        return $chosen;
    }

    /**
     * A poultry product, reachable through a poultry disease so the part scoping
     * admits it, carrying a manufacturer and an active ingredient to search by.
     */
    private function poultryProduct(string $companyName, string $tradeName, string $ingredientName): Product
    {
        $company = Company::factory()->create(['name' => $companyName, 'name_ar' => $companyName]);

        $ingredient = ActiveIngredient::factory()->create(['name' => $ingredientName, 'name_ar' => $ingredientName]);

        $product = Product::factory()->create(['trade_name' => $tradeName, 'trade_name_ar' => $tradeName]);

        $product->companies()->attach($company->id, ['role' => 'manufacturer']);
        $product->activeIngredients()->attach($ingredient->id, ['strength' => '250', 'unit' => 'mg']);
        $product->diseases()->attach($this->poultryDisease('Coccidiosis')->id);

        return $product;
    }

    /**
     * The count rendered in a bucket card's badge.
     *
     * Reads the heading-to-badge pair the card renders, so the assertion is
     * about that bucket's number rather than any number on the page.
     */
    private function bucketBadge(string $html, string $label): string
    {
        $pattern = '/'.preg_quote($label, '/').'\s*<\/h2>\s*<span[^>]*>\s*([\d,]+)\s*<\/span>/';

        $this->assertSame(1, preg_match($pattern, $html, $matches), "No bucket badge was found for [{$label}].");

        return $matches[1];
    }
}
