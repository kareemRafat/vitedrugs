<?php

namespace Tests\Feature\Poultry;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use App\Services\LargeAnimals\DiagnosisShareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * The poultry diagnosis tool must never surface livestock data, and a share
 * link minted for one part must not open under the other.
 */
class PoultryDiagnosticTest extends TestCase
{
    use RefreshDatabase;

    private HostSpecies $chicken;

    /** @var Collection<int, ClinicalSign> */
    private $signs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->chicken = HostSpecies::factory()->poultry()->create(['display_name' => 'Chicken']);
        $this->signs = ClinicalSign::factory()->count(3)->create();
    }

    /**
     * A poultry disease carrying the shared signs, attached to the bird.
     */
    private function poultryDisease(): Disease
    {
        $disease = Disease::factory()->poultry()->named('Newcastle Disease')->create();
        $disease->hostSpecies()->attach($this->chicken->id, ['is_primary_host' => true]);

        foreach ($this->signs as $sign) {
            $disease->clinicalSigns()->attach($sign->id, ['weight' => 5]);
        }

        return $disease;
    }

    /**
     * @return array<int, int>
     */
    private function signIds(): array
    {
        return $this->signs->pluck('id')->all();
    }

    public function test_the_page_lists_only_poultry_species(): void
    {
        HostSpecies::factory()->largeAnimals()->create(['display_name' => 'Cattle']);

        $response = $this->get(route('poultry.diagnosis'));

        $response->assertOk()
            ->assertSee($this->chicken->display_name)
            ->assertSee('Bird species')
            ->assertDontSee('Cattle');
    }

    public function test_it_ranks_poultry_diseases(): void
    {
        $disease = $this->poultryDisease();

        // Step one renders the refinement page rather than redirecting.
        $this->post(route('poultry.diagnosis.run'), [
            'host_species_id' => $this->chicken->id,
            'clinical_signs' => $this->signIds(),
        ])->assertOk();

        $results = $this->post(route('poultry.diagnosis.results.store'), [
            'host_species_id' => $this->chicken->id,
            'clinical_signs' => $this->signIds(),
        ]);

        $results->assertRedirect();

        $this->get($results->headers->get('Location'))
            ->assertOk()
            ->assertSee($disease->name);
    }

    public function test_it_never_returns_a_large_animal_disease(): void
    {
        $poultry = $this->poultryDisease();

        // The same signs on a livestock disease, so the only thing separating
        // the two is the domain filter rather than the evidence.
        $cattle = HostSpecies::factory()->largeAnimals()->create(['display_name' => 'Cattle']);
        $livestock = Disease::factory()->largeAnimals()->named('Bovine Respiratory Disease')->create();
        $livestock->hostSpecies()->attach($cattle->id, ['is_primary_host' => true]);

        foreach ($this->signs as $sign) {
            $livestock->clinicalSigns()->attach($sign->id, ['weight' => 5]);
        }

        $token = app(DiagnosisShareService::class)->create(
            Disease::DOMAIN_POULTRY,
            (string) $this->chicken->id,
            $this->signIds(),
            []
        );

        $this->get(route('poultry.diagnosis.results', ['assessment' => $token]))
            ->assertOk()
            ->assertSee($poultry->name)
            ->assertDontSee($livestock->name);
    }

    public function test_it_rejects_a_large_animal_species(): void
    {
        $cattle = HostSpecies::factory()->largeAnimals()->create();

        $this->post(route('poultry.diagnosis.run'), [
            'host_species_id' => $cattle->id,
            'clinical_signs' => $this->signIds(),
        ])->assertSessionHasErrors('host_species_id');
    }

    public function test_it_rejects_a_sign_belonging_to_another_part(): void
    {
        $cattle = HostSpecies::factory()->largeAnimals()->create();
        $livestockSign = ClinicalSign::factory()->create(['display_name' => 'Bloat']);

        $livestock = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();
        $livestock->hostSpecies()->attach($cattle->id, ['is_primary_host' => true]);
        $livestock->clinicalSigns()->attach($livestockSign->id, ['weight' => 5]);

        $this->poultryDisease();

        $this->post(route('poultry.diagnosis.run'), [
            'host_species_id' => $this->chicken->id,
            'clinical_signs' => [
                $this->signs[0]->id,
                $this->signs[1]->id,
                $livestockSign->id,
            ],
        ])->assertSessionHasErrors('clinical_signs.*');
    }

    public function test_suggestions_only_offer_poultry_signs(): void
    {
        $cattle = HostSpecies::factory()->largeAnimals()->create();
        $livestockSign = ClinicalSign::factory()->create(['display_name' => 'Bloat']);

        $livestock = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();
        $livestock->hostSpecies()->attach($cattle->id, ['is_primary_host' => true]);
        $livestock->clinicalSigns()->attach($livestockSign->id, ['weight' => 5]);

        $this->signs[0]->update(['display_name' => 'Drooping wings']);
        $this->poultryDisease();

        $results = $this->getJson(route('poultry.diagnosis.suggestions', ['q' => 'Bloat']));
        $results->assertOk()->assertExactJson([]);

        $this->getJson(route('poultry.diagnosis.suggestions', ['q' => 'Drooping']))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Drooping wings']);
    }

    public function test_a_large_animal_share_link_is_rejected(): void
    {
        $cattle = HostSpecies::factory()->largeAnimals()->create();

        $token = app(DiagnosisShareService::class)->create(
            Disease::DOMAIN_LARGE_ANIMAL,
            (string) $cattle->id,
            $this->signIds(),
            []
        );

        $this->get(route('poultry.diagnosis.results', ['assessment' => $token]))
            ->assertRedirect(route('poultry.diagnosis'))
            ->assertSessionHas('warning', __('poultry.diagnosis.invalid_share_link'));

        // The redirect target has to actually show the flash, or the user lands
        // on a working form with no idea why they were sent back.
        $this->followingRedirects()
            ->get(route('poultry.diagnosis.results', ['assessment' => $token]))
            ->assertOk()
            ->assertSee(__('poultry.diagnosis.invalid_share_link'));
    }

    public function test_a_poultry_share_link_opens(): void
    {
        $disease = $this->poultryDisease();

        $token = app(DiagnosisShareService::class)->create(
            Disease::DOMAIN_POULTRY,
            (string) $this->chicken->id,
            $this->signIds(),
            []
        );

        $this->get(route('poultry.diagnosis.results', ['assessment' => $token]))
            ->assertOk()
            ->assertSee($disease->name);
    }

    public function test_the_disease_detail_page_is_reachable_from_the_results(): void
    {
        $disease = $this->poultryDisease();

        $this->get(route('poultry.diseases.show', $disease->slug))
            ->assertOk()
            ->assertSee($disease->name);
    }

    public function test_the_disease_detail_page_rejects_a_large_animal_slug(): void
    {
        $livestock = Disease::factory()->largeAnimals()->named('Foot and Mouth Disease')->create();

        $this->get(route('poultry.diseases.show', $livestock->slug))->assertNotFound();
    }

    public function test_a_large_animal_slug_redirects_from_the_large_animal_page(): void
    {
        $disease = $this->poultryDisease();

        // Search and specializations link to diseases without a domain filter, so
        // the large-animal detail page still receives poultry slugs.
        $this->get(route('large-animals.diseases.show', $disease->slug))
            ->assertRedirect(route('poultry.diseases.show', $disease->slug));
    }
}
