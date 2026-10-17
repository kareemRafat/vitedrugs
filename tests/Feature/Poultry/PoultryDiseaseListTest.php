<?php

namespace Tests\Feature\Poultry;

use App\Enums\OntologyType;
use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The part disease list — `/poultry/diseases` and `/large-animals/diseases`.
 *
 * Two things this page had to get right that a list of anything else would not
 * have:
 *
 * **Sign and lesion counts exclude rather than match.** `findings.ontology_type`
 * is nullable and is null on the curated signs — Pyrexia, Inappetence and
 * Hemorrhagic diarrhea — so counting `ontology_type = 'sign'` silently drops the
 * three most common signs in the clinical vocabulary. Phase 7 hit this on the
 * comparison matrix; `counts_signs_by_excluding_lesions` is the guard.
 *
 * **Neither part had a list before this.** Both went from the hub straight to
 * `diseases/{slug}`, so there is no large-animal page this could have diverged
 * from — and the part scoping has to be asserted rather than assumed.
 */
class PoultryDiseaseListTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------- the page lands

    #[Test]
    public function the_list_renders(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.diseases.index'))
            ->assertOk()
            ->assertSee(__('poultry.diseases_list.heading'))
            ->assertSee('Coccidiosis');
    }

    /**
     * The other half of the shared component. Built at the same time as the
     * poultry route, because a shared component with one wired part is a
     * component nobody has checked.
     */
    #[Test]
    public function the_large_animals_half_renders_too(): void
    {
        $this->largeAnimalDisease('Foot and Mouth Disease');

        $this->get(route('large-animals.diseases.index'))
            ->assertOk()
            ->assertSee('Foot and Mouth Disease');
    }

    #[Test]
    public function the_list_renders_in_arabic(): void
    {
        app()->setLocale('ar');

        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.diseases.index'))
            ->assertOk()
            ->assertSee(__('poultry.diseases_list.heading', [], 'ar'), escape: false);
    }

    /**
     * `:count` is substituted, not printed. `__()` returns the key unchanged when
     * it is given no replacement map, so a helper forwarding only the key turns
     * every placeholder into literal `:count` text — which renders fine visually
     * and is wrong in every language.
     */
    #[Test]
    public function no_translation_placeholder_reaches_the_page(): void
    {
        $this->poultryDisease('Coccidiosis');
        $this->poultryDisease('Newcastle Disease');

        $html = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])->html();

        $this->assertStringNotContainsString(':count', $html);
        $this->assertStringContainsString('2 diseases', $html);
    }

    // --------------------------------------------------------------- part scoping

    /**
     * A part list that is not scoped leaks the other part, and it looks fine while
     * it does: 151 poultry diseases and 18 large-animal ones render as an ordinary
     * list. This is the same defect class as the unscoped search page and the
     * unscoped comparison picker.
     */
    #[Test]
    public function a_disease_from_the_other_part_is_not_listed(): void
    {
        $this->largeAnimalDisease('Foot and Mouth Disease');
        $poultry = $this->poultryDisease('Coccidiosis');

        Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->assertSee($poultry->name)
            ->assertDontSee('Foot and Mouth Disease');
    }

    #[Test]
    public function an_inactive_disease_is_not_counted_in_the_hero(): void
    {
        $this->poultryDisease('Coccidiosis');
        $this->poultryDisease('Retired Syndrome')->update(['is_active' => false]);

        $component = Livewire::test('knowledge.disease-list', ['part' => 'poultry']);

        $this->assertSame(1, $component->instance()->totals()['diseases']);
    }

    // ------------------------------------------------------- signs vs lesions

    /**
     * **The exclusion rule, pinned.**
     *
     * The curated signs carry `NULL` on `findings.ontology_type` — and NULL is not
     * a lesion, so Pyrexia belongs on the clinical-sign count. Counting
     * `ontology_type = 'sign'` instead would drop the single most common sign in
     * the veterinary vocabulary from this card while the detail page showed it,
     * which is the exact failure Phase 7 found on the comparison matrix.
     */
    #[Test]
    public function counts_signs_by_excluding_lesions(): void
    {
        $disease = $this->poultryDisease('Newcastle Disease');

        // A curated sign: null on `ontology_type`, exactly like Pyrexia in the data.
        $this->attach($disease, 'Pyrexia', null);
        $this->attach($disease, 'Head tremors', OntologyType::Sign);
        $this->attach($disease, 'Tracheal hemorrhage', OntologyType::Lesion);

        $row = $this->firstRow('Newcastle Disease');

        $this->assertSame(2, $row['sign_items_count'], 'A null ontology_type is an observation, not a lesion.');
        $this->assertSame(1, $row['lesion_items_count']);
    }

    /**
     * The list and the detail page must not disagree about what a disease
     * carries, or a reader counts two signs on a card and then finds one.
     */
    #[Test]
    public function the_counts_agree_with_the_detail_page(): void
    {
        $disease = $this->poultryDisease('Newcastle Disease');

        $this->attach($disease, 'Pyrexia', null);
        $this->attach($disease, 'Tracheal hemorrhage', OntologyType::Lesion);

        $disease->load('clinicalSigns.finding');

        $this->assertSame(count($disease->signItems()), $this->firstRow('Newcastle Disease')['sign_items_count']);
        $this->assertSame(count($disease->lesionItems()), $this->firstRow('Newcastle Disease')['lesion_items_count']);
    }

    // ------------------------------------------------------------ letter filter

    /**
     * Filtered in SQL, not in PHP after the paginator has chosen a page. Done the
     * other way round, "A" on page 7 of the unfiltered list returns nothing while
     * the pager still offers six more pages — the atlas hit that and its test
     * comments explain why it matters.
     */
    #[Test]
    public function the_letter_filter_narrows_the_list(): void
    {
        $this->poultryDisease('Coccidiosis');
        $this->poultryDisease('Newcastle Disease');
        $this->poultryDisease('Avian Influenza');

        Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->assertSee('Coccidiosis')
            ->call('selectLetter', 'N')
            ->assertSee('Newcastle Disease')
            ->assertDontSee('Coccidiosis')
            ->assertDontSee('Avian Influenza');
    }

    /**
     * A letter with nothing under it must render the empty state rather than a
     * silent blank grid, or the reader cannot tell "no results" from "failed to
     * load".
     */
    #[Test]
    public function a_letter_with_no_diseases_says_so(): void
    {
        $this->poultryDisease('Coccidiosis');

        Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->call('selectLetter', 'Z')
            ->assertSee(__('poultry.diseases_list.empty_title'))
            ->assertDontSee('Coccidiosis');
    }

    /**
     * The rail is the reader's way back out, so it is also the "off" control.
     */
    #[Test]
    public function clicking_the_active_letter_clears_the_filter(): void
    {
        $this->poultryDisease('Coccidiosis');
        $this->poultryDisease('Newcastle Disease');

        $component = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->call('selectLetter', 'N');

        $this->assertSame('N', $component->instance()->letter);

        $component->call('selectLetter', 'N')
            ->assertSee('Coccidiosis')
            ->assertSee('Newcastle Disease');

        $this->assertSame('', $component->instance()->letter);
    }

    /**
     * The rail offers the letters this part actually has. A hardcoded A–Z on a
     * part with 18 diseases is mostly dead keys, and a dead key looks identical
     * to a broken one.
     */
    #[Test]
    public function the_rail_offers_the_letters_that_have_diseases(): void
    {
        $this->poultryDisease('Coccidiosis');
        $this->poultryDisease('Newcastle Disease');

        $letters = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->instance()
            ->letters();

        $this->assertSame(['C', 'N'], $letters);
    }

    /**
     * The rail is built from the canonical name, never the localised one. On the
     * Arabic page the localised first character is never `[A-Z]`, so a localised
     * alphabet would match nothing there.
     */
    #[Test]
    public function the_rail_ignores_the_localised_name(): void
    {
        $this->poultryDisease('Coccidiosis', ['name_ar' => 'الكوكسيديا']);

        $this->assertSame(
            ['C'],
            Livewire::test('knowledge.disease-list', ['part' => 'poultry'])->instance()->letters(),
        );
    }

    /**
     * Pagination and the filter must agree. `<x-pagination>` builds its page links
     * from `withQueryString()`, which reads the *request's* query string — and a
     * Livewire update has none, the filter travelling in the request body. So the
     * component appends the filter itself; without that, filtering to "N" and
     * clicking page 2 silently drops the filter while the rail still reads "N".
     */
    #[Test]
    public function pager_links_keep_the_letter_filter(): void
    {
        foreach (range(1, 25) as $i) {
            $this->poultryDisease(sprintf('Newcastle Variant %02d', $i));
        }

        $html = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->call('selectLetter', 'N')
            ->html();

        preg_match_all('/href="([^"]*page=2[^"]*)"/', $html, $matches);

        $this->assertNotEmpty($matches[1], 'The list produced no second-page link to inspect.');

        foreach ($matches[1] as $href) {
            $this->assertStringContainsString(
                'letter=N',
                html_entity_decode($href),
                "Pager link [{$href}] drops the letter filter, so page 2 is of the unfiltered list.",
            );
        }
    }

    /**
     * A relative href here becomes `/poultry/poultry/diseases?page=2` under
     * `wire:navigate` and 404s on every page after the first — the paginator reuses
     * the request path, which arrives without its leading slash inside a Livewire
     * render.
     */
    #[Test]
    public function every_pager_link_resolves_to_this_list(): void
    {
        foreach (range(1, 25) as $i) {
            $this->poultryDisease(sprintf('Coccidiosis Variant %02d', $i));
        }

        $html = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])->html();

        preg_match_all('/href="([^"]*page=\d+[^"]*)"/', $html, $matches);

        $this->assertNotEmpty($matches[1]);

        foreach ($matches[1] as $href) {
            $path = parse_url(html_entity_decode($href), PHP_URL_PATH);

            $this->assertStringEndsWith(
                '/poultry/diseases',
                (string) $path,
                "Pager link [{$href}] does not resolve to the disease list.",
            );
        }
    }

    // ---------------------------------------------------------------- the card

    /**
     * Every one of the 30 curated rows stores `name_ar` identical to `name`, so an
     * unguarded `@if (filled($name_ar))` prints the name twice on all of them.
     * `⚡part-catalogue` already hit this on the drug cards.
     *
     * Asserted on element text (`>Name<`) rather than on the raw string. The name
     * legitimately appears twice in the markup — as the heading and as the `title`
     * attribute carrying the full string for a truncated two-line heading — and a
     * bare `substr_count` cannot tell that apart from the bug it is guarding.
     */
    #[Test]
    public function the_arabic_name_renders_once_not_twice(): void
    {
        $this->poultryDisease('Coccidiosis', ['name_ar' => 'Coccidiosis']);

        $html = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])->html();

        $this->assertSame(
            1,
            substr_count($html, '>Coccidiosis<'),
            'The Arabic line is rendered even though it repeats the English name, so every card prints its title twice.',
        );
    }

    #[Test]
    public function a_genuinely_different_arabic_name_is_shown(): void
    {
        $this->poultryDisease('Coccidiosis', ['name_ar' => 'الكواكسيديا']);

        $html = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])->html();

        $this->assertStringContainsString('>الكواكسيديا<', $html);
    }

    #[Test]
    public function each_card_links_to_its_own_detail_page(): void
    {
        $disease = $this->poultryDisease('Coccidiosis');

        Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->assertSee(route('poultry.diseases.show', $disease->slug), escape: false);
    }

    // -------------------------------------------------------------- performance

    /**
     * The three counts are subqueries on the one paginator query. Done the obvious
     * way — three loaded relations per card — it is 21 cards × 3 = 63 queries on a
     * full page, and the ceiling below is what catches a regression to that.
     */
    #[Test]
    public function the_list_does_not_grow_its_query_count_with_the_disease_count(): void
    {
        foreach (range(1, 60) as $i) {
            $disease = $this->poultryDisease(sprintf('Newcastle Variant %02d', $i));
            $this->attach($disease, 'Pyrexia', null, 'pyrexia_'.$i);
        }

        $queries = 0;

        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        Livewire::test('knowledge.disease-list', ['part' => 'poultry'])->html();

        $this->assertLessThanOrEqual(
            8,
            $queries,
            "The list issued {$queries} queries for one page. The counts must be subqueries, not loaded relations.",
        );
    }

    // -------------------------------------------------------------- wiring

    /**
     * A route nobody can reach is not a feature. Both the header and the hub have
     * to link it, or the page exists only for someone who types the URL.
     *
     * `assertSee(route(...))` alone is not enough, and this test is the second
     * place that has been shown the gap. It passed green over a navbar whose
     * Diseases link existed **only in the desktop bar**, leaving the page
     * unreachable on a phone — the drawer is a separately authored copy of the
     * same nav, and nothing was asserting on it.
     */
    #[Test]
    public function the_list_is_linked_from_the_header_and_the_hub(): void
    {
        $this->get(route('poultry.home'))
            ->assertOk()
            ->assertSee(route('poultry.diseases.index'), escape: false)
            ->assertSee(__('poultry.landing.services.diseases.title'))
            ->assertSee(__('poultry.nav.diseases'));

        $this->get(route('large-animals.home'))
            ->assertOk()
            ->assertSee(route('large-animals.diseases.index'), escape: false)
            ->assertSee(__('large-animals.nav.diseases'));
    }

    /**
     * The navbar is written twice — a desktop bar and a mobile drawer — and they
     * drift. Asserting the href exists proves one of them has it, not both, so
     * this pins the drawer specifically: `onclick="toggleMobileMenu()"` is on
     * every drawer link and on no desktop link, which is what makes it a usable
     * discriminator.
     */
    #[Test]
    public function the_mobile_drawer_carries_the_disease_link_on_both_parts(): void
    {
        foreach (['poultry', 'large-animals'] as $part) {
            $html = $this->get(route("{$part}.home"))->assertOk()->getContent();

            $this->assertStringContainsString(
                'href="'.route("{$part}.diseases.index").'" onclick="toggleMobileMenu()"',
                (string) $html,
                "The {$part} mobile drawer has no Diseases link, so the page is unreachable on a phone.",
            );
        }
    }

    /**
     * **A missing translation key renders as the key itself, silently.** Laravel
     * returns the key unchanged when it is absent, so `__('large-animals.nav.
     * diseases')` printed the literal string `large-animals.nav.diseases` into the
     * navbar — a correct URL with a machine-readable label, and nothing in the
     * suite failed.
     *
     * It survived a grep for `'diseases' =>` too, because a matching key already
     * existed further down the file in the `breadcrumb` block. Checking that a key
     * is *reachable* is the only thing that works; checking that the string is
     * *present in the file* is not.
     *
     * The pattern is deliberately loose — any part of any part-namespaced key
     * reaching the page is a bug, whatever the block it came from.
     */
    #[Test]
    public function no_translation_key_reaches_the_navbar(): void
    {
        foreach (['poultry', 'large-animals'] as $part) {
            foreach (['en', 'ar'] as $locale) {
                app()->setLocale($locale);

                $html = (string) $this->get(route("{$part}.home"))->assertOk()->getContent();

                $leaked = preg_match_all(
                    '/\b(?:large-animals|poultry)\.[a-z0-9_]+\.[a-z0-9_.]+/',
                    $html,
                    $matches,
                );

                $this->assertSame(
                    0,
                    $leaked,
                    "Unresolved translation key(s) on the {$part} navbar in {$locale}: "
                        .implode(', ', array_unique($matches[0])),
                );
            }
        }

        app()->setLocale(config('app.locale'));
    }

    /**
     * The drug catalogue existed on both parts but only the poultry hub carried a
     * card for it, so a visitor landing on `/large-animals` had no route to it
     * except the navbar.
     */
    #[Test]
    public function both_hubs_carry_the_drug_catalogue_card(): void
    {
        $this->get(route('poultry.home'))
            ->assertOk()
            ->assertSee(route('poultry.products.index'), escape: false);

        $this->get(route('large-animals.home'))
            ->assertOk()
            ->assertSee(route('large-animals.products.index'), escape: false)
            ->assertSee(__('large-animals.landing.services.products.title'));
    }

    // ------------------------------------------------------------------ helpers

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function poultryDisease(string $name, array $attributes = []): Disease
    {
        return $this->disease($name, Disease::DOMAIN_POULTRY, $attributes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function largeAnimalDisease(string $name, array $attributes = []): Disease
    {
        return $this->disease($name, Disease::DOMAIN_LARGE_ANIMAL, $attributes);
    }

    /**
     * Created through the model rather than the factory. `DiseaseFactory` draws
     * its name from a Faker `unique()` pool of thirty veterinary diseases, and
     * `diseases.name` is UNIQUE, so a test naming several diseases exhausts the
     * pool. Nothing about a disease here is randomised anyway.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function disease(string $name, string $domain, array $attributes = []): Disease
    {
        return Disease::query()->create(array_merge([
            'name' => $name,
            'name_ar' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'domain' => $domain,
            'is_active' => true,
            'is_zoonotic' => false,
            'is_internal' => false,
        ], $attributes));
    }

    /**
     * Carry one finding on a disease.
     *
     * `$type` is deliberately nullable rather than defaulted. The curated signs in
     * the real data carry NULL on both `ontology_type` and `category`, and that is
     * the case the counting rule exists for — a helper that always set a value
     * would quietly delete the test.
     *
     * `$canonical` is separate because `findings.canonical_name` is UNIQUE, so a
     * test attaching the same sign to many diseases has to disambiguate. Left
     * implicit, the query-budget test fails on a constraint that has nothing to do
     * with what it is measuring.
     */
    private function attach(Disease $disease, string $name, ?OntologyType $type, ?string $canonical = null): Finding
    {
        $canonical ??= Str::slug($name, '_');

        $finding = Finding::factory()->create([
            'canonical_name' => $canonical,
            'display_name' => $name,
            'ontology_type' => $type,
        ]);

        $sign = ClinicalSign::factory()->create([
            'finding_id' => $finding->id,
            'anatomical_structure_id' => null,
            'modifier_id' => null,
            'canonical_name' => $canonical.'_sign',
            'display_name' => $name,
        ]);

        $disease->clinicalSigns()->attach($sign);

        return $finding;
    }

    /**
     * The component's first page, straight off the service, so a count assertion
     * reads a number rather than parsing markup.
     *
     * @return array<string, mixed>
     */
    private function firstRow(string $name): array
    {
        $rows = Livewire::test('knowledge.disease-list', ['part' => 'poultry'])
            ->instance()
            ->diseases()
            ->getCollection();

        $row = $rows->firstWhere('name', $name);

        $this->assertNotNull($row, "The list did not return a row for [{$name}].");

        return $row->getAttributes();
    }
}
