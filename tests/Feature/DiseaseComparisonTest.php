<?php

namespace Tests\Feature;

use App\Models\Disease;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiseaseComparisonTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'clinical_signs' => [
                ['canonical_name' => 'pyrexia', 'display_name' => 'Pyrexia'],
                ['canonical_name' => 'depression', 'display_name' => 'Depression'],
            ],
            'postmortem_findings' => [
                ['canonical_name' => 'congestion', 'display_name' => 'Congestion'],
            ],
            'diagnosis' => [
                ['method' => 'Clinical examination'],
            ],
            'treatment' => [
                ['intervention' => 'Supportive care'],
            ],
            'prevention_control' => [
                ['measure' => 'Biosecurity'],
            ],
            'references' => [
                ['title' => 'OIE Terrestrial Code'],
            ],
        ], $overrides);
    }

    public function test_compare_page_returns_successful_response(): void
    {
        Disease::factory()->create();

        $this->get(route('drugs.comparison'))
            ->assertOk()
            ->assertSee(__('drugs.comparison.heading'));
    }

    public function test_component_selects_and_removes_diseases(): void
    {
        $disease = Disease::factory()->create(['knowledge_payload' => $this->payload()]);

        Livewire::test('drugs.disease-compare-builder')
            ->set('search1', $disease->name)
            ->call('selectDisease', 1, $disease->id)
            ->assertSet('diseaseId1', $disease->id)
            ->assertSet('search1', '')
            ->call('removeDisease', 1)
            ->assertSet('diseaseId1', null);
    }

    public function test_component_limits_selection_to_four_diseases(): void
    {
        $diseases = Disease::factory()->count(5)->create(['knowledge_payload' => $this->payload()]);

        $component = Livewire::test('drugs.disease-compare-builder');

        foreach ($diseases->take(4) as $index => $disease) {
            $component->call('selectDisease', $index + 1, $disease->id);
        }

        $component->assertSet('diseaseId1', $diseases[0]->id)
            ->assertSet('diseaseId2', $diseases[1]->id)
            ->assertSet('diseaseId3', $diseases[2]->id)
            ->assertSet('diseaseId4', $diseases[3]->id);

        $this->assertCount(4, $component->instance()->selectedDiseases);
    }

    public function test_component_builds_presence_matrices_with_differs_flags(): void
    {
        $common = ['canonical_name' => 'pyrexia', 'display_name' => 'Pyrexia'];
        $onlyFirst = ['canonical_name' => 'oral_vesicle', 'display_name' => 'Oral vesicle'];

        $first = Disease::factory()->create([
            'name' => 'Disease A',
            'slug' => 'disease-a',
            'knowledge_payload' => $this->payload(['clinical_signs' => [$common, $onlyFirst]]),
        ]);
        $second = Disease::factory()->create([
            'name' => 'Disease B',
            'slug' => 'disease-b',
            'knowledge_payload' => $this->payload(['clinical_signs' => [$common]]),
        ]);

        $component = Livewire::test('drugs.disease-compare-builder')
            ->call('selectDisease', 1, $first->id)
            ->call('selectDisease', 2, $second->id);

        $matrix = $component->instance()->clinicalSignsMatrix;

        $this->assertCount(2, $matrix);

        $pyrexia = collect($matrix)->firstWhere('name', 'pyrexia');
        $this->assertFalse($pyrexia['differs']);
        $this->assertTrue($pyrexia['presence'][$first->id]);
        $this->assertTrue($pyrexia['presence'][$second->id]);

        $vesicle = collect($matrix)->firstWhere('name', 'oral_vesicle');
        $this->assertTrue($vesicle['differs']);
        $this->assertTrue($vesicle['presence'][$first->id]);
        $this->assertFalse($vesicle['presence'][$second->id]);
    }

    public function test_component_hydrates_selection_from_url_query_string(): void
    {
        $disease = Disease::factory()->create(['knowledge_payload' => $this->payload()]);

        Livewire::withQueryParams(['disease1' => $disease->id])
            ->test('drugs.disease-compare-builder')
            ->assertSet('diseaseId1', $disease->id)
            ->assertSee($disease->name);
    }
}
