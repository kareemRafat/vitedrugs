<?php

namespace Tests\Feature\Knowledge;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The part-scoping contract for global search.
 *
 * This file exists because the search page this replaced ran one unscoped query
 * per model: `/large-animals/search` returned all 151 poultry diseases, and a
 * term like "dyspnea" rendered twice because large-animal and poultry each keep
 * their own `clinical_signs` row for it. Every test here is a case that page got
 * wrong.
 */
class SearchScopingTest extends TestCase
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

    public function test_a_poultry_disease_is_absent_from_large_animal_results(): void
    {
        $poultry = $this->disease('Coccidiosis', Disease::DOMAIN_POULTRY);
        $this->link($poultry, $this->chicken, []);

        $this->get(route('large-animals.search', ['q' => 'coccidiosis']))
            ->assertOk()
            ->assertDontSee($poultry->name);
    }

    public function test_a_large_animal_disease_is_absent_from_poultry_results(): void
    {
        $livestock = $this->disease('Bovine Respiratory Disease', Disease::DOMAIN_LARGE_ANIMAL);
        $this->link($livestock, $this->cow, []);

        $this->get(route('poultry.search', ['q' => 'bovine respiratory']))
            ->assertOk()
            ->assertDontSee($livestock->name);
    }

    /**
     * The exact duplication seen live: `dyspnea` exists once per part, and the
     * old large-animals page rendered both rows.
     */
    public function test_a_shared_term_returns_each_part_only_its_own_clinical_sign(): void
    {
        $poultrySign = ClinicalSign::factory()->create([
            'canonical_name' => 'dyspnea',
            'display_name' => 'Dyspnea (poultry)',
        ]);

        $livestockSign = ClinicalSign::factory()->create([
            'canonical_name' => 'dyspnea',
            'display_name' => 'Dyspnea (livestock)',
        ]);

        $this->link($this->disease('Coccidiosis', Disease::DOMAIN_POULTRY), $this->chicken, [$poultrySign]);
        $this->link($this->disease('Bovine Respiratory Disease', Disease::DOMAIN_LARGE_ANIMAL), $this->cow, [$livestockSign]);

        $largeAnimals = $this->get(route('large-animals.search', ['q' => 'dyspnea']))
            ->assertOk()
            ->assertSee('Dyspnea (livestock)')
            ->assertDontSee('Dyspnea (poultry)');

        $this->get(route('poultry.search', ['q' => 'dyspnea']))
            ->assertOk()
            ->assertSee('Dyspnea (poultry)')
            ->assertDontSee('Dyspnea (livestock)');
    }

    public function test_host_species_are_restricted_to_their_own_part(): void
    {
        $this->get(route('poultry.search', ['q' => 'ick']))
            ->assertOk()
            ->assertSee($this->chicken->display_name)
            ->assertDontSee($this->cow->display_name);

        $this->get(route('large-animals.search', ['q' => 'attle']))
            ->assertOk()
            ->assertSee($this->cow->display_name)
            ->assertDontSee($this->chicken->display_name);
    }

    public function test_products_are_scoped_through_the_diseases_they_treat(): void
    {
        $poultryProduct = $this->product('Alpha Poultry Remedy', $this->disease('Coccidiosis', Disease::DOMAIN_POULTRY));
        $this->product('Beta Livestock Remedy', $this->disease('Bovine Respiratory Disease', Disease::DOMAIN_LARGE_ANIMAL));

        $this->get(route('poultry.search', ['q' => 'Remedy']))
            ->assertOk()
            ->assertSee($poultryProduct->trade_name)
            ->assertDontSee('Beta Livestock Remedy');

        $this->get(route('large-animals.search', ['q' => 'Remedy']))
            ->assertOk()
            ->assertSee('Beta Livestock Remedy')
            ->assertDontSee($poultryProduct->trade_name);
    }

    /**
     * `diseases.is_active` was never filtered by the old page, so deactivated
     * diseases stayed findable.
     */
    public function test_an_inactive_disease_is_not_findable(): void
    {
        $inactive = $this->disease('Coccidiosis', Disease::DOMAIN_POULTRY, ['is_active' => false]);
        $this->link($inactive, $this->chicken, []);

        $this->get(route('poultry.search', ['q' => 'coccidiosis']))
            ->assertOk()
            ->assertDontSee($inactive->name);
    }

    private function disease(string $name, string $domain, array $attributes = []): Disease
    {
        return Disease::factory()->named($name)->create($attributes + ['domain' => $domain]);
    }

    private function product(string $name, Disease $disease)
    {
        $product = Product::factory()->create(['trade_name' => $name]);
        $disease->products()->attach($product->id, ['sort_order' => 0]);

        return $product;
    }

    /**
     * @param  array<int, ClinicalSign>  $signs
     */
    private function link(Disease $disease, HostSpecies $species, array $signs): void
    {
        $disease->hostSpecies()->attach($species->id, ['is_primary_host' => true]);

        foreach ($signs as $sign) {
            $disease->clinicalSigns()->attach($sign->id, ['weight' => 5]);
        }
    }
}
