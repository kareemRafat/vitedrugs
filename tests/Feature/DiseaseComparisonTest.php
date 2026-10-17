<?php

namespace Tests\Feature;

use App\Enums\OntologyType;
use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The side-by-side disease comparison, shared by `/poultry/compare` and
 * `/large-animals/compare`.
 *
 * Rewritten when the component became part-aware. The three properties worth
 * protecting are:
 *
 * 1. **Part scoping.** The previous picker ran `Disease::where('name','like',…)`
 *    with no domain filter, so the large-animals page offered 151 poultry
 *    diseases — the same defect class as the unscoped search page. A slug from
 *    the other half of the site must not render.
 * 2. **Relational, not payload.** Only 2 of the 151 poultry diseases carry a
 *    `knowledge_payload`, so a payload-only matrix renders empty for almost the
 *    whole section. Signs and lesions have to come from the pivot.
 * 3. **The grouping the plan asked for.** Shared-by-all and unique-to-one are
 *    different questions from "differs", and the page answers both.
 */
class DiseaseComparisonTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------ routes

    public function test_the_poultry_page_renders(): void
    {
        $this->poultryDisease('Coccidiosis');

        $this->get(route('poultry.comparison'))
            ->assertOk()
            ->assertSee(__('poultry.comparison.heading'));
    }

    /**
     * The large-animals half changed from "a view that instantiates a
     * large-animals-only component" to "the same component with a different
     * part". A regression here is invisible on the poultry side.
     */
    public function test_the_large_animals_page_still_renders(): void
    {
        $this->largeAnimalDisease('Foot and Mouth Disease');

        $this->get(route('large-animals.comparison'))
            ->assertOk()
            ->assertSee(__('large-animals.comparison.heading'));
    }

    public function test_the_poultry_page_is_reachable_from_the_poultry_hub(): void
    {
        $this->get(route('poultry.home'))
            ->assertOk()
            ->assertSee(route('poultry.comparison'), escape: false);
    }

    // ------------------------------------------------------------- part scoping

    /**
     * The picker is scoped. This is the assertion the old component could not
     * have made: it returned poultry diseases on the large-animals page.
     */
    public function test_the_picker_only_offers_diseases_of_this_part(): void
    {
        $poultry = $this->poultryDisease('Coccidiosis');
        $this->largeAnimalDisease('Anthrax');

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->set('searches.0', 'Coc');

        $this->assertSame(
            [$poultry->slug],
            $component->instance()->searchResults(0)->pluck('slug')->all(),
        );

        // The large-animals half of the same query.
        $other = Livewire::test('knowledge.disease-compare-builder', ['part' => 'large-animals'])
            ->set('searches.0', 'Coc');

        $this->assertSame([], $other->instance()->searchResults(0)->pluck('slug')->all());
    }

    /**
     * A shared link is attacker-controlled input in the only sense that matters
     * here: it can carry a slug from the other section. It is dropped, not
     * rendered — and the surviving diseases still render.
     */
    public function test_a_slug_from_the_other_part_is_dropped_from_a_shared_link(): void
    {
        $mine = $this->poultryDisease('Coccidiosis');
        $theirs = $this->largeAnimalDisease('Anthrax');

        $html = $this->get(route('poultry.comparison', [
            'diseases' => [$mine->slug, $theirs->slug],
        ]))->assertOk()->getContent();

        $this->assertStringContainsString('Coccidiosis', $html);
        $this->assertStringNotContainsString('Anthrax', $html);
    }

    public function test_a_disease_selected_from_the_other_part_is_dropped(): void
    {
        $mine = $this->poultryDisease('Coccidiosis');
        $theirs = $this->largeAnimalDisease('Anthrax');

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->call('addDisease', 0, $mine->slug)
            ->call('addDisease', 0, $theirs->slug);

        $this->assertSame([$mine->slug], $component->get('selected'));
        $this->assertSame(1, $component->instance()->totalCount);
    }

    // ------------------------------------------------------------- the selection

    public function test_it_compares_two_diseases_side_by_side(): void
    {
        $first = $this->poultryDisease('Coccidiosis');
        $second = $this->poultryDisease('Newcastle Disease');

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->call('addDisease', 0, $first->slug)
            ->call('addDisease', 1, $second->slug);

        $this->assertSame([$first->slug, $second->slug], $component->get('selected'));
        $this->assertCount(2, $component->instance()->selectedDiseases);
    }

    public function test_it_compares_four_diseases(): void
    {
        $slugs = collect(['Coccidiosis', 'Newcastle Disease', 'Avian Influenza', 'Fowl Cholera'])
            ->map(fn (string $name): string => $this->poultryDisease($name)->slug);

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        foreach ($slugs as $slot => $slug) {
            $component->call('addDisease', $slot, $slug);
        }

        $this->assertCount(4, $component->instance()->selectedDiseases);
    }

    /**
     * A choice made in the third picker lands in the third card.
     *
     * `addDisease()` used to append, which meant the picker you typed into had
     * no bearing on where the disease appeared: searching the third box with the
     * first two empty dropped the result into the first card, so the page looked
     * like it had ignored the input.
     */
    public function test_a_pick_from_the_third_picker_lands_in_the_third_card(): void
    {
        $first = $this->poultryDisease('Coccidiosis');
        $second = $this->poultryDisease('Newcastle Disease');
        $third = $this->poultryDisease('Avian Influenza');

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->call('addDisease', 2, $third->slug);

        // Slot 2, with the two before it genuinely empty.
        $this->assertSame([2 => $third->slug], $component->get('selected'));

        // The table still shows exactly one column, and it is the right disease:
        // a column for a card nobody filled would be wider than its disease.
        $columns = $component->instance()->selectedDiseases;
        $this->assertCount(1, $columns);
        $this->assertSame($third->slug, $columns->first()->slug);

        // Filling the first card afterwards must not disturb the third.
        $component->call('addDisease', 0, $first->slug)
            ->call('addDisease', 1, $second->slug);

        $this->assertSame(
            [$first->slug, $second->slug, $third->slug],
            $component->instance()->selectedDiseases->pluck('slug')->all(),
        );
    }

    /**
     * The picker boxes stay attached to their own card.
     *
     * Removal rebuilds both arrays rather than shifting them independently, so a
     * half-typed search can never end up filtering a different column than the
     * one it was typed in.
     */
    public function test_removal_keeps_each_card_attached_to_its_own_search_box(): void
    {
        $slugs = collect(['Coccidiosis', 'Newcastle Disease', 'Avian Influenza'])
            ->map(fn (string $name): string => $this->poultryDisease($name)->slug);

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->call('addDisease', 0, $slugs[0])
            ->call('addDisease', 1, $slugs[1])
            ->call('addDisease', 2, $slugs[2])
            ->call('removeDisease', 1);

        $this->assertSame([$slugs[0], $slugs[2]], $component->get('selected'));
        $this->assertSame(['', '', '', ''], $component->get('searches'));
    }

    /**
     * Five is a refusal, not a truncation. Silently dropping the fifth would
     * leave the reader comparing something other than what they asked for.
     */
    public function test_a_fifth_disease_is_refused(): void
    {
        $slugs = collect(['Coccidiosis', 'Newcastle Disease', 'Avian Influenza', 'Fowl Cholera', 'Salmonellosis'])
            ->map(fn (string $name): string => $this->poultryDisease($name)->slug);

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        foreach ($slugs as $slot => $slug) {
            // The fifth has nowhere to go — there is no slot 4.
            $component->call('addDisease', min($slot, 3), $slug);
        }

        $this->assertCount(4, $component->get('selected'));
        $this->assertNotContains($slugs->last(), $component->get('selected'));
        $this->assertTrue($component->instance()->isFull);
    }

    public function test_the_same_disease_cannot_be_added_twice(): void
    {
        $slug = $this->poultryDisease('Coccidiosis')->slug;

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->call('addDisease', 0, $slug)
            ->call('addDisease', 0, $slug);

        $this->assertSame([$slug], $component->get('selected'));
    }

    /**
     * Removal closes the gap rather than leaving a hole, because the table's
     * columns come from the selection in order — a hole would render as an empty
     * column and read as "these diseases share nothing".
     */
    public function test_removing_a_disease_closes_the_gap(): void
    {
        $slugs = collect(['Coccidiosis', 'Newcastle Disease', 'Avian Influenza'])
            ->map(fn (string $name): string => $this->poultryDisease($name)->slug);

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        foreach ($slugs as $slot => $slug) {
            $component->call('addDisease', $slot, $slug);
        }

        $component->call('removeDisease', 1)
            ->assertSet('selected', [$slugs[0], $slugs[2]]);
    }

    public function test_clearing_removes_every_disease(): void
    {
        $slugs = collect(['Coccidiosis', 'Newcastle Disease'])
            ->map(fn (string $name): string => $this->poultryDisease($name)->slug);

        $component = Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        foreach ($slugs as $slot => $slug) {
            $component->call('addDisease', $slot, $slug);
        }

        $component->call('clearAll')
            ->assertSet('selected', [])
            ->assertSet('searches', ['', '', '', '']);
    }

    public function test_the_search_box_is_cleared_when_a_disease_is_chosen_from_it(): void
    {
        $slug = $this->poultryDisease('Coccidiosis')->slug;

        Livewire::test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->set('searches.2', 'Coc')
            ->call('addDisease', 2, $slug)
            ->assertSet('searches.2', '');
    }

    // ---------------------------------------------------------------- the URL

    public function test_the_url_carries_the_selection(): void
    {
        $first = $this->poultryDisease('Coccidiosis');
        $second = $this->poultryDisease('Newcastle Disease');

        Livewire::withQueryParams(['diseases' => [$first->slug, $second->slug]])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->assertSet('selected', [$first->slug, $second->slug])
            ->assertSee('Coccidiosis')
            ->assertSee('Newcastle Disease');
    }

    public function test_a_link_with_more_than_four_diseases_is_truncated(): void
    {
        $slugs = collect(['Coccidiosis', 'Newcastle Disease', 'Avian Influenza', 'Fowl Cholera', 'Salmonellosis'])
            ->map(fn (string $name): string => $this->poultryDisease($name)->slug);

        Livewire::withQueryParams(['diseases' => $slugs->all()])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->assertSet('selected', $slugs->take(4)->values()->all());
    }

    // ------------------------------------------------------------ the grouping

    /**
     * The core of the page. A finding every disease has cannot tell them apart,
     * and a finding only one has is the whole reason to run the comparison —
     * so the two are separated rather than left as one undifferentiated list.
     *
     * Built from the relational pivot, not the payload, because that is the
     * source that is populated for poultry.
     */
    public function test_findings_are_grouped_as_shared_unique_or_partial(): void
    {
        $respiratory = $this->anatomy('Respiratory tract');
        $digestive = $this->anatomy('Digestive tract');

        $shared = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $respiratory);
        $onlyFirst = $this->sign('coughing', 'Coughing', OntologyType::Sign, $respiratory);
        $onlySecond = $this->sign('diarrhoea', 'Diarrhoea', OntologyType::Sign, $digestive);

        $this->poultryDisease('Coccidiosis', [$shared, $onlyFirst]);
        $this->poultryDisease('Newcastle Disease', [$shared, $onlySecond]);

        $rows = $this->rows(
            Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
                ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
                ->instance()->signMatrix,
        );

        $this->assertSame('shared', $this->groupOf($rows, 'Pyrexia'));
        $this->assertSame('unique', $this->groupOf($rows, 'Coughing'));
        $this->assertSame('unique', $this->groupOf($rows, 'Diarrhoea'));

        // And the presence map itself, which the table renders.
        $pyrexia = $this->rowOf($rows, 'Pyrexia');
        $this->assertFalse($pyrexia['differs'], 'A finding every disease has is not a difference.');

        $coughing = $this->rowOf($rows, 'Coughing');
        $this->assertTrue($coughing['differs']);
        $this->assertTrue($coughing['presence']['coccidiosis']);
        $this->assertFalse($coughing['presence']['newcastle-disease']);
    }

    /**
     * Group headers carry `has_diff` so that a group which is identical across
     * every column disappears with the "differences only" toggle, instead of
     * leaving a label hanging over nothing.
     */
    public function test_group_headers_report_whether_they_hold_any_difference(): void
    {
        $respiratory = $this->anatomy('Respiratory tract');

        $shared = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $respiratory);
        $onlyFirst = $this->sign('coughing', 'Coughing', OntologyType::Sign, $respiratory);

        $this->poultryDisease('Coccidiosis', [$shared, $onlyFirst]);
        $this->poultryDisease('Newcastle Disease', [$shared]);

        $matrix = Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->instance()->signMatrix;

        $headers = collect($matrix)->where('is_header', true);

        $this->assertTrue($headers->firstWhere('group', 'shared')['has_diff'] === false);
        $this->assertTrue($headers->firstWhere('group', 'unique')['has_diff']);
    }

    /**
     * Unique rows sort before shared ones. A sign only one disease has is the
     * reason to open the page; a sign all of them have is the least informative
     * thing on it.
     */
    public function test_unique_rows_are_listed_before_shared_ones(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');

        $shared = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $anatomy);
        $onlyFirst = $this->sign('coughing', 'Coughing', OntologyType::Sign, $anatomy);

        $this->poultryDisease('Coccidiosis', [$shared, $onlyFirst]);
        $this->poultryDisease('Newcastle Disease', [$shared]);

        $groups = collect(Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->instance()->signMatrix)
            ->where('is_header', true)
            ->pluck('group')
            ->all();

        $this->assertSame(['unique', 'shared'], $groups);
    }

    /**
     * "Shared by some" needs at least three diseases — with two, a finding is
     * either in both or in one, so the group is unreachable. It is the group
     * that matters most in practice (two diseases look alike, a third breaks
     * the tie), so it gets its own case rather than being inferred from the
     * two-way test above.
     */
    public function test_a_finding_in_two_of_three_diseases_is_grouped_as_partial(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');

        $inTwo = $this->sign('coughing', 'Coughing', OntologyType::Sign, $anatomy);
        $everywhere = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $anatomy);

        $this->poultryDisease('Coccidiosis', [$inTwo, $everywhere]);
        $this->poultryDisease('Newcastle Disease', [$inTwo, $everywhere]);
        $this->poultryDisease('Avian Influenza', [$everywhere]);

        $rows = $this->rows(
            Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease', 'avian-influenza']])
                ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
                ->instance()->signMatrix,
        );

        $partial = $this->rowOf($rows, 'Coughing');

        $this->assertSame('partial', $partial['group']);
        $this->assertSame(2, $partial['present_count']);
        $this->assertSame(3, $partial['total']);
        $this->assertTrue($partial['differs']);
        $this->assertTrue($partial['presence']['coccidiosis']);
        $this->assertTrue($partial['presence']['newcastle-disease']);
        $this->assertFalse($partial['presence']['avian-influenza']);
    }

    // ------------------------------------------------------------- the matrices

    /**
     * Signs and lesions are the same pivot read through
     * `finding.ontology_type`. If the split is wrong, one of the two tabs comes
     * out empty and the other doubles up.
     */
    public function test_signs_and_lesions_are_split_by_the_ontology_type_of_their_finding(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');

        $sign = $this->sign('coughing', 'Coughing', OntologyType::Sign, $anatomy);
        $lesion = $this->sign('consolidation', 'Consolidation', OntologyType::Lesion, $anatomy);

        $this->poultryDisease('Coccidiosis', [$sign, $lesion]);
        $this->poultryDisease('Newcastle Disease', [$sign, $lesion]);

        $component = Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        $signs = $this->rows($component->instance()->signMatrix);
        $lesions = $this->rows($component->instance()->lesionMatrix);

        $this->assertSame(['Coughing'], $this->namesOf($signs));
        $this->assertSame(['Consolidation'], $this->namesOf($lesions));
    }

    /**
     * `findings.ontology_type` is nullable, and it is null on the curated signs —
     * Pyrexia, Inappetence and Hemorrhagic diarrhea on the live poultry data.
     * Matching `'sign'` exactly dropped all three from the comparison, which is how
     * the most common sign in the vocabulary went missing without any error.
     */
    public function test_a_finding_with_no_ontology_type_is_still_treated_as_a_sign(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');
        $untyped = $this->sign('pyrexia', 'Pyrexia', null, $anatomy);

        $this->poultryDisease('Coccidiosis', [$untyped]);
        $this->poultryDisease('Newcastle Disease', [$untyped]);

        $component = Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        $this->assertSame(['Pyrexia'], $this->namesOf($this->rows($component->instance()->signMatrix)));
        $this->assertSame([], $component->instance()->lesionMatrix);
    }

    /**
     * The organs axis reads `important_organs_affected` from the knowledge
     * payload.
     *
     * Deriving it from `clinicalSigns.anatomical_structure` was built first and
     * removed: only 27 of 1,109 sign rows carry an anatomy, covering 2 of the
     * 151 poultry diseases. The payload field is populated in 152 of the 154
     * source documents and simply has not been imported, so the tab fills
     * itself once the bulk import runs rather than needing a second code path.
     */
    public function test_organs_are_read_from_the_imported_knowledge_payload(): void
    {
        $first = $this->poultryDisease('Coccidiosis', [], [
            'knowledge_payload' => ['important_organs_affected' => ['liver', 'spleen', 'intestine']],
        ]);

        $this->poultryDisease('Newcastle Disease', [], [
            'knowledge_payload' => ['important_organs_affected' => ['liver', 'bursa of Fabricius']],
        ]);

        $rows = $this->rows(
            Livewire::withQueryParams(['diseases' => [$first->slug, 'newcastle-disease']])
                ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
                ->instance()->organMatrix,
        );

        $this->assertSame('shared', $this->groupOf($rows, 'liver'));
        $this->assertSame('unique', $this->groupOf($rows, 'spleen'));
        $this->assertSame('unique', $this->groupOf($rows, 'bursa of Fabricius'));
    }

    /**
     * Without a payload the organs tab is not rendered at all — the same honest
     * empty the payload-only tabs get, rather than a tab built on the two
     * diseases that happen to carry an anatomy.
     */
    public function test_the_organs_tab_is_absent_until_the_bulk_import_lands(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');
        $sign = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $anatomy);

        $this->poultryDisease('Coccidiosis', [$sign]);
        $this->poultryDisease('Newcastle Disease', [$sign]);

        $this->assertSame([], Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
            ->instance()->organMatrix);
    }

    /**
     * The payload still supplies what the pivot cannot. This is additive rather
     * than a replacement, so the large-animals page — where the payload is
     * actually populated — gains rows instead of losing them.
     */
    public function test_payload_entries_are_merged_with_the_relational_ones(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');

        $relational = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $anatomy);

        $first = $this->poultryDisease('Coccidiosis', [$relational], [
            'knowledge_payload' => ['clinical_signs' => [
                ['canonical_name' => 'drop in production', 'display_name' => 'Drop in production'],
            ]],
        ]);

        $this->poultryDisease('Newcastle Disease', [$relational]);

        $names = $this->namesOf($this->rows(
            Livewire::withQueryParams(['diseases' => [$first->slug, 'newcastle-disease']])
                ->test('knowledge.disease-compare-builder', ['part' => 'poultry'])
                ->instance()->signMatrix,
        ));

        $this->assertContains('Pyrexia', $names);
        $this->assertContains('Drop in production', $names);
    }

    public function test_a_tab_with_no_data_is_not_rendered_at_all(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');
        $sign = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $anatomy);

        $this->poultryDisease('Coccidiosis', [$sign]);
        $this->poultryDisease('Newcastle Disease', [$sign]);

        $component = Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        // No knowledge payload anywhere, so the payload-only matrices are empty.
        $this->assertSame([], $component->instance()->diagnosisMatrix);
        $this->assertSame([], $component->instance()->treatmentMatrix);
        $this->assertSame([], $component->instance()->referencesMatrix);

        $this->assertNotSame([], $component->instance()->signMatrix);
    }

    public function test_the_overview_reports_the_diseases_treating_drugs(): void
    {
        $disease = $this->poultryDisease('Coccidiosis');
        $disease->products()->attach(Product::factory()->create(['trade_name' => 'Amprolium'])->id);

        $other = $this->poultryDisease('Newcastle Disease');

        $component = Livewire::withQueryParams(['diseases' => ['coccidiosis', 'newcastle-disease']])
            ->test('knowledge.disease-compare-builder', ['part' => 'poultry']);

        $products = collect($component->instance()->overviewMatrix)->firstWhere('name', 'products');

        $this->assertStringContainsString('Amprolium', $products['values']['coccidiosis']);
        $this->assertSame('', $products['values']['newcastle-disease']);
        $this->assertTrue($products['differs']);
    }

    // -------------------------------------------------------------- translation

    /**
     * A missing key renders as the raw key, which looks like a working page
     * right up until a reader sees `comparison.groups.unique` on it.
     */
    public function test_every_translation_key_on_the_page_resolves(): void
    {
        $anatomy = $this->anatomy('Respiratory tract');
        $shared = $this->sign('pyrexia', 'Pyrexia', OntologyType::Sign, $anatomy);
        $only = $this->sign('coughing', 'Coughing', OntologyType::Sign, $anatomy);

        $this->poultryDisease('Coccidiosis', [$shared, $only]);
        $this->poultryDisease('Newcastle Disease', [$shared]);

        foreach (['en', 'ar'] as $locale) {
            app()->setLocale($locale);

            $this->get(route('poultry.comparison', ['diseases' => ['coccidiosis', 'newcastle-disease']]))
                ->assertOk()
                ->assertDontSee('comparison.', escape: false);
        }
    }

    // ------------------------------------------------------------------ helpers

    /**
     * @param  array<int, array<string, mixed>>  $matrix
     * @return Collection<int, array<string, mixed>>
     */
    private function rows(array $matrix): Collection
    {
        return collect($matrix)->where('is_header', false)->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    private function rowOf(Collection $rows, string $name): array
    {
        return $rows->firstWhere('name', $name) ?? [];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    private function groupOf(Collection $rows, string $name): ?string
    {
        return $this->rowOf($rows, $name)['group'] ?? null;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @return array<int, string>
     */
    private function namesOf(Collection $rows): array
    {
        return $rows->pluck('name')->all();
    }

    private function anatomy(string $name): AnatomicalStructure
    {
        return AnatomicalStructure::factory()->create([
            'canonical_name' => mb_strtolower($name),
            'display_name' => $name,
            'display_name_ar' => $name,
            'type' => 'organ',
        ]);
    }

    /**
     * `FindingFactory` randomises `ontology_type`, and the comparison reads that
     * column to split signs from lesions — so it is pinned here rather than left
     * to a coin flip that would fail roughly half the time. `null` is a real
     * value in the data, not a gap in the test.
     */
    private function sign(
        string $canonicalName,
        string $displayName,
        ?OntologyType $ontologyType,
        AnatomicalStructure $anatomy,
    ): ClinicalSign {
        $finding = Finding::factory()->create([
            'canonical_name' => $canonicalName,
            'display_name' => $displayName,
            'category' => $ontologyType === OntologyType::Lesion ? 'postmortem' : 'clinical',
            'ontology_type' => $ontologyType,
        ]);

        return ClinicalSign::factory()->create([
            'canonical_name' => $canonicalName,
            'display_name' => $displayName,
            'display_name_ar' => $displayName,
            'finding_id' => $finding->id,
            'anatomical_structure_id' => $anatomy->id,
            'modifier_id' => null,
        ]);
    }

    /**
     * @param  array<int, ClinicalSign>  $signs
     * @param  array<string, mixed>  $attributes
     */
    private function poultryDisease(string $name, array $signs = [], array $attributes = []): Disease
    {
        $disease = Disease::factory()->poultry()->named($name)->create($attributes);

        foreach ($signs as $sign) {
            $disease->clinicalSigns()->attach($sign->id, ['weight' => 5]);
        }

        return $disease;
    }

    private function largeAnimalDisease(string $name): Disease
    {
        return Disease::factory()->largeAnimals()->named($name)->create();
    }
}
