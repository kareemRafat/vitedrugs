<?php

namespace Tests\Feature\Knowledge;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
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
}
