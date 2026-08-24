<?php

namespace Tests\Feature;

use App\Models\Disease;
use App\Models\LargeAnimals\ClinicalSign;
use App\Models\LargeAnimals\HostSpecies;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosisFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_a_host_species_and_at_least_three_signs(): void
    {
        $sign = ClinicalSign::factory()->create();

        $this->post(route('large-animals.diagnosis.run'), [
            'clinical_signs' => [$sign->id],
        ])->assertSessionHasErrors(['host_species_id', 'clinical_signs']);
    }

    public function test_it_rejects_sign_ids_that_do_not_exist(): void
    {
        $species = HostSpecies::factory()->create();

        $this->post(route('large-animals.diagnosis.run'), [
            'host_species_id' => $species->id,
            'clinical_signs' => [999999, 999998, 999997],
        ])->assertSessionHasErrors('clinical_signs.*');
    }

    public function test_it_ranks_diseases_for_a_valid_submission(): void
    {
        $species = HostSpecies::factory()->create();
        $signs = ClinicalSign::factory()->count(3)->create();

        $disease = Disease::factory()->create();
        $disease->hostSpecies()->attach($species->id, ['is_primary_host' => true]);
        foreach ($signs as $sign) {
            $disease->clinicalSigns()->attach($sign->id, ['weight' => 5]);
        }

        $this->post(route('large-animals.diagnosis.run'), [
            'host_species_id' => $species->id,
            'clinical_signs' => $signs->pluck('id')->all(),
        ])->assertOk();
    }
}
