<?php

namespace Tests\Feature\Poultry;

use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\Finding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The poultry body map, `/poultry/body-map`.
 *
 * Phase 8 was planned on a star note — "important_organs_affected is populated
 * for 152 diseases, only the illustration is missing". There is no such column,
 * `knowledge_payload` has no such key, and the curated path from a disease to an
 * organ reaches two of the 151 poultry diseases. These tests exist because the
 * page therefore renders two tiers of content with different levels of truth, and
 * the failure mode for that is invisible: a guessed match that reads exactly like
 * a curated one. So each tier is asserted separately, and the copy that
 * distinguishes them is asserted too.
 */
class PartBodyMapTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------- the tiers

    /**
     * A clinical sign carrying an anatomical structure is a curated link, and it
     * lands in the "mapped" tier.
     */
    #[Test]
    public function a_curated_clinical_sign_links_its_disease_to_the_organ(): void
    {
        $organ = $this->organ('Liver', 'liver');
        $disease = $this->poultryDisease('Duck Viral Hepatitis');
        $sign = $this->sign($organ, Finding::factory()->create([
            'canonical_name' => 'unrelated_label',
            'display_name' => 'Unrelated label',
            'ontology_type' => 'sign',
        ]));

        $disease->clinicalSigns()->attach($sign);

        // The tier has no heading of its own: it is headed by the sentence that
        // explains it, so that is what is asserted.
        $component = Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertSee($disease->name)
            ->assertSee(__('poultry.body_map.mapped_note'));

        $this->assertSame(
            [$disease->slug],
            $component->instance()->selectedOrganEntry()['mapped']->pluck('slug')->all(),
        );
    }

    /**
     * A lesion whose label names the organ reaches the disease that carries it —
     * by text, not by a link anybody made. This is the tier that makes the page
     * worth building at all, and it is also the tier that must never be presented
     * as curated.
     */
    #[Test]
    public function a_lesion_naming_the_organ_reaches_its_disease_by_name(): void
    {
        $organ = $this->organ('Liver', 'liver');
        $disease = $this->poultryDisease('Aflatoxicosis');

        // No `anatomical_structure_id` on the sign: that is what puts this in the
        // name-matched tier rather than the curated one.
        $sign = $this->sign(null, Finding::factory()->create([
            'canonical_name' => 'liver_lesions',
            'display_name' => 'Liver lesions',
            'ontology_type' => 'lesion',
        ]));

        $disease->clinicalSigns()->attach($sign);

        // The caution is the block's header, so seeing it is what proves the
        // guessed tier rendered — there is no separate name for it to assert.
        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertSee($disease->name)
            ->assertSee(__('poultry.body_map.derived_note'));

        $this->assertSame(
            [$disease->slug],
            Livewire::test('anatomy.body-map', ['part' => 'poultry'])
                ->call('selectOrgan', 'liver')
                ->instance()->selectedOrganEntry()['derived']->pluck('slug')->all(),
        );
    }

    /**
     * A clinical adjective is how veterinary labels actually name an organ, and
     * "Liver" has to reach "Hepatic necrosis". Without the vocabulary map the
     * organ reads as empty while the matching label sits in the same table.
     */
    #[Test]
    public function an_organ_reaches_a_label_that_names_it_only_in_the_adjective(): void
    {
        $this->organ('Liver', 'liver');
        $disease = $this->poultryDisease('Mycotoxicosis');
        $sign = $this->sign(null, Finding::factory()->create([
            'canonical_name' => 'hepatic_necrosis',
            'display_name' => 'Hepatic necrosis',
            'ontology_type' => 'lesion',
        ]));

        $disease->clinicalSigns()->attach($sign);

        $this->assertSame(
            [$disease->slug],
            Livewire::test('anatomy.body-map', ['part' => 'poultry'])
                ->call('selectOrgan', 'liver')
                ->instance()->selectedOrganEntry()['derived']->pluck('slug')->all(),
        );
    }

    /**
     * A disease is never counted twice on one organ: the curated link wins and the
     * name match does not repeat it.
     */
    #[Test]
    public function a_curated_disease_is_not_also_offered_as_a_name_match(): void
    {
        $organ = $this->organ('Liver', 'liver');
        $disease = $this->poultryDisease('Coccidiosis');

        // Two signs on one disease: one curated, one that only names the organ.
        // The curated one wins and the other must not repeat the disease under it.
        $disease->clinicalSigns()->attach($this->sign($organ, Finding::factory()->create([
            'canonical_name' => 'liver_granulomas',
            'display_name' => 'Liver granulomas',
            'ontology_type' => 'lesion',
        ])));

        $disease->clinicalSigns()->attach($this->sign(null, Finding::factory()->create([
            'canonical_name' => 'liver_lesions',
            'display_name' => 'Liver lesions',
            'ontology_type' => 'lesion',
        ])));

        $entry = Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->instance()->selectedOrganEntry();

        $this->assertSame([$disease->slug], $entry['mapped']->pluck('slug')->all());
        $this->assertSame([], $entry['derived']->pluck('slug')->all());
    }

    /**
     * The word-boundary guarantee. A bare `LIKE '%crop%'` would hand "Cropped
     * beak" to the crop, which is the kind of quiet wrong answer a clinical page
     * cannot afford.
     */
    #[Test]
    public function an_organ_does_not_claim_a_label_that_merely_contains_its_name(): void
    {
        $this->organ('Crop', 'crop');
        $disease = $this->poultryDisease('Beak trauma');
        $sign = $this->sign(null, Finding::factory()->create([
            'canonical_name' => 'cropped_beak',
            'display_name' => 'Cropped beak',
            'ontology_type' => 'lesion',
        ]));

        $disease->clinicalSigns()->attach($sign);

        $this->assertSame(
            [],
            Livewire::test('anatomy.body-map', ['part' => 'poultry'])
                ->call('selectOrgan', 'crop')
                ->instance()->selectedOrganEntry()['derived']->pluck('slug')->all(),
            '"Cropped beak" is not about the crop.',
        );
    }

    // ------------------------------------------------------ why it was matched

    /**
     * A name match with no stated reason is a guess the reader cannot check, and a
     * guess that looks like a curated link is the one failure this page cannot
     * afford. The service therefore records which label put each disease on the
     * organ, and the panel prints it under the disease.
     */
    #[Test]
    public function a_name_matched_disease_carries_the_label_that_matched_it(): void
    {
        $this->organ('Liver', 'liver');
        $disease = $this->poultryDisease('Mycotoxicosis');
        $finding = Finding::factory()->create([
            'canonical_name' => 'hepatic_necrosis',
            'display_name' => 'Hepatic necrosis',
            'ontology_type' => 'lesion',
        ]);

        $disease->clinicalSigns()->attach($this->sign(null, $finding));

        $reasons = $this->entry('liver')['derivedReasons'][(string) $disease->getKey()];

        $this->assertSame(
            ['Hepatic necrosis'],
            $reasons->pluck('display_name')->all(),
        );

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertSee(__('poultry.body_map.matched_because'))
            ->assertSee('Hepatic necrosis');
    }

    /**
     * The reason trail is for the guessed tier only. A curated disease carries a
     * link somebody made, and the labels that happen to name the organ are not that
     * link — printing them would dress a curated row up as an inference, or worse,
     * print nothing at all and leave the reason line half-rendered.
     */
    #[Test]
    public function a_curated_disease_is_given_no_match_reason(): void
    {
        $organ = $this->organ('Liver', 'liver');
        $disease = $this->poultryDisease('Coccidiosis');

        $disease->clinicalSigns()->attach($this->sign($organ, Finding::factory()->create([
            'canonical_name' => 'unrelated',
            'display_name' => 'Unrelated label',
            'ontology_type' => 'sign',
        ])));

        // A second label that does name the liver, so the only thing standing
        // between this disease and a reason line is that it was curated.
        $disease->clinicalSigns()->attach($this->sign(null, Finding::factory()->create([
            'canonical_name' => 'liver_granulomas',
            'display_name' => 'Liver granulomas',
            'ontology_type' => 'lesion',
        ])));

        $entry = $this->entry('liver');

        $this->assertSame([$disease->slug], $entry['mapped']->pluck('slug')->all());
        $this->assertArrayNotHasKey((string) $disease->getKey(), $entry['derivedReasons']);
    }

    // ------------------------------------------------------- capped panel lists

    /**
     * The panel now prints a zoonotic badge on every disease row, which reads off
     * `diseaseClassification`. Left lazy that is one query per disease, and the
     * organ that triggers it — liver, at 38 — is the busiest the page has.
     *
     * Asserted as a budget rather than a count for the same reason the atlas does
     * it: a query added on purpose should be visible here rather than discovered
     * in production.
     */
    #[Test]
    public function the_panel_stays_within_its_query_budget(): void
    {
        $this->organ('Liver', 'liver');

        $this->attachLiverDiseases(6);

        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        // The listener sees every statement on the connection, seeding included, so
        // each render is measured as a delta rather than a running total.
        $before = $queries;
        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertOk();
        $atSix = $queries - $before;

        $this->attachLiverDiseases(6, from: 7);

        $before = $queries;
        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertOk();
        $atTwelve = $queries - $before;

        // Twice the diseases must not cost twice the queries. This is the assertion
        // that actually proves there is no N+1; the ceiling below only stops one
        // being added later.
        $this->assertLessThanOrEqual(
            $atSix,
            $atTwelve,
            "Six diseases took {$atSix} queries; twelve took {$atTwelve}. The count must not grow with the disease count.",
        );

        // 14 in practice: the seven the service issues (structures, body systems,
        // the curated links, the label vocabulary, the diseases, their
        // classifications and the findings) plus Livewire's own. Lazy-loading the
        // classification instead would add one per row — twelve here, thirty-eight
        // on a real liver — which is what this ceiling is here to stop.
        $this->assertLessThanOrEqual(
            18,
            $atTwelve,
            "The organ panel issued {$atTwelve} queries. Diseases and their classifications load eagerly.",
        );
    }

    /**
     * "Liver" reaches 38 diseases and "Trachea" 17. The panel shows a dozen and
     * offers the rest, because a card that renders every row pushes the clinical
     * signs and lesions off the bottom of it.
     */
    #[Test]
    public function a_long_disease_list_is_capped_until_it_is_expanded(): void
    {
        $this->organ('Liver', 'liver');

        foreach (range(1, 14) as $index) {
            $disease = $this->poultryDisease(sprintf('Liver Disease %02d', $index));
            $disease->clinicalSigns()->attach($this->sign(null, Finding::factory()->create([
                'canonical_name' => "liver_label_{$index}",
                'display_name' => 'Liver label '.$index,
                'ontology_type' => 'lesion',
            ])));
        }

        $capped = Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver');

        $capped->assertSee('Liver Disease 01')
            ->assertSee('Liver Disease 12')
            ->assertDontSee('Liver Disease 13')
            ->assertSee(__('poultry.body_map.show_all', ['count' => 14]));

        $capped->call('toggleSection', 'derived')
            ->assertSet('expanded', ['derived' => true])
            ->assertSee('Liver Disease 13')
            ->assertSee('Liver Disease 14')
            ->assertSee(__('poultry.body_map.show_less'));
    }

    /**
     * Both tiers sit under one heading, and neither keeps a name of its own.
     *
     * "Mapped" and "matched by name" were near-synonyms, so two different kinds
     * of claim read as one category; and neither said what had actually happened.
     * The tiers are still separated — solid against dashed, and a curated link
     * never sits under the text-match caution — but each block is now headed by
     * the sentence that explains it, and the reader is asked to learn no
     * vocabulary to see the difference.
     *
     * @see PartBodyMapTest::a_curated_clinical_sign_links_its_disease_to_the_organ()
     */
    #[Test]
    public function both_tiers_share_one_diseases_heading(): void
    {
        $liver = $this->organ('Liver', 'liver');

        // One curated link, so both blocks render and the heading is contested.
        $curated = $this->poultryDisease('Coccidiosis');
        $curated->clinicalSigns()->attach($this->sign($liver, Finding::factory()->create([
            'canonical_name' => 'unrelated_label',
            'display_name' => 'Unrelated label',
            'ontology_type' => 'sign',
        ])));

        $label = $this->sign(null, Finding::factory()->create([
            'canonical_name' => 'liver_lesions',
            'display_name' => 'Liver lesions',
            'ontology_type' => 'lesion',
        ]));

        foreach (range(1, 3) as $index) {
            $disease = $this->poultryDisease(sprintf('Liver Disease %02d', $index));
            $disease->clinicalSigns()->attach($label);
        }

        $panel = $this->panelOf(
            Livewire::test('anatomy.body-map', ['part' => 'poultry'])->call('selectOrgan', 'liver'),
        );

        // One heading, carrying both tiers' counts. Matched on the markup rather
        // than the bare word, because the heading also has to be the one holding
        // the total — "one occurrence of the string Diseases" would pass just as
        // happily on two headings that each counted only their own tier.
        preg_match_all(
            '/<h3[^>]*>\s*'.preg_quote(__('poultry.body_map.diseases'), '/').'\s*<span[^>]*>(\d+)<\/span>/',
            $panel,
            $heading,
        );

        $this->assertCount(
            1,
            $heading[0],
            'Two tiers must not produce two "Diseases" headings.',
        );
        $this->assertSame(
            '4',
            $heading[1][0],
            'The single heading must count the curated tier and the matched tier together.',
        );

        // Both blocks present, each headed by the sentence that explains it.
        $this->assertStringContainsString(__('poultry.body_map.mapped_note'), $panel);
        $this->assertStringContainsString(__('poultry.body_map.derived_note'), $panel);

        // And neither block carries a name again.
        foreach (['Mapped', 'Matched by name'] as $jargon) {
            $this->assertStringNotContainsString($jargon, $panel);
        }
    }

    /**
     * The other half of the cap. An organ with fewer diseases than the cap shows
     * all of them, so it must offer nothing to expand — a "Show all 5" over a list
     * of five is a button that does nothing, and it is what the panel rendered
     * until both disease tiers were gated on the same test the findings already
     * used.
     *
     * Both tiers are populated on purpose: they were the two call sites that
     * ignored the test, and a liver carrying two curated links and five matches is
     * the smallest case that catches either one.
     */
    #[Test]
    public function a_list_under_the_cap_offers_no_expand_control(): void
    {
        $liver = $this->organ('Liver', 'liver');

        // Two curated links, on a label that names nothing — so they stay curated
        // and never also turn up in the matched tier.
        foreach (range(1, 2) as $index) {
            $disease = $this->poultryDisease(sprintf('Curated Disease %02d', $index));
            $disease->clinicalSigns()->attach($this->sign($liver, Finding::factory()->create([
                'canonical_name' => "unrelated_label_{$index}",
                'display_name' => 'Unrelated label '.$index,
                'ontology_type' => 'sign',
            ])));
        }

        // Five name matches, against a cap of twelve.
        $label = $this->sign(null, Finding::firstOrCreate(
            ['canonical_name' => 'liver_lesions'],
            ['display_name' => 'Liver lesions', 'ontology_type' => 'lesion'],
        ));

        foreach (range(1, 5) as $index) {
            $disease = $this->poultryDisease(sprintf('Liver Disease %02d', $index));
            $disease->clinicalSigns()->attach($label);
        }

        $entry = $this->entry('liver');

        $this->assertCount(2, $entry['mapped']);
        $this->assertCount(5, $entry['derived']);

        $panel = $this->panelOf(
            Livewire::test('anatomy.body-map', ['part' => 'poultry'])->call('selectOrgan', 'liver'),
        );

        // Seven rows, all visible, so the panel carries no expand control at all.
        $this->assertStringNotContainsString('toggleSection', $panel);
        $this->assertStringNotContainsString(__('poultry.body_map.show_all', ['count' => 2]), $panel);
        $this->assertStringNotContainsString(__('poultry.body_map.show_all', ['count' => 5]), $panel);
        $this->assertStringNotContainsString(__('poultry.body_map.show_less'), $panel);
    }

    /**
     * Opening one organ's list and then selecting another must not carry the
     * expansion across — arriving at the trachea to find the liver's rows already
     * open is the sort of state leak that makes a panel feel like it is lying.
     */
    #[Test]
    public function changing_organ_folds_the_previous_sections_back(): void
    {
        $this->organ('Liver', 'liver');
        $this->organ('Trachea', 'trachea', null, $this->system('respiratory', 'Respiratory System'));

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->call('toggleSection', 'derived')
            ->assertSet('expanded', ['derived' => true])
            ->call('selectOrgan', 'trachea')
            ->assertSet('expanded', []);
    }

    /**
     * The section name arrives from the markup and a Livewire payload is the
     * client's to edit, so an unknown key opens nothing rather than becoming a
     * free-text flag on the component.
     */
    #[Test]
    public function an_unknown_section_cannot_be_toggled(): void
    {
        $this->organ('Liver', 'liver');

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->call('toggleSection', 'not-a-section')
            ->assertSet('expanded', []);
    }

    /**
     * A zoonotic disease on the map is a biosafety signal, and it is only useful if
     * it arrives before the reader opens the disease. It rides on the row rather
     * than on the disease page, which means the classification has to be loaded
     * with the list — lazily it is one query per row on a 38-disease organ.
     */
    #[Test]
    public function a_zoonotic_disease_is_flagged_on_its_row(): void
    {
        $this->organ('Liver', 'liver');

        $zoonotic = $this->poultryDisease('Salmonellosis');
        DiseaseClassification::factory()->create([
            'disease_id' => $zoonotic->getKey(),
            'zoonotic' => true,
        ]);

        $notZoonotic = $this->poultryDisease('Aflatoxicosis');
        DiseaseClassification::factory()->create([
            'disease_id' => $notZoonotic->getKey(),
            'zoonotic' => false,
        ]);

        // One label shared by both diseases: both are on the liver for the same
        // reason, which is the point of the row being about the match and not the
        // disease.
        $label = $this->sign(null, Finding::factory()->create([
            'canonical_name' => 'liver_lesions',
            'display_name' => 'Liver lesions',
            'ontology_type' => 'lesion',
        ]));

        $zoonotic->clinicalSigns()->attach($label);
        $notZoonotic->clinicalSigns()->attach($label);

        $html = $this->panelOf(
            Livewire::test('anatomy.body-map', ['part' => 'poultry'])->call('selectOrgan', 'liver'),
        );

        // Both diseases reach the liver, so both are listed...
        $this->assertStringContainsString($zoonotic->name, $html);
        $this->assertStringContainsString($notZoonotic->name, $html);

        // ...and only the zoonotic one is flagged. A badge on every row is
        // decoration, not a signal.
        $this->assertSame(
            1,
            substr_count($html, __('poultry.body_map.zoonotic')),
            'Exactly one of the two diseases is zoonotic, so exactly one row carries the badge.',
        );
    }

    // ------------------------------------------------------------ part scoping
    /**
     * The anatomy vocabulary is one shared table and a bird has no rumen. Without
     * `anatomical_structures.domain` the chicken diagram offers teats.
     */
    #[Test]
    public function the_map_never_offers_a_large_animal_only_organ(): void
    {
        $this->organ('Rumen', 'rumen', 'large_animal');
        $this->organ('Teats', 'teats', 'large_animal');
        $this->organ('Liver', 'liver');

        $html = $this->get('/poultry/body-map?view=list')->assertOk()->getContent();

        $this->assertStringNotContainsString('Rumen', $html);
        $this->assertStringNotContainsString('Teats', $html);
        $this->assertStringContainsString('Liver', $html);
    }

    #[Test]
    public function a_large_animal_disease_does_not_reach_the_poultry_map(): void
    {
        $this->organ('Liver', 'liver');
        $disease = Disease::factory()->create([
            'name' => 'Foot rot',
            'domain' => 'large_animal',
        ]);
        $sign = $this->sign(null, Finding::factory()->create([
            'canonical_name' => 'liver_lesions',
            'display_name' => 'Liver lesions',
            'ontology_type' => 'lesion',
        ]));

        $disease->clinicalSigns()->attach($sign);

        $this->assertSame([], $this->entry('liver')['derived']->pluck('slug')->all());
    }

    /**
     * A body system with no structures is kept in the table but cannot be drawn,
     * so it is not offered. Otherwise the map carries twelve regions that can only
     * lead to an empty panel.
     */
    #[Test]
    public function a_body_system_with_no_structures_is_not_offered(): void
    {
        BodySystem::factory()->create(['canonical_name' => 'otic', 'display_name' => 'Otic']);
        $this->organ('Liver', 'liver');

        $html = $this->get('/poultry/body-map?view=list')->assertOk()->getContent();

        $this->assertStringNotContainsString('Otic', $html);
    }

    // ------------------------------------------------------------- interaction

    #[Test]
    public function selecting_a_system_lists_its_organs(): void
    {
        $digestive = $this->system('digestive', 'Digestive System');
        $respiratory = $this->system('respiratory', 'Respiratory System');
        $this->organ('Liver', 'liver', null, $digestive);
        $this->organ('Lungs', 'lungs', null, $respiratory);

        $component = Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectSystem', 'respiratory')
            ->assertSee('Respiratory System')
            ->assertSee('Lungs');

        // The panel is what is scoped to the selected system, so the panel is what
        // has to be asserted. The diagram names every organ it draws, so a
        // page-wide assertion can no longer tell "the panel is showing the right
        // system" from "the bird happens to mention the organ" — it was passing
        // before only because the drawing used no organ names to begin with.
        $this->assertStringNotContainsString('Liver', $this->panelOf($component));
    }

    /**
     * The systems under the bird are a toggle group, not a list of links: clicking
     * the one you are already on puts the map back to rest. Nothing carried that
     * fact to a screen reader — the chips had no state attribute at all, while the
     * view-mode switch directly above them did — so it is asserted here rather
     * than left to be lost in a restyle.
     */
    #[Test]
    public function the_system_chips_report_which_one_is_selected(): void
    {
        $digestive = $this->system('digestive', 'Digestive System');
        $respiratory = $this->system('respiratory', 'Respiratory System');
        $this->organ('Liver', 'liver', null, $digestive);
        $this->organ('Lungs', 'lungs', null, $respiratory);

        // Scoped to the buttons that call `selectSystem`. The view-mode switch
        // carries `aria-pressed` too, so a page-wide match counts eight of them
        // and proves nothing about the chips.
        $pressed = function (Testable $component): array {
            preg_match_all('/<button[^>]*selectSystem\([^>]*aria-pressed="(true|false)"/', $component->html(), $matches);

            return $matches[1];
        };

        $component = Livewire::test('anatomy.body-map', ['part' => 'poultry']);

        // Both chips start unpressed.
        $this->assertSame(['false', 'false'], $pressed($component));

        // Selecting one presses it and only it.
        $component->call('selectSystem', 'respiratory');

        $this->assertSame(['false', 'true'], $pressed($component));

        // Clicking the selected one again puts the map back to rest, so the state
        // has to be able to return to unpressed rather than only ever turning on.
        $component->call('selectSystem', 'respiratory');

        $this->assertSame(['false', 'false'], $pressed($component));
    }

    /**
     * Each chip swaps its chevron for a spinner while its own request is in
     * flight, so the swap is targeted by the system's name rather than by the bare
     * method name. All twelve chips call `selectSystem`; a bare target would spin
     * all of them at once, which tells the reader nothing about which one they
     * pressed.
     */
    #[Test]
    public function each_system_chip_targets_only_its_own_request(): void
    {
        $digestive = $this->system('digestive', 'Digestive System');
        $respiratory = $this->system('respiratory', 'Respiratory System');
        $this->organ('Liver', 'liver', null, $digestive);
        $this->organ('Lungs', 'lungs', null, $respiratory);

        $html = Livewire::test('anatomy.body-map', ['part' => 'poultry'])->html();

        // Each chip targets itself, by name. Compared against the escaped form,
        // because that is what the attribute holds in the raw HTML — the browser
        // decodes it back to `selectSystem('digestive')` before Livewire reads it.
        foreach (['digestive', 'respiratory'] as $system) {
            $this->assertStringContainsString(
                'wire:target="'.e("selectSystem('{$system}')").'"',
                $html,
            );
        }

        // No chip targets the bare method, which is what would spin all of them.
        $this->assertStringNotContainsString(
            'wire:target="selectSystem"',
            $html,
        );

        // Every chip offers both states: a chevron at rest, a spinner in flight.
        // Counted as the buttons that are both a `selectSystem` call and a toggle,
        // because the panel's own body-system list calls `selectSystem` too and has
        // no chevron to swap.
        preg_match_all('/wire:click="selectSystem[^"]*"[^>]*aria-pressed="/', $html, $chips);

        $this->assertCount(2, $chips[0]);
        $this->assertSame(2, substr_count($html, 'wire:loading.remove'));

        // One spinner per chip. Scoped to `selectSystem`, because a page-wide
        // `wire:loading ` count credited these chips for the organ markers'
        // spinners too — so it passed right up until a second control on the page
        // grew one.
        $this->assertSame(
            count($chips[0]),
            substr_count($html, 'wire:loading wire:target="selectSystem'),
            'Every chip needs a spinner of its own.',
        );

        // And each chip locks while its request runs, so a double-click cannot
        // toggle the selection back mid-flight. Asserted within the chip buttons:
        // the markers and the panel's own system list carry `disabled` too.
        foreach ($chips[0] as $chip) {
            $this->assertStringContainsString('wire:loading.attr="disabled"', $chip);
        }
    }

    #[Test]
    public function the_url_carries_the_selection(): void
    {
        $this->organ('Liver', 'liver');

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertSet('selectedOrgan', 'liver')
            ->assertSet('selectedSystem', 'digestive');

        $this->get('/poultry/body-map?organ=liver')
            ->assertOk()
            ->assertSee('Liver');
    }

    /**
     * An organ with nothing recorded against it says so. Inventing a list of
     * diseases for it would be the one thing this page must never do.
     */
    #[Test]
    public function an_organ_with_nothing_recorded_says_so(): void
    {
        $this->organ('Thyroid gland', 'thyroid_gland');

        // Asserting the whole block is absent rather than a heading that no
        // longer exists: the caution text appearing at all would mean a tier was
        // rendered, which is the failure this is here to catch.
        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'thyroid_gland')
            ->assertSee(__('poultry.body_map.organ_empty'))
            ->assertDontSee(__('poultry.body_map.derived_note'))
            ->assertDontSee(__('poultry.body_map.mapped_note'));
    }

    /**
     * Clicking the system you are already on puts the map back to rest.
     */
    #[Test]
    public function selecting_the_current_system_again_clears_the_selection(): void
    {
        $this->organ('Liver', 'liver');

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectSystem', 'digestive')
            ->assertSet('selectedSystem', 'digestive')
            ->call('selectSystem', 'digestive')
            ->assertSet('selectedSystem', '')
            ->assertSet('selectedOrgan', '');
    }

    #[Test]
    public function an_unknown_system_or_organ_cannot_be_selected(): void
    {
        $this->organ('Liver', 'liver');

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectSystem', 'not-a-system')
            ->assertSet('selectedSystem', '')
            ->call('selectOrgan', 'not-an-organ')
            ->assertSet('selectedOrgan', '');
    }

    // --------------------------------------------------------- accessibility

    /**
     * The diagram is one of two views, not the only view: the list carries every
     * system and every organ as real text.
     */
    #[Test]
    public function the_list_view_carries_every_system_and_organ_as_text(): void
    {
        $this->organ('Liver', 'liver');
        $this->organ('Air Sacs', 'air_sacs', null, $this->system('respiratory', 'Respiratory System'));

        $this->get('/poultry/body-map?view=list')
            ->assertOk()
            ->assertSee('Digestive System')
            ->assertSee('Respiratory System')
            ->assertSee('Liver')
            ->assertSee('Air Sacs');
    }

    /**
     * The markers are drawn, not generated, and each one is keyed by the same
     * canonical name the structures table uses. A key that stops matching — an
     * organ renamed, or a typo introduced into the geometry — does not error: the
     * marker simply never renders and the bird quietly loses the organ. Nothing
     * else on the page notices, which is why this is asserted against the full
     * set rather than one marker at a time.
     */
    #[Test]
    public function every_organ_drawn_on_the_bird_gets_exactly_one_marker(): void
    {
        $this->createLabelledOrgans();

        $html = $this->get('/poultry/body-map')->assertOk()->getContent();

        preg_match_all('/wire:key="organ-([a-z_]+)"/', $html, $matches);

        $this->assertSame(
            collect(array_keys($this->labelledOrgans()))->sort()->values()->all(),
            collect($matches[1])->unique()->sort()->values()->all(),
        );
    }

    /**
     * A marker carried both a literal `class="…"` and an `@class([…])`, which puts
     * two `class` attributes on one element. HTML forbids that and the browser keeps
     * the first and discards the second, so every conditional class was dropped and
     * the markers rendered as 6px white-bordered transparent squares: no size, no
     * colour, no ping, and a selection that never looked selected.
     *
     * The assertion above could not have caught it. It counts `wire:key`, which was
     * never affected, so the bird kept drawing all nineteen organs correctly while
     * every one of them looked broken. This checks the tag itself.
     */
    #[Test]
    public function a_marker_carries_exactly_one_class_attribute(): void
    {
        $this->organ('Liver', 'liver');

        $html = $this->get('/poultry/body-map')->assertOk()->getContent();

        preg_match('/<button\b[^>]*wire:key="organ-liver"[^>]*>/', $html, $tag);
        $this->assertNotEmpty($tag, 'The liver marker did not render at all.');

        $this->assertSame(
            1,
            substr_count($tag[0], 'class='),
            'A tag cannot carry two class attributes; the browser silently drops the second, and with it every conditional class.',
        );

        // The classes that were being discarded, and the reason the markers looked
        // like a white dot on the organ rather than a coloured marker.
        foreach (['h-7', 'w-7', 'rounded-full', 'bg-danger', 'after:animate-ping', 'hover:scale-110'] as $class) {
            $this->assertStringContainsString(
                $class,
                $tag[0],
                "The marker lost `{$class}`, so it was never sized, coloured or animated.",
            );
        }
    }

    /**
     * The markers carried an invisible hit-area expansion — `before:-inset-5`,
     * and `-inset-7` on large screens — which is +20/+28px on every side of a
     * 36px dot. That made each marker a 76-92px touch target on a bird that
     * carries nineteen of them, and four organs became unreachable: the eye sat
     * under the beak, the oesophagus under the trachea, the oviduct under the
     * cloaca and the intestines under the caeca.
     *
     * Asserted on the markup rather than on geometry. PHPUnit cannot measure
     * which marker wins a hit test, and a browser check is not something this
     * suite can run — but the expansion is a single class list, and its absence
     * is what keeps the organs reachable. Bigger dots are not the answer: 44px
     * dots re-cover the trachea and the caeca.
     */
    #[Test]
    public function a_marker_does_not_expand_its_hit_area_over_its_neighbours(): void
    {
        $this->organ('Liver', 'liver');

        $html = $this->get('/poultry/body-map')->assertOk()->getContent();

        preg_match('/<button\b[^>]*wire:key="organ-liver"[^>]*>/', $html, $tag);
        $this->assertNotEmpty($tag, 'The liver marker did not render at all.');

        foreach (['before:-inset-', 'before:absolute', 'before:content-'] as $expansion) {
            $this->assertStringNotContainsString(
                $expansion,
                $tag[0],
                'An expanded hit area makes this marker cover the organs nearest it, and the covered one cannot be clicked at all.',
            );
        }
    }

    /**
     * Every marker shows a spinner while its own request is in flight, and only
     * its own. All nineteen call `selectOrgan`, so the target has to carry the
     * organ's name — a bare method target would put nineteen spinners on screen
     * for one click and tell the reader nothing about what they pressed.
     */
    #[Test]
    public function a_marker_spins_only_for_its_own_request(): void
    {
        $this->organ('Liver', 'liver');
        $this->organ('Lungs', 'lungs', null, $this->system('respiratory', 'Respiratory System'));

        $html = $this->get('/poultry/body-map')->assertOk()->getContent();

        // Compared against the literal form. The marker writes this attribute out
        // verbatim, so the quotes are raw — unlike the system chips, which build
        // the string in PHP and print it through `{{ }}`, which escapes them.
        foreach (['liver', 'lungs'] as $organ) {
            $this->assertStringContainsString(
                "wire:target=\"selectOrgan('{$organ}')\"",
                $html,
            );
        }

        $this->assertStringNotContainsString(
            'wire:target="selectOrgan"',
            $html,
            'A bare target would spin every marker on the bird at once.',
        );

        // Each marker offers a spinner and locks while its request runs, so a
        // double-click cannot queue a second one behind the first. Counted within
        // the marker tags rather than page-wide: the system chips under the bird
        // carry a `disabled` attribute too, and a page-wide count would credit
        // their four to the markers' two.
        preg_match_all('/<button\b[^>]*wire:key="organ-[a-z_]+"[^>]*>/', $html, $tags);
        $markers = $tags[0];

        $this->assertGreaterThan(0, count($markers));

        foreach ($markers as $marker) {
            $this->assertStringContainsString(
                'wire:loading.attr="disabled"',
                $marker,
                'A marker that stays live mid-flight can be double-clicked into a second request.',
            );
        }

        // One spinner per marker. `selectOrgan` in this count keeps the chips out:
        // theirs target `selectSystem`.
        $this->assertSame(
            count($markers),
            substr_count($html, 'wire:loading wire:target="selectOrgan'),
        );

        // The centring must live on an inner element, not on the `wire:loading`
        // one. Livewire writes `style="display: inline-block"` onto a
        // `wire:loading` element while its request is in flight, and an inline
        // style beats every class on it — so flex alignment placed there is inert
        // and the ring lands in the corner of the marker, off centre. Asserting
        // the mechanism rather than the symptom, because "looks slightly off" is
        // exactly the kind of regression that gets read as a taste change.
        preg_match_all('/<span wire:loading wire:target="selectOrgan[^"]*"[^>]*>/', $html, $loadingSpans);

        $this->assertNotEmpty($loadingSpans[0]);

        foreach ($loadingSpans[0] as $span) {
            foreach (['items-center', 'justify-center', 'justify-items', 'place-items'] as $inert) {
                $this->assertStringNotContainsString(
                    $inert,
                    $span,
                    "Livewire overrides this element's display during the request, so `{$inert}` on it does nothing.",
                );
            }
        }

        $this->assertSame(
            count($markers),
            substr_count($html, 'class="flex h-full w-full items-center justify-center rounded-full text-white"'),
            'Each spinner needs an inner flex wrapper to be centred by.',
        );
    }

    /**
     * The geometry is authored by hand, so it is not part-scoped the way the query
     * is. A marker whose organ belongs to another part has to be withheld rather
     * than drawn: otherwise the diagram is the one place on the page that will
     * happily point a poultry bird at a teat.
     */
    #[Test]
    public function an_organ_the_part_excludes_has_no_marker(): void
    {
        $this->createLabelledOrgans(except: ['eye']);

        $this->organ('Eye', 'eye', 'large_animal', $this->system('ocular', self::SYSTEM_NAMES['ocular']));

        $html = $this->get('/poultry/body-map')->assertOk()->getContent();

        $this->assertStringNotContainsString('wire:key="organ-eye"', $html);
        $this->assertStringContainsString('wire:key="organ-liver"', $html);
    }

    #[Test]
    public function the_diagram_markers_are_reachable_by_keyboard(): void
    {
        $this->organ('Liver', 'liver');

        $html = $this->get('/poultry/body-map')->assertOk()->getContent();

        $this->assertStringContainsString('role="button"', $html);
        $this->assertStringContainsString('tabindex="0"', $html);
        $this->assertStringContainsString('wire:keydown.enter', $html);
        $this->assertStringContainsString('wire:keydown.space', $html);
        $this->assertStringContainsString('aria-label', $html);
    }

    #[Test]
    public function it_renders_in_arabic(): void
    {
        $this->organ('Liver', 'liver', displayNameAr: 'الكبد');

        app()->setLocale('ar');

        $this->get(route('poultry.body-map', ['organ' => 'liver']))
            ->assertOk()
            ->assertSee('الكبد')
            ->assertSee(__('poultry.body_map.heading', [], 'ar'), false)
            ->assertDontSee(__('poultry.body_map.select_system', [], 'en'), false);
    }

    // ------------------------------------------------------------------ wiring

    #[Test]
    public function the_map_is_linked_from_the_header_and_the_landing_hub(): void
    {
        $url = route('poultry.body-map');

        $this->get('/poultry')->assertOk()->assertSee($url, false);
        $this->get('/poultry/filter')->assertOk()->assertSee($url, false);
    }

    /**
     * Phase 9 has landed. This asserted a 404 while the atlas was unbuilt, so that
     * adding it would be a deliberate change rather than a stray route; the atlas
     * page itself is covered by `PoultryLesionAtlasTest`, and this only pins that
     * the two anatomy pages coexist on distinct routes.
     */
    #[Test]
    public function the_lesion_atlas_is_a_separate_page_from_the_body_map(): void
    {
        $this->get('/poultry/atlas')->assertOk();
        $this->get('/poultry/body-map')->assertOk();
    }

    // ----------------------------------------------------------------- helpers

    /**
     * Display names for the body systems the illustration reaches into, so a
     * system created here is not called "Digestive System" by accident.
     *
     * @var array<string, string>
     */
    private const SYSTEM_NAMES = [
        'cardiovascular' => 'Cardiovascular System',
        'digestive' => 'Digestive System',
        'immune' => 'Immune System',
        'ocular' => 'Ocular System',
        'reproductive' => 'Reproductive System',
        'respiratory' => 'Respiratory System',
        'urinary' => 'Urinary System',
    ];

    /**
     * The organs the supplied illustration labels: canonical name => display name
     * and the body system it sits in.
     *
     * This is the diagram's geometry seen from the other side. The view draws a
     * marker per entry and this seeds a structure per entry, so the two have to
     * agree — which is the whole point of asserting the full set above.
     *
     * "Small Intestine" is the artwork's label for `intestines`: the illustration
     * splits large from small and the table keeps one row. The artwork's other
     * seventeen labels are the structure's own name.
     *
     * @return array<string, array{string, string}>
     */
    private function labelledOrgans(): array
    {
        return [
            'eye' => ['Eye', 'ocular'],
            'beak' => ['Beak', 'digestive'],
            'esophagus' => ['Esophagus', 'digestive'],
            'trachea' => ['Trachea', 'respiratory'],
            'lungs' => ['Lungs', 'respiratory'],
            'crop' => ['Crop', 'digestive'],
            'heart' => ['Heart', 'cardiovascular'],
            'proventriculus' => ['Proventriculus', 'digestive'],
            'gizzard' => ['Gizzard', 'digestive'],
            'liver' => ['Liver', 'digestive'],
            'spleen' => ['Spleen', 'immune'],
            'pancreas' => ['Pancreas', 'digestive'],
            'ovary' => ['Ovary', 'reproductive'],
            'kidneys' => ['Kidneys', 'urinary'],
            'oviduct' => ['Oviduct', 'reproductive'],
            'cloaca' => ['Cloaca', 'digestive'],
            'large_intestine' => ['Large Intestine', 'digestive'],
            'intestines' => ['Intestines', 'digestive'],
            'ceca' => ['Ceca', 'digestive'],
        ];
    }

    /**
     * Give the diagram something to draw against.
     *
     * @param  array<int, string>  $except  Canonical names to leave uncreated, so
     *                                      a test can create one of them itself.
     */
    private function createLabelledOrgans(array $except = []): void
    {
        foreach ($this->labelledOrgans() as $canonical => [$display, $system]) {
            if (in_array($canonical, $except, true)) {
                continue;
            }

            $this->organ($display, $canonical, null, $this->system($system, self::SYSTEM_NAMES[$system]));
        }
    }

    /**
     * One digestive system per test, reused across organs: `canonical_name` is
     * unique and a second organ must not fail on the system it lands in.
     */
    private function system(string $canonical = 'digestive', string $display = 'Digestive System'): BodySystem
    {
        return BodySystem::query()->firstOrCreate(
            ['canonical_name' => $canonical],
            ['display_name' => $display, 'display_name_ar' => null],
        );
    }

    private function organ(
        string $display,
        string $canonical,
        ?string $domain = null,
        ?BodySystem $system = null,
        ?string $displayNameAr = null,
    ): AnatomicalStructure {
        return AnatomicalStructure::factory()->create([
            'body_system_id' => ($system ?? $this->system())->id,
            'canonical_name' => $canonical,
            'display_name' => $display,
            'display_name_ar' => $displayNameAr,
            'domain' => $domain,
        ]);
    }

    private function poultryDisease(string $name): Disease
    {
        return Disease::factory()->create([
            'name' => $name,
            'domain' => 'poultry',
            'is_active' => true,
        ]);
    }

    private function sign(?AnatomicalStructure $structure, Finding $finding): ClinicalSign
    {
        return ClinicalSign::firstOrCreate(
            ['finding_id' => $finding->id],
            [
                'anatomical_structure_id' => $structure?->id,
                'canonical_name' => $finding->canonical_name,
                'display_name' => $finding->display_name,
            ],
        );
    }

    /**
     * Poultry diseases that reach the liver by name, for the query-budget test.
     *
     * One shared label rather than one per disease: the budget is about the panel
     * loading diseases and their classifications, not about the matcher, and the
     * helper is called twice in one test, so the label has to be reusable.
     */
    private function attachLiverDiseases(int $count, int $from = 1): void
    {
        $finding = Finding::firstOrCreate(
            ['canonical_name' => 'liver_lesions'],
            ['display_name' => 'Liver lesions', 'ontology_type' => 'lesion'],
        );

        $label = $this->sign(null, $finding);

        foreach (range($from, $from + $count - 1) as $index) {
            $disease = $this->poultryDisease(sprintf('Liver Disease %02d', $index));

            DiseaseClassification::factory()->create([
                'disease_id' => $disease->getKey(),
                'zoonotic' => $index % 3 === 0,
            ]);

            $disease->clinicalSigns()->attach($label);
        }
    }

    /**
     * The panel's markup on its own.
     *
     * A page-wide "does not contain" assertion cannot tell a panel that is scoped
     * to the selection from a diagram that names every organ it draws. Only the
     * panel answers the question these assertions are actually asking.
     */
    private function panelOf(Testable $component): string
    {
        $html = $component->html();
        $start = strpos($html, '<aside');
        $end = strpos($html, '</aside>');

        return $start === false || $end === false ? '' : substr($html, $start, $end - $start);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function entry(string $organ): ?array
    {
        return Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', $organ)
            ->instance()->selectedOrganEntry();
    }
}
