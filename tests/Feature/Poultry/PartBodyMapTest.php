<?php

namespace Tests\Feature\Poultry;

use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $component = Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertSee($disease->name)
            ->assertSee(__('poultry.body_map.mapped'));

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

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'liver')
            ->assertSee($disease->name)
            ->assertSee(__('poultry.body_map.derived'))
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

        Livewire::test('anatomy.body-map', ['part' => 'poultry'])
            ->call('selectOrgan', 'thyroid_gland')
            ->assertSee(__('poultry.body_map.organ_empty'))
            ->assertDontSee(__('poultry.body_map.derived'));
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
        return ClinicalSign::factory()->create([
            'anatomical_structure_id' => $structure?->id,
            'finding_id' => $finding->id,
            'canonical_name' => $finding->canonical_name,
            'display_name' => $finding->display_name,
        ]);
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
