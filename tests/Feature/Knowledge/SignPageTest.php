<?php

namespace Tests\Feature\Knowledge;

use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The clinical sign page, reached by clicking a sign in the global search.
 *
 * A sign used to link to the diagnosis tool, which requires a species and three
 * signs and so could not be submitted with the one sign it handed over; and then
 * briefly to the filter tool. It now has its own page.
 *
 * The load-bearing behaviour is the part boundary. `clinical_signs` has no domain
 * column, so a sign belongs to a part by being carried by one of its diseases —
 * which means the same sign can be legitimately present on both sides, and a
 * poultry-only sign on the large-animals page must be a 404 rather than a page
 * that renders outside its section.
 */
class SignPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_a_sign_with_the_diseases_that_carry_it(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $this->poultryDisease('Coccidiosis', [$sign]);
        $this->poultryDisease('Newcastle Disease', [$sign]);

        $this->get(route('poultry.signs.show', 'drooping-wings'))
            ->assertOk()
            ->assertSee('Drooping wings')
            ->assertSee('Coccidiosis')
            ->assertSee('Newcastle Disease');
    }

    public function test_it_shows_how_strongly_each_disease_leans_on_the_sign(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $this->poultryDisease('Coccidiosis', [$sign], pivot: ['weight' => 9, 'is_pathognomonic' => true]);
        $this->poultryDisease('Newcastle Disease', [$sign], pivot: ['weight' => 2]);

        $html = $this->get(route('poultry.signs.show', 'drooping-wings'))->assertOk()->getContent();

        $this->assertStringContainsString(__('poultry.sign.pathognomonic'), $html);
        $this->assertStringContainsString(__('poultry.sign.weight').': 9', $html);
        $this->assertStringContainsString(__('poultry.sign.weight').': 2', $html);
    }

    /**
     * The page and the diagnosis engine have to agree about which disease is the
     * strongest match, so the ordering is the pivot weight — not the name.
     */
    public function test_diseases_are_ordered_by_how_strongly_they_use_the_sign(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $this->poultryDisease('Aviary Something', [$sign], pivot: ['weight' => 1]);
        $this->poultryDisease('Zzz Condition', [$sign], pivot: ['weight' => 10]);

        $html = $this->get(route('poultry.signs.show', 'drooping-wings'))->assertOk()->getContent();

        // "Zzz Condition" sorts first alphabetically but weighs ten times as
        // heavily, so the weight has to win. Named to be the opposite of the
        // alphabetical order the page must not fall back to.
        $this->assertLessThan(
            strpos($html, 'Aviary Something'),
            strpos($html, 'Zzz Condition'),
            'The heaviest association should be listed first.',
        );
    }

    public function test_it_shows_the_anatomical_structure_and_body_system(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $bodySystem = BodySystem::factory()->create(['display_name' => 'Respiratory']);
        $structure = AnatomicalStructure::factory()->create([
            'display_name' => 'Keel',
            'body_system_id' => $bodySystem->id,
        ]);

        $sign->update(['anatomical_structure_id' => $structure->id]);

        $this->poultryDisease('Coccidiosis', [$sign]);

        $this->get(route('poultry.signs.show', 'drooping-wings'))
            ->assertOk()
            ->assertSee('Keel')
            ->assertSee('Respiratory');
    }

    /**
     * Both hand-offs onward live at the bottom of the page, which is why the
     * filter prefill had to stay: the sign page is now the place a reader
     * chooses which tool to use.
     */
    public function test_it_offers_both_tools_with_the_sign_carried(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $this->poultryDisease('Coccidiosis', [$sign]);

        $this->get(route('poultry.signs.show', 'drooping-wings'))
            ->assertOk()
            ->assertSee(route('poultry.filter', ['sign' => $sign->id]), escape: false)
            ->assertSee(route('poultry.diagnosis', ['sign' => $sign->id]), escape: false);
    }

    /**
     * The large-animals half is a thin sibling of the poultry half and must
     * render the same page from the same trait.
     */
    public function test_the_large_animals_half_renders_the_same_page(): void
    {
        $sign = $this->sign('lameness', 'Lameness', 'large-animals');

        $disease = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();
        $disease->clinicalSigns()->attach($sign->id, ['weight' => 8, 'is_required' => true]);

        $this->get(route('large-animals.signs.show', 'lameness'))
            ->assertOk()
            ->assertSee('Lameness')
            ->assertSee('Foot and Mouth Disease')
            ->assertSee(__('large-animals.sign.required'));
    }

    public function test_a_sign_reachable_from_both_domains_is_offered_on_both_pages(): void
    {
        $sign = $this->sign('nasal-discharge', 'Nasal discharge', ['poultry', 'large-animals']);

        $this->poultryDisease('Coccidiosis', [$sign]);

        $large = Disease::factory()->largeAnimals()->named('Anthrax')->create();
        $large->clinicalSigns()->attach($sign->id, ['weight' => 5]);

        $this->get(route('poultry.signs.show', 'nasal-discharge'))
            ->assertOk()
            ->assertSee('Coccidiosis')
            ->assertDontSee('Anthrax');

        $this->get(route('large-animals.signs.show', 'nasal-discharge'))
            ->assertOk()
            ->assertSee('Anthrax')
            ->assertDontSee('Coccidiosis');
    }

    public function test_a_sign_from_the_other_part_is_a_404(): void
    {
        $sign = $this->sign('poultry-only', 'Poultry-only sign', 'poultry');

        $this->poultryDisease('Coccidiosis', [$sign]);

        $this->get(route('large-animals.signs.show', 'poultry-only'))->assertNotFound();
    }

    public function test_an_unknown_sign_is_a_404(): void
    {
        $this->get(route('poultry.signs.show', 'no-such-sign'))->assertNotFound();
    }

    /**
     * An inactive disease must not make a sign look alive on this page. The
     * search layer already excludes them; this is the same rule one level down.
     */
    public function test_an_inactive_disease_is_not_listed(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $this->poultryDisease('Coccidiosis', [$sign]);
        $this->poultryDisease('Newcastle Disease', [$sign], ['is_active' => false]);

        $this->get(route('poultry.signs.show', 'drooping-wings'))
            ->assertOk()
            ->assertSee('Coccidiosis')
            ->assertDontSee('Newcastle Disease');
    }

    /**
     * The search result has to actually point here, or the page is unreachable.
     */
    public function test_a_sign_result_in_search_links_to_this_page(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $this->poultryDisease('Coccidiosis', [$sign]);

        $this->get(route('poultry.search', ['q' => 'Drooping wings']))
            ->assertOk()
            ->assertSee(route('poultry.signs.show', 'drooping-wings'), escape: false);
    }

    /**
     * The same, on the large-animals half.
     *
     * The route is declared once as `{part}.signs.show`, so this was wired from
     * the start — but only the poultry link was ever asserted, which is exactly
     * the gap where a per-part regression would have gone unnoticed.
     */
    public function test_a_sign_result_in_large_animals_search_links_to_this_page(): void
    {
        $sign = $this->sign('lameness', 'Lameness', 'large-animals');

        $disease = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();
        $disease->clinicalSigns()->attach($sign->id, ['weight' => 8]);

        $this->get(route('large-animals.search', ['q' => 'Lameness']))
            ->assertOk()
            ->assertSee(route('large-animals.signs.show', 'lameness'), escape: false)
            ->assertDontSee(route('poultry.signs.show', 'lameness'), escape: false);
    }

    /**
     * One concept, two records, one page listing the union of their diseases.
     *
     * Five names are recorded twice on the poultry side today. Resolving with
     * `first()` showed one record's diseases and silently dropped the other's.
     */
    public function test_two_records_of_one_sign_are_unioned_into_one_page(): void
    {
        [$first, $second] = $this->poultryDuplicateRecords('depression', 'Depression');

        $this->poultryDisease('Coccidiosis', [$first]);
        $this->poultryDisease('Newcastle Disease', [$second]);

        $this->get(route('poultry.signs.show', 'depression'))
            ->assertOk()
            ->assertSee('Coccidiosis')
            ->assertSee('Newcastle Disease');
    }

    /**
     * The heaviest association wins, and a marker recorded against either record
     * is shown — dropping it because the other record lacked it would
     * understate the sign.
     */
    public function test_merged_flags_keep_the_heaviest_weight_and_either_marker(): void
    {
        [$first, $second] = $this->poultryDuplicateRecords('diarrhea', 'Diarrhea');

        // One disease, linked to both records with different pivots. Two diseases
        // of the same name would collide on `diseases.slug`.
        $disease = $this->poultryDisease('Coccidiosis');

        $disease->clinicalSigns()->attach($first->id, ['weight' => 3]);
        $disease->clinicalSigns()->attach($second->id, ['weight' => 8, 'is_pathognomonic' => true]);

        $html = $this->get(route('poultry.signs.show', 'diarrhea'))->assertOk()->getContent();

        // One row, not two.
        $this->assertSame(1, substr_count($html, 'Coccidiosis</a>'), 'The disease should be listed once, merged.');

        $this->assertStringContainsString(__('poultry.sign.weight').': 8', $html);
        $this->assertStringNotContainsString(__('poultry.sign.weight').': 3', $html);
        $this->assertStringContainsString(__('poultry.sign.pathognomonic'), $html);
    }

    /**
     * The collapse has to happen in the results too, or the page explains a
     * duplicate the reader was never shown.
     */
    public function test_search_lists_one_row_per_concept_not_per_record(): void
    {
        [$first, $second] = $this->poultryDuplicateRecords('depression', 'Depression');

        $this->poultryDisease('Coccidiosis', [$first]);
        $this->poultryDisease('Newcastle Disease', [$second]);

        $html = $this->get(route('poultry.search', ['q' => 'Depression']))->assertOk()->getContent();

        // A result row renders its URL twice — once on the title, once on the arrow
        // button — so one concept is two occurrences, not one.
        $this->assertSame(
            2,
            substr_count($html, route('poultry.signs.show', 'depression')),
            'Two records of one sign must collapse to a single result.',
        );
    }

    /**
     * Two `clinical_signs` rows sharing a canonical name, both reachable from
     * poultry — the shape the five duplicated names have in the database.
     *
     * Built through the factory twice rather than via a single `create()`, so
     * nothing forces the rows apart: a canonical name is not unique.
     *
     * @return array<int, ClinicalSign>
     */
    private function poultryDuplicateRecords(string $canonicalName, string $displayName): array
    {
        return [
            ClinicalSign::factory()->create([
                'canonical_name' => $canonicalName,
                'display_name' => $displayName,
                'display_name_ar' => $displayName,
            ]),
            ClinicalSign::factory()->create([
                'canonical_name' => $canonicalName,
                'display_name' => $displayName,
                'display_name_ar' => $displayName,
            ]),
        ];
    }

    /**
     * A missing translation renders as the raw key, which looks like a working
     * page right up until a reader sees `sign.diseases_heading` on it.
     */
    public function test_every_translation_key_on_the_page_resolves(): void
    {
        $sign = $this->sign('drooping-wings', 'Drooping wings', 'poultry');

        $this->poultryDisease('Coccidiosis', [$sign]);

        foreach (['en', 'ar'] as $locale) {
            app()->setLocale($locale);

            $this->get(route('poultry.signs.show', 'drooping-wings'))
                ->assertOk()
                ->assertDontSee('sign.', escape: false);
        }
    }

    /**
     * Creates a sign and, per domain, one disease carrying it — enough for the
     * sign to belong to that part.
     *
     * Every disease here goes through `named()`. Passing `['name' => …]` to
     * `create()` would override the name but leave `slug` derived from the
     * factory's *random* pick, so a disease named in one place can collide with a
     * factory roll in another — the intermittent UNIQUE violation §12.1 warns
     * about, and it fails as a confusing database error rather than as a naming
     * mistake.
     */
    private function sign(string $canonicalName, string $displayName, string|array $domains): ClinicalSign
    {
        $sign = ClinicalSign::factory()->create([
            'canonical_name' => $canonicalName,
            'display_name' => $displayName,
            'display_name_ar' => $displayName,
        ]);

        foreach ((array) $domains as $domain) {
            Disease::factory()
                ->named($displayName.' '.ucfirst($domain))
                ->forDomain($domain === 'poultry'
                    ? Disease::DOMAIN_POULTRY
                    : Disease::DOMAIN_LARGE_ANIMAL)
                ->create()
                ->clinicalSigns()
                ->attach($sign->id, ['weight' => 5]);
        }

        return $sign;
    }

    /**
     * @param  array<int, ClinicalSign>  $signs
     * @param  array<string, mixed>  $attributes  disease columns
     * @param  array<string, mixed>  $pivot  disease_clinical_sign columns
     */
    private function poultryDisease(
        string $name,
        array $signs = [],
        array $attributes = [],
        array $pivot = [],
    ): Disease {
        $disease = Disease::factory()->poultry()->named($name)->create($attributes);

        foreach ($signs as $sign) {
            $disease->clinicalSigns()->attach($sign->id, $pivot ?: ['weight' => 5]);
        }

        return $disease;
    }
}
