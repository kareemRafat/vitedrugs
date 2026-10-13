<?php

namespace Tests\Feature\Knowledge;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Search results link to the diagnosis and filter tools with the matched row in
 * the query string. Those links used to land on a completely blank tool, so
 * clicking a clinical sign or a host species out of the results page did nothing
 * visible.
 *
 * A clinical sign has its own page now — see `SignPageTest` — so this file covers
 * the filter prefill it leaves behind, which stays reachable because the sign
 * page offers it as a hand-off.
 *
 * Every case here is about a parameter that came from a URL, so the interesting
 * behaviour is the rejection: an id from the other part, a deleted id, or plain
 * rubbish has to degrade to an ordinary empty tool rather than an error page.
 */
class SearchPrefillTest extends TestCase
{
    use RefreshDatabase;

    private HostSpecies $chicken;

    private HostSpecies $cow;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chicken = HostSpecies::factory()->poultry()->create([
            'canonical_name' => 'chicken',
            'display_name' => 'Chicken',
        ]);

        $this->cow = HostSpecies::factory()->largeAnimals()->create([
            'canonical_name' => 'cattle',
            'display_name' => 'Cattle',
        ]);
    }

    public function test_a_clinical_sign_result_prefills_the_diagnosis_form(): void
    {
        $sign = $this->sign('Drooping wings', 'poultry');

        $this->get(route('poultry.diagnosis', ['sign' => $sign->id]))
            ->assertOk()
            ->assertSee('Drooping wings')
            ->assertSee($this->seededSignPayload($sign->id, 'Drooping wings'));
    }

    public function test_a_sign_reachable_from_both_domains_is_offered_on_either_tool(): void
    {
        $sign = $this->sign('Nasal discharge', ['poultry', 'large-animals']);

        foreach (['poultry', 'large-animals'] as $part) {
            $this->get(route("{$part}.diagnosis", ['sign' => $sign->id]))
                ->assertOk()
                ->assertSee($this->seededSignPayload($sign->id, 'Nasal discharge'));
        }
    }

    public function test_a_sign_from_the_other_part_is_ignored(): void
    {
        $poultryOnly = $this->sign('Poultry-only sign', 'poultry');

        $this->get(route('large-animals.diagnosis', ['sign' => $poultryOnly->id]))
            ->assertOk()
            ->assertDontSee('Poultry-only sign')
            ->assertDontSee($this->seededSignPayload($poultryOnly->id, 'Poultry-only sign'));

        $largeOnly = $this->sign('Large-only sign', 'large-animals');

        $this->get(route('poultry.diagnosis', ['sign' => $largeOnly->id]))
            ->assertOk()
            ->assertDontSee('Large-only sign');
    }

    public function test_a_host_species_result_preselects_the_filter(): void
    {
        $this->get(route('poultry.filter', ['host_species_id' => $this->chicken->id]))
            ->assertOk()
            ->assertSee('value="'.$this->chicken->id.'" selected', escape: false);
    }

    public function test_a_host_species_result_preselects_the_diagnosis_species(): void
    {
        $this->get(route('poultry.diagnosis', ['host_species_id' => $this->chicken->id]))
            ->assertOk()
            ->assertSee('value="'.$this->chicken->id.'" selected', escape: false);
    }

    public function test_a_species_from_the_other_part_is_ignored(): void
    {
        $this->get(route('poultry.filter', ['host_species_id' => $this->cow->id]))
            ->assertOk()
            ->assertDontSee('value="'.$this->cow->id.'" selected', escape: false);
    }

    /**
     * A stale bookmark or a hand-edited query string must not become a 404.
     *
     * @return array<string, array{string, string}>
     */
    public static function unusableParameterProvider(): array
    {
        return [
            'missing id' => ['sign', ''],
            'not a number' => ['sign', 'abc'],
            'decimal' => ['sign', '1.5'],
            'zero' => ['sign', '0'],
            'negative' => ['sign', '-4'],
            'unknown sign' => ['sign', '999999'],
            'unknown species' => ['host_species_id', '999999'],
        ];
    }

    #[DataProvider('unusableParameterProvider')]
    public function test_unusable_parameters_degrade_to_an_empty_tool(string $key, string $value): void
    {
        $this->get(route('poultry.diagnosis', [$key => $value]))->assertOk();
        $this->get(route('poultry.filter', [$key => $value]))->assertOk();
    }

    public function test_the_tools_still_work_without_any_parameters(): void
    {
        $this->get(route('poultry.diagnosis'))->assertOk();
        $this->get(route('poultry.filter'))->assertOk();
        $this->get(route('large-animals.diagnosis'))->assertOk();
        $this->get(route('large-animals.filter'))->assertOk();
    }

    /**
     * A host-species search result has to arrive carrying its parameter,
     * otherwise the preselect above is unreachable from the results page.
     *
     * A clinical sign no longer comes here at all — it has its own page now, and
     * that page offers the filter and diagnosis hand-offs itself. See SignPageTest.
     */
    public function test_search_result_links_carry_the_parameters_the_tools_expect(): void
    {
        $response = $this->get(route('poultry.search', ['q' => 'Chicken']))->assertOk();

        $this->assertStringContainsString(
            route('poultry.filter', ['host_species_id' => $this->chicken->id]),
            $response->getContent(),
        );
    }

    /**
     * The filter tool's picker is Alpine state, so a pre-selected sign has to be
     * seeded into it. Seeded into `tokens` rather than rendered separately, or the
     * sign would appear on screen but never post with the form.
     */
    public function test_a_clinical_sign_result_prefills_the_filter_tool(): void
    {
        $sign = $this->sign('Drooping wings', 'poultry');

        $this->get(route('poultry.filter', ['sign' => $sign->id]))
            ->assertOk()
            ->assertSee('Drooping wings')
            ->assertSee($this->filterTokenPayload($sign->id, 'Drooping wings'), escape: false);
    }

    public function test_a_sign_reachable_from_both_domains_is_offered_on_either_filter(): void
    {
        $sign = $this->sign('Nasal discharge', ['poultry', 'large-animals']);

        foreach (['poultry', 'large-animals'] as $part) {
            $this->get(route("{$part}.filter", ['sign' => $sign->id]))
                ->assertOk()
                ->assertSee($this->filterTokenPayload($sign->id, 'Nasal discharge'), escape: false);
        }
    }

    public function test_a_sign_from_the_other_part_is_ignored_by_the_filter_tool(): void
    {
        $poultryOnly = $this->sign('Poultry-only sign', 'poultry');

        $this->get(route('large-animals.filter', ['sign' => $poultryOnly->id]))
            ->assertOk()
            ->assertDontSee($this->filterTokenPayload($poultryOnly->id, 'Poultry-only sign'), escape: false);

        $largeOnly = $this->sign('Large-only sign', 'large-animals');

        $this->get(route('poultry.filter', ['sign' => $largeOnly->id]))
            ->assertOk()
            ->assertDontSee($this->filterTokenPayload($largeOnly->id, 'Large-only sign'), escape: false);
    }

    /**
     * The prefilled sign has to survive being submitted, which means it reaches
     * the form as a real `clinical_signs[]` value rather than only as a chip.
     */
    public function test_a_prefilled_sign_can_be_submitted_and_returns_diseases(): void
    {
        $sign = $this->sign('Drooping wings', 'poultry');

        $this->post(route('poultry.filter.results.store'), [
            'clinical_signs' => [$sign->id],
        ])->assertRedirect();
    }

    /**
     * The slice of the Alpine seed payload that carries the prefilled sign in the
     * filter token picker.
     *
     * The visible chips are an Alpine template, so a token's id is never in the
     * server-rendered markup as an attribute — it reaches the browser only
     * through this JSON, which is what has to be asserted.
     *
     * @see resources/views/filter/index.blade.php
     */
    private function filterTokenPayload(int $id, string $title): string
    {
        // Blade's @js() wraps the payload in JSON.parse('...') and hex-escapes the
        // quotes as \u0022. Object braces are left alone.
        return str_replace(
            '"',
            '\u0022',
            json_encode([['id' => $id, 'title' => $title]], JSON_THROW_ON_ERROR),
        );
    }

    /**
     * The slice of the Alpine seed payload that carries the prefilled sign.
     *
     * The visible sign input is an Alpine binding (`:value="sign.id"`), so the id is
     * never in the server-rendered markup as an attribute — it reaches the browser
     * only through this JSON, which is what has to be asserted.
     *
     * @see resources/views/diagnosis/index.blade.php
     */
    private function seededSignPayload(int $id, string $label): string
    {
        // Blade's @js() wraps the payload in JSON.parse('...') and hex-escapes the
        // quotes as \u0022. Object braces are left alone.
        return str_replace(
            '"',
            '\u0022',
            json_encode(['sign' => ['id' => $id, 'label' => $label], 'speciesId' => null], JSON_THROW_ON_ERROR),
        );
    }

    private function sign(string $name, string|array $domains): ClinicalSign
    {
        $sign = ClinicalSign::factory()->create([
            'canonical_name' => strtolower($name),
            'display_name' => $name,
        ]);

        foreach ((array) $domains as $domain) {
            $disease = Disease::factory()->create(['name' => $name.' '.ucfirst($domain)]);

            $disease->update([
                'domain' => $domain === 'poultry'
                    ? Disease::DOMAIN_POULTRY
                    : Disease::DOMAIN_LARGE_ANIMAL,
            ]);

            $disease->clinicalSigns()->attach($sign->id, ['weight' => 5]);
        }

        return $sign;
    }
}
