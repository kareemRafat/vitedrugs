<?php

namespace Tests\Feature\Poultry;

use App\Enums\UserRole;
use App\Filament\Pages\Diseases\ImportDiseaseKnowledge;
use App\Models\Disease;
use App\Models\User;
use App\Services\Knowledge\DiseaseKnowledgePayloadTransformer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportDiseaseKnowledgePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get('/admin/diseases/import-disease-knowledge')
            ->assertOk()
            ->assertSee('Import Disease Knowledge');
    }

    #[Test]
    public function it_offers_both_a_single_and_a_bulk_import(): void
    {
        $component = Livewire::actingAs($this->admin(), 'admin')->test(ImportDiseaseKnowledge::class);

        $component->assertActionExists('import');
        $component->assertActionExists('importBulk');
    }

    #[Test]
    public function it_saves_a_single_uploaded_payload_onto_the_selected_disease(): void
    {
        $disease = Disease::create($this->poultryDisease('Newcastle Disease'));

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('import')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'disease_id' => $disease->id,
                'json_file' => [$this->jsonFile('newcastle-disease', [
                    'gross_lesions' => [['canonical_name' => 'splenomegaly', 'display_name' => 'Enlarged spleen']],
                ])],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $payload = $disease->fresh()->knowledge_payload;

        $this->assertSame('splenomegaly', $payload['postmortem_findings'][0]['canonical_name']);
        $this->assertSame(DiseaseKnowledgePayloadTransformer::KEYS, array_keys($payload));
    }

    #[Test]
    public function a_bulk_upload_matches_each_file_to_a_disease_by_filename(): void
    {
        Disease::create($this->poultryDisease('Newcastle Disease'));
        Disease::create($this->poultryDisease('Coccidiosis'));

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('importBulk')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'files' => [
                    $this->jsonFile('newcastle-disease', ['important_organs_affected' => ['spleen', 'brain']]),
                    $this->jsonFile('coccidiosis', ['important_organs_affected' => ['intestine']]),
                ],
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertSame(
            ['spleen', 'brain'],
            Disease::where('slug', 'newcastle-disease')->value('knowledge_payload')['important_organs_affected'],
        );

        $this->assertSame(
            ['intestine'],
            Disease::where('slug', 'coccidiosis')->value('knowledge_payload')['important_organs_affected'],
        );
    }

    #[Test]
    public function the_filename_survives_upload_so_matching_can_work(): void
    {
        Disease::create($this->poultryDisease('Newcastle Disease'));

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('importBulk')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'files' => [$this->jsonFile('newcastle-disease', ['important_organs_affected' => ['spleen']])],
            ])
            ->callMountedAction();

        $this->assertContains(
            'knowledge-imports/newcastle-disease.json',
            Storage::disk('local')->allFiles(),
            'Filament rewrites upload names to UUIDs unless preserveFilenames is set.',
        );
    }

    #[Test]
    public function a_file_with_no_matching_disease_is_left_alone(): void
    {
        Disease::create($this->poultryDisease('Newcastle Disease'));

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('importBulk')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'files' => [$this->jsonFile('not-a-real-disease', ['important_organs_affected' => ['nothing']])],
            ])
            ->callMountedAction();

        $this->assertNull(Disease::query()->where('slug', 'newcastle-disease')->value('knowledge_payload'));
        $this->assertSame(1, Disease::query()->poultry()->count(), 'An unmatched file must not create a Disease.');
    }

    #[Test]
    public function a_bulk_preview_writes_nothing(): void
    {
        $disease = Disease::create($this->poultryDisease('Newcastle Disease'));

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('importBulk')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'preview_only' => true,
                'files' => [$this->jsonFile('newcastle-disease', ['important_organs_affected' => ['spleen']])],
            ])
            ->callMountedAction();

        $this->assertNull($disease->fresh()->knowledge_payload, 'A preview must not write anything.');
    }

    #[Test]
    public function bulk_matching_only_sees_diseases_in_the_chosen_domain(): void
    {
        Disease::create([
            'name' => 'Strangles',
            'slug' => 'strangles',
            'domain' => Disease::DOMAIN_LARGE_ANIMAL,
        ]);

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('importBulk')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'files' => [$this->jsonFile('strangles', ['important_organs_affected' => ['skin']])],
            ])
            ->callMountedAction();

        $this->assertNull(
            Disease::where('slug', 'strangles')->value('knowledge_payload'),
            'A large-animal disease must not be reachable from the poultry section.',
        );
    }

    #[Test]
    public function an_unparsable_file_is_skipped_without_failing_the_batch(): void
    {
        Disease::create($this->poultryDisease('Newcastle Disease'));
        Disease::create($this->poultryDisease('Coccidiosis'));

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('importBulk')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'files' => [
                    UploadedFile::fake()->createWithContent('broken.json', '{ not json'),
                    $this->jsonFile('coccidiosis', ['important_organs_affected' => ['intestine']]),
                ],
            ])
            ->callMountedAction();

        $this->assertNull(Disease::where('slug', 'newcastle-disease')->value('knowledge_payload'));
        $this->assertSame(
            ['intestine'],
            Disease::where('slug', 'coccidiosis')->value('knowledge_payload')['important_organs_affected'],
        );
    }

    #[Test]
    public function a_configured_alias_matches_a_document_whose_filename_differs(): void
    {
        Disease::create($this->poultryDisease('Oosporein Toxicosis'));

        config()->set('vetpedia.poultry.knowledge_file_aliases', ['oosporein' => 'oosporein-toxicosis']);

        Storage::fake('local');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImportDiseaseKnowledge::class)
            ->callAction('importBulk')
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'files' => [$this->jsonFile('oosporein', ['important_organs_affected' => ['kidney']])],
            ])
            ->callMountedAction();

        $this->assertSame(
            ['kidney'],
            Disease::where('slug', 'oosporein-toxicosis')->value('knowledge_payload')['important_organs_affected'],
        );
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function poultryDisease(string $name, array $attributes = []): array
    {
        return array_merge([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'domain' => Disease::DOMAIN_POULTRY,
        ], $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function jsonFile(string $slug, array $payload): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $slug.'.json',
            (string) json_encode($payload),
        );
    }
}
