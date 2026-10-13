<?php

namespace Tests\Feature\Poultry;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\HostSpecies;
use App\Services\LargeAnimals\FilterShareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\FilterToolTest;
use Tests\TestCase;

/**
 * @see FilterToolTest for the large-animal counterpart
 */
class PoultryFilterTest extends TestCase
{
    use RefreshDatabase;

    private HostSpecies $chicken;

    private ClinicalSign $sign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chicken = HostSpecies::factory()->poultry()->create(['display_name' => 'Chicken']);
        $this->sign = ClinicalSign::factory()->create(['display_name' => 'Drooping wings']);
    }

    private function poultryDisease(string $name = 'Newcastle Disease'): Disease
    {
        $disease = Disease::factory()->poultry()->named($name)->create();
        $disease->hostSpecies()->attach($this->chicken->id, ['is_primary_host' => true, 'susceptibility' => 'high']);
        $disease->clinicalSigns()->attach($this->sign->id, ['weight' => 5]);

        return $disease;
    }

    public function test_the_page_lists_only_poultry_species(): void
    {
        HostSpecies::factory()->largeAnimals()->create(['display_name' => 'Cattle']);

        $this->get(route('poultry.filter'))
            ->assertOk()
            ->assertSee($this->chicken->display_name)
            ->assertSee('Bird species')
            ->assertDontSee('Cattle');
    }

    public function test_it_matches_poultry_diseases(): void
    {
        $target = $this->poultryDisease();

        $response = $this->followingRedirects()->post(route('poultry.filter.results.store'), [
            'host_species_id' => $this->chicken->id,
            'clinical_signs' => [$this->sign->id],
        ]);

        $response->assertOk()->assertSee($target->name);
    }

    public function test_it_excludes_large_animal_diseases(): void
    {
        $this->poultryDisease();

        $cattle = HostSpecies::factory()->largeAnimals()->create();
        $livestock = Disease::factory()->largeAnimals()->named('Bovine Respiratory Disease')->create();
        $livestock->hostSpecies()->attach($cattle->id, ['is_primary_host' => true, 'susceptibility' => 'high']);
        $livestock->clinicalSigns()->attach($this->sign->id, ['weight' => 5]);

        $response = $this->followingRedirects()->post(route('poultry.filter.results.store'), [
            'clinical_signs' => [$this->sign->id],
        ]);

        $response->assertOk()->assertDontSee($livestock->name);
    }

    public function test_it_only_offers_etiologies_that_exist_in_this_part(): void
    {
        $bacterial = $this->poultryDisease();
        DiseaseClassification::factory()->create([
            'disease_id' => $bacterial->id,
            'etiology_type' => 'Bacterial disease',
        ]);

        $livestock = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();
        DiseaseClassification::factory()->create([
            'disease_id' => $livestock->id,
            'etiology_type' => 'Large Animal Only Etiology',
        ]);

        $this->get(route('poultry.filter'))
            ->assertOk()
            ->assertSee('Bacterial disease')
            ->assertDontSee('Large Animal Only Etiology');
    }

    public function test_it_rejects_a_large_animal_species(): void
    {
        $cattle = HostSpecies::factory()->largeAnimals()->create();
        $this->poultryDisease();

        $this->post(route('poultry.filter.results.store'), [
            'host_species_id' => $cattle->id,
            'clinical_signs' => [$this->sign->id],
        ])->assertSessionHasErrors('host_species_id');
    }

    public function test_it_rejects_a_sign_belonging_to_another_part(): void
    {
        $this->poultryDisease();

        $cattle = HostSpecies::factory()->largeAnimals()->create();
        $livestockSign = ClinicalSign::factory()->create(['display_name' => 'Bloat']);

        $livestock = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();
        $livestock->hostSpecies()->attach($cattle->id, ['is_primary_host' => true]);
        $livestock->clinicalSigns()->attach($livestockSign->id, ['weight' => 5]);

        $this->post(route('poultry.filter.results.store'), [
            'host_species_id' => $this->chicken->id,
            'clinical_signs' => [$livestockSign->id],
        ])->assertSessionHasErrors('clinical_signs.*');
    }

    public function test_suggestions_only_offer_poultry_signs(): void
    {
        $cattle = HostSpecies::factory()->largeAnimals()->create();
        $livestockSign = ClinicalSign::factory()->create(['display_name' => 'Bloat']);

        $livestock = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();
        $livestock->hostSpecies()->attach($cattle->id, ['is_primary_host' => true]);
        $livestock->clinicalSigns()->attach($livestockSign->id, ['weight' => 5]);

        $this->poultryDisease();

        $this->getJson(route('poultry.filter.suggestions', ['q' => 'Bloat']))
            ->assertOk()
            ->assertExactJson(['data' => []]);

        $this->getJson(route('poultry.filter.suggestions', ['q' => 'Drooping']))
            ->assertOk()
            ->assertJsonFragment(['title' => 'Drooping wings']);
    }

    public function test_the_json_submit_returns_a_poultry_results_url(): void
    {
        $this->poultryDisease();

        // The token embeds a random IV and an expiry, so it cannot be compared
        // against a freshly minted one. Following the redirect proves the same
        // thing: the response points at /poultry and the page it reaches renders.
        $redirect = $this->postJson(route('poultry.filter.results.store'), [
            'host_species_id' => $this->chicken->id,
            'clinical_signs' => [$this->sign->id],
        ])->assertOk()->json('redirect');

        $this->assertStringStartsWith(route('poultry.filter.results').'?criteria=', $redirect);

        $this->get($redirect)->assertOk()->assertSee(__('poultry.filter.selected_criteria'));
    }

    public function test_a_large_animal_share_link_is_rejected(): void
    {
        $token = app(FilterShareService::class)->create(
            ['host_species_id' => HostSpecies::factory()->largeAnimals()->create()->id, 'clinical_signs' => [$this->sign->id]],
            Disease::DOMAIN_LARGE_ANIMAL,
        );

        $this->get(route('poultry.filter.results', ['criteria' => $token]))
            ->assertRedirect(route('poultry.filter'))
            ->assertSessionHas('warning', __('poultry.filter.invalid_link'));
    }
}
