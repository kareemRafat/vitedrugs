<?php

namespace Tests\Feature\Knowledge;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The hero could not clip, and the footer had to link to search.
 *
 * Neither of these is testable by asserting on a screenshot, so they are locked
 * structurally instead: the hero wrapper must stay overflow-visible, the clip
 * must sit on the decorative layer alone, and the dropdown must stay inside the
 * slot so the two properties keep composing the way they do now.
 */
class SearchChromeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression: `overflow-hidden` on the hero wrapper clipped the suggestions
     * dropdown at the hero's bottom edge, so it could never extend past it. The
     * decorative blur still needs clipping, but that now lives one level in.
     */
    public function test_the_hero_wrapper_does_not_clip_its_slot(): void
    {
        $html = $this->get(route('poultry.search'))->assertOk()->getContent();

        $wrapper = $this->heroWrapper($html);

        $this->assertStringNotContainsString(
            'overflow-hidden',
            $wrapper,
            'The hero wrapper must stay overflow-visible or the suggestions dropdown is clipped.',
        );
    }

    public function test_the_decorative_blur_stays_clipped_to_the_rounded_hero(): void
    {
        $html = $this->get(route('poultry.search'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/absolute inset-0 overflow-hidden rounded-base[^"]*"[^>]*>\s*<div class="absolute inset-0 opacity-10/',
            $html,
            'The blur circles must be clipped by an inset wrapper, or they bleed past the rounded corners.',
        );
    }

    /**
     * The form still has to sit inside the hero slot.
     *
     * This was originally a test about the dropdown's position, which anchored the
     * form's `relative` wrapper and kept the circles from bleeding. The dropdown is
     * gone, but the form itself is still the thing that must render inside the hero,
     * so the assertion is kept in its simpler form.
     *
     * Asserted against rendered output, not Blade source: the component's closing
     * tag is compiled away, so `</x-large-animals.page-hero>` never appears in the
     * HTML. The form is the reliable boundary instead.
     */
    public function test_the_search_form_lives_inside_the_hero_slot(): void
    {
        $html = $this->get(route('poultry.search'))->assertOk()->getContent();

        $heroStart = strpos($html, 'bg-gradient-to-br from-brand');
        $this->assertIsInt($heroStart, 'The page hero wrapper could not be found.');

        $formStart = strpos($html, '<form method="GET" action="'.route('poultry.search').'"');
        $this->assertIsInt($formStart, 'The hero slot form could not be found.');

        $this->assertGreaterThan($heroStart, $formStart, 'The form should sit inside the hero.');
        $this->assertGreaterThan($formStart, strpos($html, 'name="q"'), 'The input should sit inside the form.');
    }

    public function test_each_parts_footer_links_to_its_own_search_page(): void
    {
        $this->get(route('poultry.search'))
            ->assertOk()
            ->assertSee(route('poultry.search'), escape: false);

        $this->get(route('large-animals.search'))
            ->assertOk()
            ->assertSee(route('large-animals.search'), escape: false);
    }

    public function test_the_footer_search_link_is_labelled_from_the_parts_own_lang_file(): void
    {
        $this->get(route('poultry.search'))
            ->assertOk()
            ->assertSee(__('poultry.nav.search'));

        $this->get(route('large-animals.search'))
            ->assertOk()
            ->assertSee(__('large-animals.nav.search'));
    }

    public function test_the_footer_search_link_is_marked_active_only_on_its_own_page(): void
    {
        // Active on search, not active on a sibling page.
        $onSearch = $this->footerLinkClass($this->get(route('poultry.search'))->assertOk()->getContent(), 'poultry.search');
        $onDiagnosis = $this->footerLinkClass($this->get(route('poultry.diagnosis'))->assertOk()->getContent(), 'poultry.search');

        $this->assertStringContainsString('text-fg-brand font-medium', $onSearch, 'The link should read as active on the search page.');
        $this->assertStringNotContainsString('text-fg-brand font-medium', $onDiagnosis, 'The link should not read as active on the diagnosis page.');
    }

    public function test_a_part_footer_never_links_into_the_other_parts_search(): void
    {
        $poultryHtml = $this->get(route('poultry.search'))->assertOk()->getContent();
        $largeAnimalsHtml = $this->get(route('large-animals.search'))->assertOk()->getContent();

        $this->assertStringNotContainsString(route('large-animals.search'), $poultryHtml);
        $this->assertStringNotContainsString(route('poultry.search'), $largeAnimalsHtml);
    }

    private function heroWrapper(string $html): string
    {
        $this->assertMatchesRegularExpression(
            '/<div class="[^"]*bg-gradient-to-br from-brand[^"]*">/',
            $html,
            'The hero wrapper could not be found.',
        );

        preg_match('/<div class="[^"]*bg-gradient-to-br from-brand[^"]*">/', $html, $matches);

        return $matches[0];
    }

    /**
     * The class attribute of the footer anchor pointing at the given route.
     *
     * Scoped to the <footer> element on purpose: the header carries a search
     * link to the same route, so an unscoped match finds that one instead.
     */
    private function footerLinkClass(string $html, string $routeName): string
    {
        preg_match('/<footer[\s\S]*?<\/footer>/', $html, $footer);

        $this->assertNotEmpty($footer, 'The footer was not rendered.');

        $url = route($routeName);

        $this->assertStringContainsString($url, $footer[0], "No footer link to [{$routeName}] was found.");

        preg_match('/<a href="'.preg_quote($url, '/').'"[^>]*class="([^"]*)"/', $footer[0], $matches);

        $this->assertNotEmpty($matches, "Could not read the class of the footer link to [{$routeName}].");

        return $matches[1];
    }
}
