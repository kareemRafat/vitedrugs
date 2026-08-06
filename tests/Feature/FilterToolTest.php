<?php

namespace Tests\Feature;

use App\Models\Disease;
use App\Models\LargeAnimals\AnatomicalStructure;
use App\Models\LargeAnimals\BodySystem;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\DiseaseClassification;
use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class FilterToolTest extends TestCase
{
    use RefreshDatabase;

    private HostSpecies $species;

    private ClinicalSign $sign;

    private string $storeRoute = 'large-animals.filter.results.store';

    private string $resultsRoute = 'large-animals.filter.results';

    protected function setUp(): void
    {
        parent::setUp();

        $this->species = HostSpecies::factory()->create(['display_name' => 'Cattle']);
        $this->sign = ClinicalSign::factory()->create();
    }

    private function foodSign(): ClinicalSign
    {
        return ClinicalSign::factory()->create();
    }

    private function makeDisease(bool $withFoodSign = true): Disease
    {
        $disease = Disease::factory()->create();
        $disease->hostSpecies()->attach($this->species->id, ['is_primary_host' => true, 'susceptibility' => 'high']);
        $disease->clinicalSigns()->attach($this->sign->id, ['weight' => 5]);

        if ($withFoodSign) {
            $disease->clinicalSigns()->attach($this->foodSign()->id, ['weight' => 3]);
        }

        return $disease;
    }

    private function submitFilter(array $criteria): TestResponse
    {
        return $this->followingRedirects()->post(route($this->storeRoute), $criteria);
    }

    public function test_it_filters_by_species_only(): void
    {
        $target = $this->makeDisease();

        $post = $this->post(route($this->storeRoute), [
            'host_species_id' => $this->species->id,
            'clinical_signs' => [$this->sign->id],
        ]);

        $location = $post->headers->get('Location');
        $response = $this->get($location);
        $response->assertOk()->assertSee($target->name);
    }

    public function test_it_redirects_to_a_get_results_url(): void
    {
        $this->makeDisease();

        $this->post(route($this->storeRoute), [
            'host_species_id' => $this->species->id,
            'clinical_signs' => [$this->sign->id],
        ])->assertRedirect();
    }

    public function test_it_filters_by_etiology(): void
    {
        $bacterial = $this->makeDisease();
        DiseaseClassification::factory()->create(['disease_id' => $bacterial->id, 'etiology_type' => 'Bacterial disease']);

        $viral = $this->makeDisease();
        DiseaseClassification::factory()->create(['disease_id' => $viral->id, 'etiology_type' => 'Viral disease']);

        $response = $this->submitFilter([
            'host_species_id' => $this->species->id,
            'clinical_signs' => [$this->sign->id],
            'etiology_type' => 'Bacterial disease',
        ]);

        $response->assertOk()
            ->assertSee($bacterial->name)
            ->assertDontSee($viral->name);
    }

    public function test_it_filters_by_zoonotic_only(): void
    {
        $zoonotic = $this->makeDisease();
        DiseaseClassification::factory()->create(['disease_id' => $zoonotic->id, 'zoonotic' => true]);

        $nonZoonotic = $this->makeDisease();
        DiseaseClassification::factory()->create(['disease_id' => $nonZoonotic->id, 'zoonotic' => false]);

        $response = $this->submitFilter([
            'host_species_id' => $this->species->id,
            'clinical_signs' => [$this->sign->id],
            'zoonotic' => '1',
        ]);

        $response->assertOk()
            ->assertSee($zoonotic->name)
            ->assertDontSee($nonZoonotic->name);
    }

    public function test_it_filters_by_body_system(): void
    {
        $respiratory = BodySystem::factory()->create(['display_name' => 'Respiratory']);
        $respiratoryStructure = AnatomicalStructure::factory()->create(['body_system_id' => $respiratory->id]);
        $respiratorySign = ClinicalSign::factory()->create(['anatomical_structure_id' => $respiratoryStructure->id]);

        $inSystem = $this->makeDisease(false);
        $inSystem->clinicalSigns()->attach($respiratorySign->id, ['weight' => 5]);

        $other = $this->makeDisease(false);

        $response = $this->submitFilter([
            'host_species_id' => $this->species->id,
            'clinical_signs' => [$this->sign->id],
            'body_system_id' => $respiratory->id,
        ]);

        $response->assertOk()
            ->assertSee($inSystem->name)
            ->assertDontSee($other->name);
    }

    public function test_it_requires_a_single_clinical_sign(): void
    {
        $this->post(route($this->storeRoute), [
            'host_species_id' => $this->species->id,
            'clinical_signs' => [],
        ])->assertSessionHasErrors('clinical_signs');
    }

    public function test_it_rejects_an_invalid_etiology(): void
    {
        $this->post(route($this->storeRoute), [
            'host_species_id' => $this->species->id,
            'clinical_signs' => [$this->sign->id],
            'etiology_type' => 'not-a-real-etiology',
        ])->assertSessionHasErrors('etiology_type');
    }

    public function test_it_returns_empty_state(): void
    {
        $response = $this->submitFilter([
            'host_species_id' => $this->species->id,
            'clinical_signs' => [$this->sign->id],
        ]);

        $response->assertOk()->assertSee(__('large-animals.filter.no_results'));
    }

    public function test_it_redirects_back_with_invalid_token(): void
    {
        $this->get(route($this->resultsRoute, ['criteria' => 'not-a-real-token']))
            ->assertRedirect(route('large-animals.filter'))
            ->assertSessionHas('warning', __('large-animals.filter.invalid_link'));
    }
}
