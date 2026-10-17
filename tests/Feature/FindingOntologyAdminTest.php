<?php

namespace Tests\Feature;

use App\Enums\OntologyType;
use App\Enums\UserRole;
use App\Filament\Resources\LargeAnimals\Findings\Pages\CreateFinding;
use App\Filament\Resources\LargeAnimals\Findings\Pages\EditFinding;
use App\Filament\Resources\LargeAnimals\Findings\Pages\ListFindings;
use App\Filament\Resources\LargeAnimals\Findings\RelationManagers\ImagesRelationManager;
use App\Models\LargeAnimals\Finding;
use App\Models\LargeAnimals\FindingImage;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * `findings.ontology_type` is what decides whether a row reaches the lesion
 * atlas at all, and the atlas filter is an exact string match. It was a free
 * text box on a form, so `Lesion` with a capital L saved cleanly and left the
 * finding invisible on the page — a failure with no error and no trace.
 *
 * The second half of this file covers the photograph upload, which hangs off the
 * same resource and is the reason this column had to be trustworthy first.
 */
class FindingOntologyAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['role' => UserRole::Admin]);
    }

    // ------------------------------------------------------------ the dropdown

    /**
     * The point of the change: the legal values are chosen, not typed. Asserted
     * against the field's own options rather than against the enum so that a
     * future third case cannot be added to the enum and quietly disappear from
     * the form, which would reintroduce the same bug through the back door.
     */
    #[Test]
    public function the_edit_form_offers_exactly_the_two_legal_values(): void
    {
        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(EditFinding::class, ['record' => $lesion->getKey()])
            ->assertFormFieldExists('ontology_type', function ($field): bool {
                $options = $field->getOptions();

                return array_keys($options) === ['lesion', 'sign']
                    && $options['lesion'] === 'Lesion'
                    && $options['sign'] === 'Sign';
            });
    }

    /**
     * The reverse of the bug: a value the application cannot read must not be
     * writable. `assertFormSet` on the raw string would pass here, so the
     * assertion is on the cast attribute — the same one the atlas reads.
     */
    #[Test]
    public function the_stored_value_is_read_back_as_an_enum(): void
    {
        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);

        $this->assertSame(OntologyType::Lesion, $lesion->fresh()->ontology_type);
        $this->assertSame('lesion', $lesion->fresh()->getRawOriginal('ontology_type'));
    }

    /**
     * Editing through the form persists the chosen type.
     */
    #[Test]
    public function a_miscategorised_finding_can_be_corrected(): void
    {
        $sign = $this->finding('Pyrexia', OntologyType::Sign);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(EditFinding::class, ['record' => $sign->getKey()])
            ->fillForm(['ontology_type' => OntologyType::Lesion])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(OntologyType::Lesion, $sign->fresh()->ontology_type);
    }

    /**
     * `required()` on the form, asserted because it is what stops new rows from
     * arriving with a null type — the state that made the body map and the atlas
     * disagree about the same finding. The 17 rows already in the database still
     * need the backfill; this only prevents new ones.
     */
    #[Test]
    public function a_finding_cannot_be_created_without_a_type(): void
    {
        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'vesicle',
                'display_name' => 'Vesicle',
                'slug' => 'vesicle',
                'ontology_type' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['ontology_type' => 'required']);
    }

    // ---------------------------------------------------------------- the filter

    /**
     * Photographing 640 lesions means working through them, and the table holds
     * 640 lesions and 432 clinical signs under one heading.
     */
    #[Test]
    public function the_findings_list_can_be_filtered_to_lesions_only(): void
    {
        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);
        $sign = $this->finding('Pyrexia', OntologyType::Sign);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListFindings::class)
            ->assertTableFilterExists('ontology_type')
            ->assertCanSeeTableRecords([$lesion, $sign])
            ->filterTable('ontology_type', OntologyType::Lesion->value)
            ->assertCanSeeTableRecords([$lesion])
            ->assertCanNotSeeTableRecords([$sign]);
    }

    #[Test]
    public function the_findings_list_can_be_filtered_to_signs_only(): void
    {
        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);
        $sign = $this->finding('Pyrexia', OntologyType::Sign);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListFindings::class)
            ->filterTable('ontology_type', OntologyType::Sign->value)
            ->assertCanSeeTableRecords([$sign])
            ->assertCanNotSeeTableRecords([$lesion]);
    }

    // -------------------------------------------------------------- the upload

    /**
     * The tab the plan puts the upload on. Asserted against the edit page rather
     * than by mounting the manager directly, because the failure mode being
     * guarded against is a relation manager that is registered but never
     * rendered — which mounts fine in isolation and 500s nothing.
     */
    #[Test]
    public function the_images_tab_is_rendered_on_the_edit_page(): void
    {
        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(EditFinding::class, ['record' => $lesion->getKey()])
            ->assertSeeLivewire(ImagesRelationManager::class);
    }

    #[Test]
    public function the_images_manager_loads_and_lists_its_records(): void
    {
        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);
        $image = FindingImage::factory()->create(['finding_id' => $lesion->id]);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImagesRelationManager::class, [
                'ownerRecord' => $lesion,
                'pageClass' => EditFinding::class,
            ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$image]);
    }

    /**
     * A photograph uploads and its caption is stored beside it.
     *
     * `Storage::fake` rather than a real write: this asserts the row and the
     * path, not that GD is installed on the machine running the suite.
     */
    #[Test]
    public function a_photograph_and_its_caption_are_stored_against_the_finding(): void
    {
        Storage::fake('public');

        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImagesRelationManager::class, [
                'ownerRecord' => $lesion,
                'pageClass' => EditFinding::class,
            ])
            ->callAction(TestAction::make('create')->table(), [
                'image' => [UploadedFile::fake()->image('liver.jpg', 640, 480)],
                'caption' => 'Pale, mottled liver',
                'caption_ar' => 'كبد شاحب مرقّط',
                'sort_order' => 0,
            ])
            ->assertHasNoActionErrors();

        $stored = FindingImage::query()->where('finding_id', $lesion->id)->sole();

        $this->assertSame('Pale, mottled liver', $stored->caption);
        $this->assertSame('كبد شاحب مرقّط', $stored->caption_ar);
        $this->assertStringStartsWith('findings/'.$lesion->id.'/', $stored->image);
    }

    /**
     * A photo with no caption is still a valid upload. The captions are the
     * valuable part, but refusing the file without one would mean an editor
     * blocked on a translation before they could attach the picture at all.
     */
    #[Test]
    public function a_photograph_may_be_uploaded_without_a_caption(): void
    {
        Storage::fake('public');

        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImagesRelationManager::class, [
                'ownerRecord' => $lesion,
                'pageClass' => EditFinding::class,
            ])
            ->callAction(TestAction::make('create')->table(), [
                'image' => [UploadedFile::fake()->image('liver.jpg')],
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(1, FindingImage::query()->where('finding_id', $lesion->id)->count());
    }

    /**
     * Deleting the row deletes the file. Without the `before()` hook the row
     * goes and the upload stays, and nothing in the application ever references
     * the leftover again.
     */
    #[Test]
    public function deleting_a_photograph_removes_the_file_from_disk(): void
    {
        Storage::fake('public');

        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);
        $image = FindingImage::factory()->create([
            'finding_id' => $lesion->id,
            'image' => $this->uploaded(),
        ]);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ImagesRelationManager::class, [
                'ownerRecord' => $lesion,
                'pageClass' => EditFinding::class,
            ])
            ->callAction(TestAction::make('delete')->table($image))
            ->assertHasNoActionErrors();

        $this->assertNull(FindingImage::query()->find($image->getKey()));
        Storage::disk('public')->assertMissing($image->image);
    }

    // ------------------------------------------------ the photograph on create

    /**
     * The point of the whole phase: a new lesion arrives with its picture, in one
     * submit, instead of needing a second trip through the Images tab.
     *
     * Asserted as exactly one row each. Two `finding_images` rows would mean the
     * upload went through both the form field and the tab, and a `findings` row
     * with no image row would mean the photograph silently vanished — which is the
     * failure this design is most prone to, because the file is staged before the
     * record exists.
     */
    #[Test]
    public function creating_a_finding_with_a_photograph_attaches_it(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
                'image' => [UploadedFile::fake()->image('liver.jpg', 640, 480)],
                'caption' => 'Pale, mottled liver',
                'caption_ar' => 'كبد شاحب مرقّط',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $finding = Finding::query()->where('canonical_name', 'liver_lesions')->sole();
        $image = FindingImage::query()->where('finding_id', $finding->id)->sole();

        $this->assertSame('Pale, mottled liver', $image->caption);
        $this->assertSame('كبد شاحب مرقّط', $image->caption_ar);
    }

    /**
     * The staged file is moved into the finding's own folder.
     *
     * This is the crux of the design. Filament uploads the file before the
     * finding exists, so it lands in `findings/tmp/` and has to be relocated once
     * the id is known. An assertion on the final path catches both halves going
     * wrong: a file left in `tmp/` (never moved) and a row pointing at a temp path
     * (moved on disk but the row written from the stale value).
     */
    #[Test]
    public function the_photograph_is_filed_under_the_new_findings_own_folder(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
                'image' => [UploadedFile::fake()->image('liver.jpg')],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $finding = Finding::query()->where('canonical_name', 'liver_lesions')->sole();
        $image = FindingImage::query()->where('finding_id', $finding->id)->sole();

        $this->assertStringStartsWith('findings/'.$finding->id.'/', $image->image);
        // Filament rewrites the stem to a UUID, so only the extension is stable.
        // Matching on the original filename would assert an implementation detail
        // Filament owns, not behaviour anything depends on.
        $this->assertStringEndsWith('.jpg', $image->image);
        Storage::disk('public')->assertExists($image->image);
    }

    /**
     * Nothing is abandoned in the staging folder.
     *
     * Asserted rather than assumed: a stale `findings/tmp/` is invisible to the
     * application and would sit there forever, which is the kind of thing nobody
     * notices until the disk is full.
     */
    #[Test]
    public function the_staging_folder_is_left_empty(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
                'image' => [UploadedFile::fake()->image('liver.jpg')],
            ])
            ->call('create');

        $this->assertSame(
            [],
            Storage::disk('public')->files('findings/tmp'),
            'A staged upload that was never moved is unreachable and never cleaned up.',
        );
    }

    /**
     * The photograph is optional. Most of the 640 lesions have none, and making
     * the field required would block the far more common case of adding a finding
     * you have not photographed yet.
     */
    #[Test]
    public function a_finding_can_still_be_created_with_no_photograph(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(1, Finding::query()->count());
        $this->assertSame(0, FindingImage::query()->count());
    }

    /**
     * A blank caption stores `null`, not an empty string.
     *
     * `''` and `null` render identically but behave differently: `localized_caption`
     * falls back to English on `null` and short-circuits on `''`, so an empty
     * string would suppress a caption that exists in the other language.
     */
    #[Test]
    public function a_blank_caption_is_stored_as_null(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
                'image' => [UploadedFile::fake()->image('liver.jpg')],
                'caption' => '',
                'caption_ar' => 'كبد شاحب مرقّط',
            ])
            ->call('create');

        $image = FindingImage::query()->sole();

        $this->assertNull($image->caption);
        $this->assertSame('كبد شاحب مرقّط', $image->caption_ar);
    }

    /**
     * The staged fields are stripped before the insert.
     *
     * `image`, `caption` and `caption_ar` are not columns on `findings` and not in
     * `$fillable`. Passing them through would throw on the first one, and the
     * lesion would be lost along with the photograph — which is why this test
     * asserts the *finding* exists, not just that no exception was raised.
     */
    #[Test]
    public function the_photograph_fields_never_reach_the_findings_table(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
                'image' => [UploadedFile::fake()->image('liver.jpg')],
                'caption' => 'Pale, mottled liver',
                'caption_ar' => 'كبد شاحب مرقّط',
            ])
            ->call('create');

        $finding = Finding::query()->sole();

        $this->assertArrayNotHasKey('image', $finding->getAttributes());
        $this->assertArrayNotHasKey('caption', $finding->getAttributes());
        $this->assertArrayNotHasKey('caption_ar', $finding->getAttributes());
    }

    /**
     * A PDF renamed to `.jpg` is not a photograph.
     *
     * `->image()` resolves to a MIME check on the file's contents, not its
     * extension, which is the only version of this rule worth having — the
     * filename is the one thing an uploader controls.
     */
    #[Test]
    public function a_non_image_file_is_rejected(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
                'image' => [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')],
            ])
            ->call('create')
            ->assertHasFormErrors(['image']);
    }

    /**
     * A photograph that cannot be filed must not cost the lesion.
     *
     * The create path writes the finding first and attaches second, so a failure
     * in the attach step can only leave a finding with no picture — the
     * recoverable direction. The reverse would mean a vet's post-mortem finding
     * lost because a file did not move.
     *
     * Forced with a disk whose `move()` fails, which is what a permissions error
     * or a full volume looks like from inside the application.
     */
    #[Test]
    public function a_finding_survives_a_photograph_that_cannot_be_filed(): void
    {
        Storage::fake('public');

        $real = Storage::disk('public');

        Storage::set('public', new class($real->getDriver(), $real->getAdapter(), $real->getConfig()) extends FilesystemAdapter
        {
            public function move($path, $target): bool
            {
                return false;
            }
        });

        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateFinding::class)
            ->fillForm([
                'canonical_name' => 'liver_lesions',
                'display_name' => 'Liver lesions',
                'slug' => 'liver-lesions',
                'ontology_type' => OntologyType::Lesion,
                'image' => [UploadedFile::fake()->image('liver.jpg')],
            ])
            ->call('create');

        $this->assertSame(1, Finding::query()->count(), 'The lesion must be created even when its photograph cannot be filed.');
        $this->assertSame(0, FindingImage::query()->count());
    }

    /**
     * The photograph field is absent from the edit form.
     *
     * `FindingForm` is shared, so this is the assertion that keeps the two entry
     * points from both claiming the same upload — which would show one
     * photograph twice on one screen and let the form silently replace an image
     * the tab's reorder was relying on.
     */
    #[Test]
    public function the_edit_form_does_not_offer_a_photograph_field(): void
    {
        $lesion = $this->finding('Liver lesions', OntologyType::Lesion);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(EditFinding::class, ['record' => $lesion->getKey()])
            ->assertFormFieldDoesNotExist('image');
    }

    // ------------------------------------------------------------------ helpers

    private function admin(): User
    {
        return User::query()->where('role', UserRole::Admin)->firstOrFail();
    }

    private function finding(string $name, OntologyType $type): Finding
    {
        return Finding::factory()->create([
            'canonical_name' => str($name)->slug('_')->toString(),
            'display_name' => $name,
            'ontology_type' => $type,
        ]);
    }

    /**
     * Put a real file on the faked disk and return its path, so the delete test
     * asserts against something that exists.
     */
    private function uploaded(): string
    {
        $path = 'findings/liver.jpg';

        Storage::disk('public')->put($path, 'fake-image-bytes');

        return $path;
    }
}
