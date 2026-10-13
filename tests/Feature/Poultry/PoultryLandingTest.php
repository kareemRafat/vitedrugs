<?php

namespace Tests\Feature\Poultry;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PoultryLandingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_in_english(): void
    {
        $this->get('/poultry')
            ->assertOk()
            ->assertSee('The Poultry Knowledge Hub');
    }

    #[Test]
    public function it_renders_in_arabic(): void
    {
        // The locale prefix is baked in when routes are registered, so the
        // established way to exercise Arabic is to switch the app locale.
        app()->setLocale('ar');

        $response = $this->get('/poultry')->assertOk();

        $response->assertSee('مركز معرفة الدواجن');
        $response->assertSee('ابدأ التشخيص', escape: false);
        $response->assertSee('dir="rtl"', escape: false);
    }

    #[Test]
    public function it_counts_only_poultry_diseases(): void
    {
        $this->poultryDisease('Newcastle Disease');
        $this->poultryDisease('Coccidiosis');
        Disease::create([
            'name' => 'Strangles',
            'slug' => 'strangles',
            'domain' => Disease::DOMAIN_LARGE_ANIMAL,
            'is_active' => true,
        ]);

        $stats = $this->stats();

        $this->assertSame(2, $stats['diseases'], 'A large-animal disease must not be counted.');
    }

    #[Test]
    public function it_ignores_inactive_poultry_diseases(): void
    {
        $this->poultryDisease('Newcastle Disease');
        $this->poultryDisease('Retired Disease', ['is_active' => false]);

        $this->assertSame(1, $this->stats()['diseases']);
    }

    #[Test]
    public function it_counts_findings_and_lesions_only_when_linked_to_a_poultry_disease(): void
    {
        $poultry = $this->poultryDisease('Newcastle Disease');
        $largeAnimal = Disease::create([
            'name' => 'Strangles',
            'slug' => 'strangles',
            'domain' => Disease::DOMAIN_LARGE_ANIMAL,
        ]);

        // One sign and one lesion reachable from poultry, one of each that is not.
        $poultrySign = $this->sign('ruffled feathers', 'sign');
        $poultryLesion = $this->sign('enlarged spleen', 'lesion');
        $largeSign = $this->sign('nasal discharge', 'sign');

        $this->link($poultry, $poultrySign);
        $this->link($poultry, $poultryLesion);
        $this->link($largeAnimal, $largeSign);

        $stats = $this->stats();

        $this->assertSame(2, $stats['findings'], 'The large-animal-only finding must not be counted.');
        $this->assertSame(1, $stats['lesions'], 'Only post-mortem findings count as lesions.');
    }

    #[Test]
    public function it_counts_bird_species_by_taxonomy_not_by_disease_link(): void
    {
        HostSpecies::create([
            'canonical_name' => 'chicken',
            'display_name' => 'Chicken',
            'taxonomy_group' => 'poultry',
        ]);

        HostSpecies::create([
            'canonical_name' => 'cattle',
            'display_name' => 'Cattle',
            'taxonomy_group' => 'ruminant',
        ]);

        $this->assertSame(1, $this->stats()['species'], 'Cattle is not a bird, even if a disease links it.');
    }

    #[Test]
    public function every_card_links_to_a_route_that_exists(): void
    {
        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->map(fn ($route): string => '/'.ltrim($route->uri(), '/'))
            ->all();

        $response = $this->get('/poultry')->assertOk();
        $html = $response->getContent();

        preg_match_all('#href="(https?://[^"]+|/[^"]*)"#', $html, $matches);

        $internal = collect($matches[1])
            // parse_url returns null for an absolute URL with no path, such as
            // the site-root link in the layout chrome.
            ->map(fn (string $url): ?string => parse_url($url, PHP_URL_PATH))
            ->filter(fn (?string $path): bool => filled($path))
            // Static assets in the layout chrome are served from public/, not by
            // a route, so they are not this page's concern.
            ->reject(fn (string $path): bool => preg_match('#\.(png|jpe?g|svg|ico|css|js|webp|woff2?)$#i', $path) === 1)
            ->map(fn (string $path): string => '/'.ltrim(preg_replace('#^/(en|ar)#', '', $path), '/'))
            ->unique();

        $this->assertGreaterThan(0, $internal->count(), 'The hub should link somewhere.');

        foreach ($internal as $path) {
            $this->assertContains(
                $path,
                $routes,
                "The hub links to [{$path}] but no such route is registered.",
            );
        }
    }

    #[Test]
    public function it_hides_cards_for_sections_that_are_not_built_yet(): void
    {
        $html = $this->get('/poultry')->assertOk()->getContent();

        // Phase 9 (the lesion atlas) has not landed. Its route must not appear.
        // Phases 6, 7, 8 and 10 have landed and are asserted below.
        $this->assertStringNotContainsString('poultry/atlas', $html, 'poultry/atlas is not built yet and must not be linked.');
    }

    #[Test]
    public function it_links_to_the_body_map_now_that_it_is_built(): void
    {
        $html = $this->get('/poultry')->assertOk()->getContent();

        $this->assertStringContainsString(route('poultry.body-map'), $html);
        $this->assertStringContainsString(__('poultry.landing.services.body_map.title'), $html);
    }

    #[Test]
    public function it_links_to_the_drug_catalogue_now_that_it_is_built(): void
    {
        $html = $this->get('/poultry')->assertOk()->getContent();

        $this->assertStringContainsString(route('poultry.products.index'), $html);
        $this->assertStringContainsString(__('poultry.landing.services.products.title'), $html);
    }

    public function it_links_to_the_comparison_now_that_it_is_built(): void
    {
        $html = $this->get('/poultry')->assertOk()->getContent();

        $this->assertStringContainsString(route('poultry.comparison'), $html);
    }

    public function it_links_to_the_global_search_now_that_it_is_built(): void
    {
        $html = $this->get('/poultry')->assertOk()->getContent();

        $this->assertStringContainsString(route('poultry.search'), $html);
        $this->assertStringContainsString(__('poultry.landing.services.search.title'), $html);
    }

    #[Test]
    public function it_keeps_the_tools_inside_the_poultry_part(): void
    {
        $html = $this->get('/poultry')->assertOk()->getContent();

        // The cards used to point at /large-animals, which re-themed the whole
        // page and left no way back. Every tool link now has to stay under
        // /poultry.
        $this->assertStringContainsString(route('poultry.diagnosis'), $html);
        $this->assertStringContainsString(route('poultry.filter'), $html);
        $this->assertStringNotContainsString('/large-animals/', $html);
    }

    #[Test]
    public function it_does_not_still_claim_the_tools_are_shared(): void
    {
        $this->get('/poultry')
            ->assertOk()
            ->assertDontSee('shared with the Large Animals section');
    }

    /**
     * @return array<string, int>
     */
    private function stats(): array
    {
        $response = $this->get('/poultry')->assertOk();

        preg_match_all('/tabular-nums leading-none">([\d,]+)</', $response->getContent(), $matches);

        $values = array_map(fn (string $value): int => (int) str_replace(',', '', $value), $matches[1]);

        return array_combine(['diseases', 'findings', 'lesions', 'species'], $values);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function poultryDisease(string $name, array $attributes = []): Disease
    {
        return Disease::create(array_merge([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'domain' => Disease::DOMAIN_POULTRY,
            'is_active' => true,
        ], $attributes));
    }

    private function sign(string $canonicalName, string $ontologyType): Finding
    {
        return Finding::create([
            'canonical_name' => $canonicalName,
            'display_name' => ucfirst($canonicalName),
            'slug' => str($canonicalName)->slug()->toString(),
            'ontology_type' => $ontologyType,
            'category' => $ontologyType === 'lesion' ? 'postmortem' : 'clinical',
        ]);
    }

    private function link(Disease $disease, Finding $finding): void
    {
        $sign = ClinicalSign::create([
            'canonical_name' => $finding->canonical_name,
            'display_name' => $finding->display_name,
            'finding_id' => $finding->id,
        ]);

        DB::table('disease_clinical_sign')->insert([
            'disease_id' => $disease->id,
            'clinical_sign_id' => $sign->id,
            'weight' => 5,
            'is_specific' => false,
            'is_required' => false,
            'is_pathognomonic' => false,
        ]);
    }
}
