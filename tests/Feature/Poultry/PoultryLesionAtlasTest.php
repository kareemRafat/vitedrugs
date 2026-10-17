<?php

namespace Tests\Feature\Poultry;

use App\Enums\OntologyType;
use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\FindingImage;
use App\Services\Knowledge\LesionAtlasService;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The poultry lesion atlas, `/poultry/atlas`.
 *
 * Phase 9 was planned as "browse the merged lesion set, group by body system,
 * search it, attach images later". Three of those hold on the data as it is.
 * Two do not, and both failures are silent, so they are asserted here:
 *
 * **No lesion carries an organ.** Zero of the 640 poultry lesions have a
 * `clinical_signs.anatomical_structure_id`, so the grouping on this page is
 * produced by matching organ names inside lesion *labels*. If that ever quietly
 * becomes curated data the page would look the same and mean something stronger,
 * so the test pins the tier it came from.
 *
 * **A third of the atlas matches nothing.** 228 of 640 name no organ at all. A
 * group-by-body-system implementation drops them on the floor, which looks like a
 * successful page with a smaller number on it. `the_lesions_naming_no_organ_are_
 * listed_not_dropped` exists to make that failure loud.
 *
 * The images are the third gap: Phase 9 planned a `media_assets` table this
 * application does not have, so the layout is asserted to degrade to a framed
 * placeholder rather than a broken image.
 */
class PoultryLesionAtlasTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------- the page lands

    #[Test]
    public function the_atlas_page_renders(): void
    {
        $this->get(route('poultry.atlas'))
            ->assertOk()
            ->assertSee(__('poultry.atlas.heading'));
    }

    /**
     * `:count` is substituted, not printed. `__()` returns the key unchanged when
     * it is given no replacement map, so a helper that forwards only the key turns
     * every placeholder on the page into literal `:count` text in the markup —
     * which renders fine visually and is wrong in every language.
     */
    #[Test]
    public function no_translation_placeholder_reaches_the_page(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);
        $this->lesion('Liver lesions', 'poultry');
        $this->lesion('Cloacal prolapse', 'poultry');

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->html();

        $this->assertStringNotContainsString(':count', $html, 'A translation placeholder reached the page un-substituted.');
        $this->assertStringContainsString('1 diseases', $html);
    }

    #[Test]
    public function the_atlas_page_renders_in_arabic(): void
    {
        app()->setLocale('ar');

        $this->get(route('poultry.atlas'))
            ->assertOk()
            ->assertSee(__('poultry.atlas.heading', [], 'ar'), false);
    }

    // ------------------------------------------------------- only poultry lesions

    /**
     * `findings` has no `domain` column, so the only way to ask which part a
     * lesion belongs to is through the diseases that carry it. A lesion carried
     * only by large-animal diseases must not leak into the poultry atlas.
     */
    #[Test]
    public function a_lesion_belonging_only_to_large_animals_is_not_listed(): void
    {
        $this->lesion('Bloat', 'large_animal');
        $poultry = $this->lesion('Liver lesions', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee($poultry->display_name)
            ->assertDontSee('Bloat');
    }

    #[Test]
    public function a_clinical_sign_is_not_a_lesion(): void
    {
        $sign = $this->lesion('Ruffled feathers', 'poultry');
        $sign->update(['ontology_type' => 'sign']);

        $lesion = $this->lesion('Liver lesions', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee($lesion->display_name)
            ->assertDontSee($sign->display_name);
    }

    // ------------------------------------------------------ grouping is inferred

    /**
     * A lesion naming an organ is filed under that organ's body system. The
     * organ link is a name match — no `anatomical_structure_id` anywhere — so
     * the tier has to say so in the markup.
     */
    #[Test]
    public function a_lesion_naming_an_organ_is_filed_under_its_body_system(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);
        $lesion = $this->lesion('Liver lesions', 'poultry');

        $component = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee(__('poultry.atlas.grouping_note'))
            ->assertSee($system->display_name)
            ->assertSee($lesion->display_name);

        $sections = $component->instance()->sections();

        $this->assertArrayHasKey('digestive', $sections);
        $this->assertSame(
            [$lesion->id],
            $sections['digestive']['lesions']->pluck('id')->all(),
        );
        $this->assertSame(LesionAtlasService::TIER_GROUPED, $sections['digestive']['tier']);
    }

    /**
     * The bucket is the one guarantee that a filter cannot hide a third of the
     * atlas. A group-by implementation returns nothing here and the page still
     * renders — which is exactly why it needs asserting.
     */
    #[Test]
    public function the_lesions_naming_no_organ_are_listed_not_dropped(): void
    {
        $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', BodySystem::first());

        $placed = $this->lesion('Liver lesions', 'poultry');
        $orphan = $this->lesion('Post-mortem: birds dull', 'poultry');

        $component = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee(__('poultry.atlas.unclassified'))
            ->assertSee($orphan->display_name);

        $sections = $component->instance()->sections();

        $this->assertArrayHasKey(LesionAtlasService::UNCLASSIFIED_KEY, $sections);
        $this->assertSame(
            [$orphan->id],
            $sections[LesionAtlasService::UNCLASSIFIED_KEY]['lesions']->pluck('id')->all(),
        );
        $this->assertNotContains($placed->id, $sections[LesionAtlasService::UNCLASSIFIED_KEY]['lesions']->pluck('id')->all());
    }

    /**
     * Every lesion appears exactly once across all sections. A lesion naming two
     * organs is filed under the first in display order — it is one row in
     * `findings`, and duplicating it would inflate the counts each section reports.
     */
    #[Test]
    public function every_lesion_appears_exactly_once(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);
        $this->organ('Kidney', 'kidney', $system);

        $both = $this->lesion('Hepatic and renal lesions', 'poultry');
        $one = $this->lesion('Liver lesions', 'poultry');
        $none = $this->lesion('Post-mortem: birds dull', 'poultry');

        $sections = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->instance()
            ->sections();

        $ids = $sections->flatMap(fn (array $section) => $section['lesions']->pluck('id'))->all();

        $this->assertCount(3, $ids);
        $this->assertSame($ids, array_unique($ids));
        $this->assertEqualsCanonicalizing([$both->id, $one->id, $none->id], $ids);
    }

    // ------------------------------------------------------------------ filtering

    #[Test]
    public function a_body_system_filter_narrows_the_list_and_toggles_off(): void
    {
        $system = $this->bodySystem('Respiratory', 'respiratory');
        $other = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Air sacs', 'air_sacs', $system);
        $this->organ('Liver', 'liver', $other);

        $resp = $this->lesion('Opaque Air Sacs', 'poultry');
        $hep = $this->lesion('Liver lesions', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->call('selectSystem', 'respiratory')
            ->assertSee($resp->display_name)
            ->assertDontSee($hep->display_name)
            ->call('selectSystem', 'respiratory')
            ->assertSee($resp->display_name)
            ->assertSee($hep->display_name);
    }

    #[Test]
    public function search_matches_lesion_names(): void
    {
        $this->lesion('Opaque Air Sacs', 'poultry');
        $this->lesion('Liver lesions', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->set('search', 'Air Sacs')
            ->assertSee('Opaque Air Sacs')
            ->assertDontSee('Liver lesions');
    }

    /**
     * The search term is user input and goes into a `LIKE`. Without escaping it,
     * `%` turns into "match everything" and `_` into "match any character", which
     * is a wrong-results bug rather than a security one — but it is still a bug.
     */
    #[Test]
    public function a_wildcard_in_the_search_is_treated_as_text(): void
    {
        $this->lesion('Liver lesions', 'poultry');
        $this->lesion('Cropped beak', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->set('search', '%')
            ->assertSee(__('poultry.atlas.empty'))
            ->assertDontSee('Liver lesions');
    }

    #[Test]
    public function a_search_with_no_match_shows_an_empty_state_not_a_broken_page(): void
    {
        $this->lesion('Liver lesions', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->set('search', 'zzzznothing')
            ->assertSee(__('poultry.atlas.empty'));
    }

    // -------------------------------------------------------------------- letters

    /**
     * The rail offers only letters that have lesions. Offering all 26 would make
     * most of them lead to nothing, which reads as a broken page.
     */
    #[Test]
    public function the_letter_rail_offers_only_letters_that_exist(): void
    {
        $this->lesion('Liver lesions', 'poultry');
        $this->lesion('Opaque Air Sacs', 'poultry');

        $letters = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->instance()
            ->letters();

        $this->assertSame(['L', 'O'], $letters);
    }

    #[Test]
    public function selecting_a_letter_narrows_the_list(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);
        $this->organ('Air sacs', 'air_sacs', $system);

        $l = $this->lesion('Liver lesions', 'poultry');
        $o = $this->lesion('Opaque Air Sacs', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->call('selectLetter', 'O')
            ->assertSee($o->display_name)
            ->assertDontSee($l->display_name);
    }

    /**
     * A lesion name opening on a digit belongs under no letter. It must stay
     * reachable from "All" rather than vanishing, which is the whole reason the
     * empty letter is stored instead of defaulted to `#`.
     */
    #[Test]
    public function a_name_opening_on_a_digit_is_reachable_but_creates_no_letter(): void
    {
        $this->lesion('3-Day Disease', 'poultry');
        $this->lesion('Liver lesions', 'poultry');

        $component = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee('3-Day Disease');

        $this->assertSame(['L'], $component->instance()->letters());
    }

    /**
     * A search that removes the selected letter must not leave the page pinned
     * to an empty letter with no way back.
     */
    #[Test]
    public function a_search_that_empties_the_selected_letter_clears_it(): void
    {
        $this->lesion('Liver lesions', 'poultry');
        $this->lesion('Opaque Air Sacs', 'poultry');

        $component = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->call('selectLetter', 'O')
            ->set('search', 'Liver');

        $this->assertSame('', $component->instance()->letter);
    }

    // ------------------------------------------------------------------- diseases

    /**
     * The diseases a lesion causes sit on the tile itself, linked, rather than
     * behind a click. 571 of the 640 poultry lesions name exactly one disease,
     * so an overlay to reach that one link was a page-dimming round trip for
     * something already visible — this is the reader's next action, and it is
     * directly reachable.
     */
    #[Test]
    public function a_lesion_lists_the_diseases_that_cause_it(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $this->lesion('Liver lesions', 'poultry', $disease);

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee($disease->name);
    }

    /**
     * The link is a real link to the disease page. A tile that rendered the
     * disease names as plain text would pass the assertion above and still leave
     * the reader with nowhere to go from it.
     */
    #[Test]
    public function a_causing_disease_links_to_its_page(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $this->lesion('Liver lesions', 'poultry', $disease);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertStringContainsString(
            route('poultry.diseases.show', $disease->slug),
            $html,
        );
    }

    /**
     * An inactive disease is not a cause worth showing. Where it is the *only*
     * disease carrying a lesion, the lesion itself drops out — the part's set is
     * "lesions carried by a live poultry disease", and a retired disease carrying
     * one is not evidence the lesion is poultry's.
     */
    #[Test]
    public function an_inactive_disease_is_not_treated_as_a_cause(): void
    {
        $inactive = $this->disease('Old Syndrome', 'poultry');
        $inactive->update(['is_active' => false]);

        $orphan = $this->lesion('Liver lesions', 'poultry', $inactive);
        $live = $this->lesion('Cloacal prolapse', 'poultry');

        $entries = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee($live->display_name)
            ->assertDontSee($orphan->display_name)
            ->instance()
            ->lesions();

        $this->assertNull($entries->firstWhere('id', $orphan->id));
        $this->assertNotNull($entries->firstWhere('id', $live->id));
    }

    // ------------------------------------------------------ the atlas modal

    /**
     * The count on the tile used to be a dead `<span>`.
     *
     * A lesion naming 43 diseases showed three chips and a "+40" that led
     * nowhere: no link, no handler, and no lesion page anywhere on the site to
     * link out to. Every reader who could not read the last two names on the
     * tile had no route to them at all. This asserts the chip is a real control
     * that says how many it is hiding — "12" on its own reads as a tally, not as
     * an invitation.
     */
    #[Test]
    public function a_lesion_with_more_diseases_than_the_tile_shows_offers_the_rest(): void
    {
        $this->lesionWithDiseases('Liver lesions', 'poultry', [
            'Aflatoxicosis', 'Botulism', 'Cholera', 'Newcastle', 'Salmonellosis',
        ]);

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee('+2')
            ->assertSee(__('poultry.atlas.all_diseases', ['count' => 5]));
    }

    /**
     * **The chip has to be a control.** This is the assertion the test above cannot
     * make on its own.
     *
     * `assertSee('+2')` and `assertSee('Show all 5 diseases')` both pass against the
     * dead `<span>` this replaced, because the markup and the aria-label were never
     * the problem — the element was. Both strings are there and the chip still opens
     * nothing, which is precisely the state this page shipped in and precisely the
     * state a reader cannot describe as broken. So the tag is matched instead: a
     * `<button>` whose body is the count, carrying the handler that opens the dialog.
     *
     * Matching the whole element rather than a substring is also what makes this safe
     * against the modal's own close button — that one wraps an icon, not a number, so
     * there is only ever one match.
     */
    #[Test]
    public function the_tail_chip_is_a_button_that_opens_the_dialog_not_a_tally(): void
    {
        $this->lesionWithDiseases('Liver lesions', 'poultry', [
            'Aflatoxicosis', 'Botulism', 'Cholera', 'Newcastle', 'Salmonellosis',
        ]);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertSame(
            1,
            preg_match('/<button\b[^>]*>\s*\+2\s*<\/button>/s', $html, $chip),
            'The +N chip is not a button. It reads as a tally and opens nothing.',
        );
        $this->assertStringContainsString(
            '$dispatch(\'atlas-diseases\'',
            $chip[0],
            'The button renders but carries no handler, so it is the old dead chip in new markup.',
        );
    }

    /**
     * **All of them, not the hidden remainder.**
     *
     * The dialog opens from a chip that already shows three names, so the
     * tempting implementation is to pass only what the tile is missing and let
     * the two halves add up. That makes the dialog useless as a reference — you
     * cannot copy a list out of it, and the tile and the dialog disagree about
     * order. It has to stand on its own.
     */
    #[Test]
    public function the_modal_carries_every_disease_not_only_the_hidden_ones(): void
    {
        $names = ['Aflatoxicosis', 'Botulism', 'Cholera', 'Newcastle', 'Salmonellosis'];
        $diseases = collect($names)
            ->map(fn (string $name): Disease => $this->disease($name, 'poultry'));

        $lesion = $this->lesion('Liver lesions', 'poultry', $diseases->first());
        $sign = $lesion->clinicalSigns()->firstOrFail();

        $diseases->slice(1)->each(fn (Disease $disease) => $disease->clinicalSigns()->attach($sign));

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        foreach ($diseases as $disease) {
            $this->assertStringContainsString(
                $disease->slug,
                $html,
                "The modal payload omits {$disease->name}. Its tile shows three names and the reader has nowhere else to find the rest.",
            );
        }
    }

    /**
     * Nothing to open, nothing offered.
     *
     * 571 of 640 lesions name exactly one disease. A "+0" button — or one on a
     * tile whose three chips are the whole list — is a control that opens a dialog
     * containing what the reader was already looking at.
     */
    #[Test]
    public function a_lesion_whose_diseases_all_fit_on_the_tile_offers_no_dialog(): void
    {
        $this->lesionWithDiseases('Liver lesions', 'poultry', ['Aflatoxicosis', 'Botulism', 'Cholera']);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertStringNotContainsString(
            "\$dispatch('atlas-diseases'",
            $html,
            'A tile whose diseases all fit carries a dialog payload and a button that opens nothing.',
        );
        $this->assertStringNotContainsString(
            __('poultry.atlas.all_diseases', ['count' => 3]),
            $html,
        );
    }

    /**
     * One dialog for the whole grid.
     *
     * The obvious per-tile implementation puts a copy of the modal inside every
     * tile. Forty-eight copies is forty-eight dialogs wired to forty-eight events,
     * and they are indistinguishable to a reader opening them with the keyboard.
     * Asserted on the *count* of the listener rather than its presence, because a
     * single working one hides the other forty-seven.
     */
    #[Test]
    public function the_modal_is_mounted_once_for_the_whole_grid(): void
    {
        $this->lesionWithDiseases('Liver lesions', 'poultry', [
            'Aflatoxicosis', 'Botulism', 'Cholera', 'Newcastle',
        ]);
        // Distinct names, not a second copy of the first lesion's list: `diseases.name`
        // is unique, and a shared name would make this a test of the constraint.
        $this->lesionWithDiseases('Cloacal prolapse', 'poultry', [
            'Dystocia', 'Egg drop', 'Hypovitaminosis', 'Rickets',
        ]);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertSame(
            1,
            substr_count($html, 'atlas-diseases.window'),
            'The atlas mounted a dialog per tile. One listener, one dialog.',
        );
    }

    /**
     * **The two ways out that do not require finding the close button.**
     *
     * Escape is bound on `window` rather than the panel, so it works before
     * anything inside has focus — and the backdrop is a sibling of the panel
     * rather than its parent, so a click that lands on a long disease name does
     * not close the dialog under the reader's cursor.
     */
    #[Test]
    public function the_modal_closes_on_escape_and_on_the_backdrop(): void
    {
        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertStringContainsString('keydown.escape.window', $html);
        $this->assertStringContainsString('x-on:click="hide()"', $html);
        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
    }

    /**
     * **The whole Alpine expression survives the HTML parser.**
     *
     * This is here because the dialog shipped broken once and nothing caught it.
     * The focus trap's selector needs `"` in it, so it is written `\"` inside an
     * attribute — and HTML has no escape character inside an attribute value. The
     * parser ended `x-data` at that quote, Alpine was handed an object literal
     * cut off mid-string, and *every* method in it silently stopped existing. The
     * page rendered perfectly, every test above passed, and the console filled
     * with `hide is not defined` / `detail is not defined`. No server-side
     * assertion on the response body can see that, because the damage is done by
     * the browser's parser and not by Blade.
     *
     * So parse the response the way a browser would and assert the attribute
     * survived intact. `assertStringContainsString('cycle', $html)` would pass on
     * the broken markup — the text `cycle` is in the HTML either way, just not
     * inside the attribute Alpine reads.
     */
    #[Test]
    public function the_modals_alpine_expression_is_intact_once_the_browser_has_parsed_it(): void
    {
        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        libxml_clear_errors();

        $xpath = new DOMXPath($document);

        // The element that listens for the atlas event is the modal's root, and
        // its `x-data` is what Alpine evaluates. The attribute name holds a colon
        // and a full stop, neither of which is legal unquoted in an XPath name
        // step, so it is matched on `name()` rather than written inline.
        $roots = $xpath->query('//*[@*[name()="x-on:atlas-diseases.window"]]');

        $this->assertNotFalse($roots, 'The XPath query itself failed.');
        $this->assertSame(1, $roots->length, 'The modal root carrying the event listener was not found.');

        $expression = $roots->item(0)->getAttribute('x-data');

        $this->assertNotEmpty(
            $expression,
            'x-data is empty. An attribute truncated by the HTML parser parses as an empty string, which is how the dialog shipped with every method undefined.',
        );

        // Every method, and the literal the truncation used to eat.
        foreach (['show(', 'hide(', 'cycle(', '[tabindex=\\"-1\\"]'] as $fragment) {
            $this->assertStringContainsString(
                $fragment,
                $expression,
                "The parsed x-data is missing {$fragment}. The expression was cut short, so Alpine binds none of it.",
            );
        }

        // The failure this guards is a *parse* error, so the expression has to be
        // syntactically whole too — balanced braces are the cheapest proxy and the
        // truncation always left them unbalanced.
        $this->assertSame(
            substr_count($expression, '{'),
            substr_count($expression, '}'),
            'The x-data braces do not balance, so it cannot be a complete object literal.',
        );
    }

    /**
     * A-Z, and in the reader's language.
     *
     * The per-lesion list came out in whatever order the join returned, so the
     * three names on the tile — and the dialog's list, which has the same source —
     * could differ between two renders of the same page. Sorted on the localised
     * name: `orderBy('name')` sorts the id-keyed lookup map, which throws its own
     * order away, and the canonical name would leave the Arabic page unsorted.
     */
    #[Test]
    public function a_lesions_diseases_are_ordered_alphabetically(): void
    {
        $this->lesionWithDiseases('Liver lesions', 'poultry', ['Newcastle', 'Botulism', 'Aflatoxicosis']);

        $diseases = app(LesionAtlasService::class)
            ->lesionsPaginator('poultry')
            ->getCollection()
            ->first()['diseases'];

        $this->assertSame(
            ['Aflatoxicosis', 'Botulism', 'Newcastle'],
            $diseases->map(fn (Disease $disease): string => $disease->localized_name)->all(),
        );
    }

    /**
     * The chip says how many diseases it is hiding, and the dialog's close
     * button says what it is — in the reader's language.
     *
     * Both are asserted in Arabic because each is built from a translation, and
     * the English tests above cannot catch either falling back to the key. The
     * close label especially: `<x-modal>` is shared, and a shared component that
     * reaches into `poultry.*` for its own button label reads in English the
     * moment anybody mounts it on another part. Its `label` prop is required for
     * that reason, and this is the assertion that keeps it wired.
     *
     * The lesion name stays Latin deliberately: `Str::slug()` strips Arabic to an
     * empty string, so an Arabic lesion name would push an empty `canonical_name`
     * into the fixtures and fail on a column that has nothing to do with this.
     */
    #[Test]
    public function the_rest_of_a_lesions_diseases_are_readable_in_arabic(): void
    {
        app()->setLocale('ar');

        $this->lesionWithDiseases('Liver lesions', 'poultry', [
            'أفلاتوكسينوزيس', 'تسمم غذائي', 'كوليرا', 'نيوكاسل',
        ]);

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee('+1')
            ->assertSee(__('poultry.atlas.all_diseases', ['count' => 4], 'ar'), false)
            ->assertSee(__('poultry.atlas.close', [], 'ar'), false)
            ->assertSee(__('poultry.atlas.caused_by', [], 'ar'), false);
    }

    // -------------------------------------------------------- the photographs

    /**
     * Phase 9 planned photographs behind a `media_assets` table this application
     * does not have. The placeholder is asserted because a broken image icon reads
     * as a bug where an empty frame reads as "not yet".
     */
    #[Test]
    public function a_lesion_with_no_photograph_renders_a_placeholder_not_a_broken_image(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $this->lesion('Liver lesions', 'poultry', $disease);

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee(__('poultry.atlas.image_soon'))
            ->assertDontSee('<img', false);
    }

    /**
     * The photograph replaces the placeholder — not alongside it. Asserting both
     * `<img` and `image_soon` at once is the only way to catch a tile that draws
     * the frame *and* the caption, which reads as an upload that half-worked.
     */
    #[Test]
    public function a_lesion_with_a_photograph_renders_it_instead_of_the_placeholder(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $lesion = $this->lesion('Liver lesions', 'poultry', $disease);
        $image = FindingImage::factory()->create([
            'finding_id' => $lesion->id,
            'image' => 'findings/'.$lesion->id.'/liver.jpg',
            'caption' => 'Pale, mottled liver',
        ]);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertStringContainsString(
            Storage::disk('public')->url($image->image),
            $html,
        );
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringNotContainsString(__('poultry.atlas.image_soon'), $html);
    }

    /**
     * `alt` names the lesion so a screen reader says *which* lesion the picture
     * belongs to; the caption goes in `title`, where it describes it. Same string
     * in both would do neither job — and an `alt` of "liver photograph" is worse
     * than none when the lesion name is already on the tile.
     */
    #[Test]
    public function a_photograph_is_alt_texted_with_the_lesion_not_the_filename(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $lesion = $this->lesion('Liver lesions', 'poultry', $disease);
        FindingImage::factory()->create([
            'finding_id' => $lesion->id,
            'image' => 'findings/'.$lesion->id.'/liver.jpg',
            'caption' => 'Pale, mottled liver',
        ]);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertStringContainsString('alt="Liver lesions"', $html);
        $this->assertStringContainsString('title="Pale, mottled liver"', $html);
    }

    /**
     * A caption is required in neither language, so a tile photographed with
     * nothing written must render no `title` attribute at all rather than an
     * empty one — which is what a naive `title="{{ $caption }}"` emits.
     */
    #[Test]
    public function an_uncaptioned_photograph_renders_no_title_attribute(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $lesion = $this->lesion('Liver lesions', 'poultry', $disease);
        FindingImage::factory()->uncaptioned()->create([
            'finding_id' => $lesion->id,
        ]);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertStringContainsString('<img', $html);
        $this->assertStringNotContainsString('title=""', $html);
    }

    /**
     * The first row by `sort_order` is the one on the tile, so the ordering
     * Filament's drag handle writes has to decide what the reader sees. Asserted
     * because `Finding::images()` carries an `orderBy` for exactly this reason
     * and nothing else on the page depends on it.
     */
    #[Test]
    public function the_tile_shows_the_lowest_sort_order_photograph(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $lesion = $this->lesion('Liver lesions', 'poultry', $disease);
        FindingImage::factory()->create(['finding_id' => $lesion->id, 'image' => 'findings/second.jpg', 'sort_order' => 5]);
        FindingImage::factory()->create(['finding_id' => $lesion->id, 'image' => 'findings/first.jpg', 'sort_order' => 1]);

        $html = Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->html();

        $this->assertStringContainsString('findings/first.jpg', $html);
        $this->assertStringNotContainsString('findings/second.jpg', $html);
    }

    /**
     * Only one photograph per tile. Rendering all of them would mean N+1 `url()`
     * calls and a tile whose height depends on its upload history.
     */
    #[Test]
    public function only_the_first_photograph_of_many_reaches_the_page(): void
    {
        $disease = $this->disease('Aflatoxicosis', 'poultry');
        $lesion = $this->lesion('Liver lesions', 'poultry', $disease);
        FindingImage::factory()->create(['finding_id' => $lesion->id, 'image' => 'findings/first.jpg', 'sort_order' => 1]);
        FindingImage::factory()->create(['finding_id' => $lesion->id, 'image' => 'findings/second.jpg', 'sort_order' => 2]);

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee('findings/first.jpg')
            ->assertDontSee('findings/second.jpg');
    }

    // ------------------------------------------------------------- query budget

    /**
     * 640 lesions, each with a disease list and an organ, is exactly the shape
     * that turns into an N+1 if the component is left to lazy-load. The budget is
     * asserted rather than the count so a query can be added deliberately.
     */
    #[Test]
    public function the_page_stays_within_its_query_budget(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);

        foreach (range(1, 12) as $i) {
            $disease = $this->disease("Disease {$i}", 'poultry');
            $this->lesion(($i % 2 === 0 ? 'Liver' : 'Post-mortem')." lesion {$i}", 'poultry', $disease);
        }

        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        $small = $queries;
        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->assertOk();
        $atTwelve = $queries;

        // Twelve times the data must not cost twelve times the queries. This is
        // the assertion that actually proves there is no N+1; the ceiling below
        // only stops one being added later.
        foreach (range(13, 48) as $i) {
            $this->lesion(($i % 2 === 0 ? 'Liver' : 'Post-mortem')." lesion {$i}", 'poultry');
        }

        $queries = 0;
        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])->assertOk();
        $atFortyEight = $queries;

        $this->assertLessThanOrEqual(
            $atTwelve - $small,
            $atFortyEight,
            'Rendering 12 lesions took '.($atTwelve - $small)." queries; rendering 48 took {$atFortyEight}. The count must not grow with the lesion count.",
        );

        // 15 queries for the page: the paginated rows with their diseases, the
        // whole-atlas section counts, the letter rail, the filter chips and the two
        // hero totals. The ceiling stops a per-lesion query being added silently.
        $this->assertLessThanOrEqual(
            26,
            $queries,
            "The atlas issued {$queries} queries. A lesion's diseases and organ are loaded eagerly; nothing here should grow with the lesion count.",
        );
    }

    // ---------------------------------------------------------------- pagination

    /**
     * Paginated by name, no row on one page and the next.
     *
     * The slice is the thing most likely to be off by one — `forPage` on a builder
     * that already has a `limit`, or a page cast that arrives as a string — and the
     * symptom is a duplicated or skipped lesion that nobody notices without
     * counting.
     */
    #[Test]
    public function each_page_shows_the_next_slice_with_no_overlap(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);

        // More than one page, sorted the way the page sorts.
        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Lesion %02d', $i), 'poultry');
        }

        $first = $this->namesOnPage(1);
        $second = $this->namesOnPage(2);

        $this->assertCount(LesionAtlasService::PER_PAGE, $first);
        $this->assertCount(12, $second, '60 lesions at 48 per page leaves 12 on the second.');
        $this->assertEmpty(array_intersect($first, $second), 'A lesion appeared on both pages.');
    }

    /**
     * A section header reports the whole-atlas count, not the rows beside it.
     *
     * Without this the header reads "Digestive 48" above four pages of Digestive,
     * and the reader cannot tell which number is real.
     */
    #[Test]
    public function a_section_header_reports_every_page_not_just_this_one(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);

        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Liver %02d', $i), 'poultry');
        }

        $component = Livewire::withQueryParams(['page' => 1])
            ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee('60', false);

        $section = $component->instance()->sections->get('digestive');

        $this->assertSame(60, $section['count'], 'The header must count all 60, not the 48 on this page.');
        $this->assertSame(LesionAtlasService::PER_PAGE, $section['pageCount']);
    }

    /**
     * The specific failure a paginated group-by produces: rows silently lost
     * because the grouping ran over one page and the bucket's own tally no longer
     * matched the filtered total.
     */
    #[Test]
    public function the_unclassified_bucket_survives_pagination(): void
    {
        $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', BodySystem::first());

        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Post-mortem %02d', $i), 'poultry');
        }

        $component = Livewire::withQueryParams(['page' => 2])
            ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->instance();

        $this->assertSame(
            60,
            $component->sectionCounts[LesionAtlasService::UNCLASSIFIED_KEY],
        );
        $this->assertSame(
            60,
            $component->sections->get(LesionAtlasService::UNCLASSIFIED_KEY)['count'],
            'The bucket header must still report all 60 on the last page.',
        );
        $this->assertCount(12, $component->lesions->items());
    }

    /**
     * The counts and the paginator must agree, or a lesion is counted in a header
     * and never rendered anywhere.
     */
    #[Test]
    public function the_section_counts_sum_to_the_paginated_total(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);

        foreach (range(1, 70) as $i) {
            $this->lesion($i % 3 === 0 ? "Post-mortem {$i}" : sprintf('Liver %02d', $i), 'poultry');
        }

        $component = Livewire::withQueryParams(['page' => 2])
            ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->instance();

        $this->assertSame(
            $component->lesions->total(),
            array_sum($component->sectionCounts),
            'The headers account for every lesion the pager says exists.',
        );
    }

    /**
     * Page 7 of everything is usually past the end of a filtered set, and the
     * result is a blank page that reads as "the filter found nothing".
     */
    #[Test]
    public function filtering_from_a_later_page_returns_to_page_one(): void
    {
        $system = $this->bodySystem('Digestive', 'digestive');
        $this->organ('Liver', 'liver', $system);

        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Liver %02d', $i), 'poultry');
        }
        $this->lesion('Cloacal prolapse', 'poultry');

        // The trait keeps the page in `$paginators`, not a `$page` property, so
        // the assertion reads it from there. Asserting a `page` property here would
        // pass while the paginator stayed on the old page.
        $cases = [
            fn (Testable $c) => $c->set('search', 'Cloacal'),
            fn (Testable $c) => $c->call('selectSystem', 'digestive'),
            fn (Testable $c) => $c->call('selectLetter', 'L'),
            fn (Testable $c) => $c->call('clearFilters'),
        ];

        foreach ($cases as $index => $act) {
            $component = $act(
                Livewire::withQueryParams(['page' => 2])
                    ->test('knowledge.lesion-atlas', ['part' => 'poultry']),
            );

            $this->assertSame(
                1,
                $component->instance()->getPage(),
                "Filter change #{$index} left the paginator on page 2.",
            );
        }
    }

    /**
     * A letter rail built from the current page hides every letter whose lesions
     * happen to fall on pages 2 and 3 — which for a sparse letter is the normal
     * case, not an edge one.
     */
    #[Test]
    public function the_letter_rail_offers_every_letter_while_paginated(): void
    {
        $this->lesion('Liver lesions', 'poultry');

        // 60 lesions sort after "Liver lesions", so "L" exists on page 1 only.
        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Nodule %02d', $i), 'poultry');
        }

        $this->assertSame(
            ['L', 'N'],
            Livewire::withQueryParams(['page' => 2])
                ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
                ->instance()
                ->letters,
        );
    }

    #[Test]
    public function the_pager_renders_when_there_is_more_than_one_page(): void
    {
        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Lesion %02d', $i), 'poultry');
        }

        Livewire::withQueryParams(['page' => 1])
            ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertSee(__('poultry.atlas.next_page'))
            ->assertSee(__('poultry.atlas.of'));
    }

    /**
     * Every pager link must resolve to the atlas.
     *
     * A paginator that reuses the current request's path emits a *relative* href
     * inside a Livewire render, and `wire:navigate` then resolves it against
     * `/poultry/atlas` into `/poultry/poultry/atlas?page=2`. That 404s on every
     * page after the first, and it looks like a routing problem rather than a
     * link problem, which is why it is worth pinning.
     */
    #[Test]
    public function every_pager_link_resolves_to_the_atlas(): void
    {
        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Lesion %02d', $i), 'poultry');
        }

        $html = Livewire::withQueryParams(['page' => 1])
            ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->html();

        preg_match_all('/href="([^"]*[?&]page=[^"]*)"/', $html, $matches);

        $this->assertNotEmpty($matches[1], 'The page paginates, so it must render pager links.');

        foreach ($matches[1] as $href) {
            $path = parse_url($href, PHP_URL_PATH) ?? '';

            $this->assertSame(
                '/poultry/atlas',
                $path,
                "Pager link [{$href}] does not resolve to the atlas. A relative href here becomes /poultry/poultry/atlas under wire:navigate and 404s.",
            );
        }
    }

    /**
     * Following every rendered pager link must return the page, not a 404.
     */
    #[Test]
    public function every_pager_link_loads_a_page(): void
    {
        foreach (range(1, 60) as $i) {
            $this->lesion(sprintf('Lesion %02d', $i), 'poultry');
        }

        $html = Livewire::withQueryParams(['page' => 1])
            ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->html();

        preg_match_all('/href="([^"]*[?&]page=[^"]*)"/', $html, $matches);

        foreach (array_unique($matches[1]) as $href) {
            $this->get($href)->assertOk();
        }
    }

    #[Test]
    public function the_pager_is_absent_when_everything_fits_on_one_page(): void
    {
        $this->lesion('Liver lesions', 'poultry');

        Livewire::test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->assertDontSee(__('poultry.atlas.next_page'));
    }

    /**
     * The lesion names rendered on a given page, read the way a pager link asks
     * for them.
     *
     * @return array<int, string>
     */
    private function namesOnPage(int $page): array
    {
        return Livewire::withQueryParams(['page' => $page])
            ->test('knowledge.lesion-atlas', ['part' => 'poultry'])
            ->instance()
            ->lesions
            ->getCollection()
            ->pluck('name')
            ->all();
    }

    // ------------------------------------------------------------------- helpers

    /**
     * A lesion, carried by a clinical sign, carried by a disease in the part.
     *
     * The sign is built with its anatomy and modifier left null on purpose. Both
     * are nullable and both default to `::factory()` in `ClinicalSignFactory`, so
     * leaving them alone would create an anatomical structure per lesion — which
     * is data the atlas would then group by, and Faker's unique pool runs dry at
     * around fifty rows.
     */
    private function lesion(string $name, string $domain, ?Disease $disease = null): Finding
    {
        $finding = Finding::factory()->create([
            'canonical_name' => str($name)->slug('_')->toString(),
            'display_name' => $name,
            'ontology_type' => OntologyType::Lesion,
        ]);

        $sign = ClinicalSign::factory()->create([
            'finding_id' => $finding->id,
            'anatomical_structure_id' => null,
            'modifier_id' => null,
            'canonical_name' => $finding->canonical_name.'_sign',
            'display_name' => $name,
        ]);

        ($disease ?? $this->disease("Cause of {$name}", $domain))->clinicalSigns()->attach($sign);

        return $finding;
    }

    /**
     * Created through the model rather than the factory.
     *
     * `DiseaseFactory::definition()` draws its name from a Faker `unique()` pool
     * of thirty veterinary diseases, and `named()` pins the name *after* that
     * pool has already been drawn from. A test that creates more than thirty
     * diseases therefore exhausts the pool and throws, which is what the query
     * budget test does when it scales the lesion count up to prove there is no
     * N+1. Nothing about a disease is randomised here anyway.
     */
    /**
     * One lesion carried by several diseases.
     *
     * The atlas reaches a disease through the clinical sign that reports the
     * lesion, and `lesion()` builds exactly one sign and hands back only the
     * finding. So the extra diseases are attached to that sign afterwards rather
     * than to further signs — a second sign per disease would make them separate
     * lesions and each tile would show one disease, which is the case under test
     * everywhere else on this file.
     *
     * @param  array<int, string>  $diseaseNames
     */
    private function lesionWithDiseases(string $name, string $domain, array $diseaseNames): Finding
    {
        $lesion = $this->lesion($name, $domain, $this->disease($diseaseNames[0], $domain));
        $sign = $lesion->clinicalSigns()->firstOrFail();

        foreach (array_slice($diseaseNames, 1) as $diseaseName) {
            $this->disease($diseaseName, $domain)->clinicalSigns()->attach($sign);
        }

        return $lesion;
    }

    private function disease(string $name, string $domain): Disease
    {
        return Disease::query()->create([
            'name' => $name,
            'name_ar' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'domain' => $domain,
            'is_active' => true,
            'is_zoonotic' => false,
            'is_internal' => false,
        ]);
    }

    private function bodySystem(string $name, string $canonical): BodySystem
    {
        return BodySystem::factory()->create([
            'canonical_name' => $canonical,
            'display_name' => $name,
        ]);
    }

    private function organ(string $name, string $canonical, BodySystem $system): AnatomicalStructure
    {
        return AnatomicalStructure::factory()->create([
            'canonical_name' => $canonical,
            'display_name' => $name,
            'body_system_id' => $system->id,
            'domain' => 'poultry',
        ]);
    }
}
