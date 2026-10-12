<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Diseases\Pages\CreateDisease;
use App\Filament\Resources\Diseases\Pages\EditDisease;
use App\Filament\Resources\Diseases\Pages\ListDiseases;
use App\Filament\Resources\Species\Pages\ListSpecies;
use App\Models\Disease;
use App\Models\Species;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The `domain` column is what keeps the shared Disease and Species tables split
 * between Large Animals and Poultry. If it is not surfaced in the admin then an
 * editor cannot tell the two apart, cannot fix a miscategorised record, and a
 * new record silently lands in Large Animals.
 */
class DomainAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['role' => UserRole::Admin]);
    }

    #[Test]
    public function a_poultry_disease_shows_its_domain_on_the_edit_form(): void
    {
        $disease = $this->poultryDisease('Newcastle Disease');

        Livewire::actingAs($this->admin(), 'admin')
            ->test(EditDisease::class, ['record' => $disease->getKey()])
            ->assertFormSet(['domain' => Disease::DOMAIN_POULTRY]);
    }

    #[Test]
    public function the_disease_list_can_be_filtered_to_one_domain(): void
    {
        $poultry = $this->poultryDisease('Newcastle Disease');
        $largeAnimal = Disease::create([
            'name' => 'Strangles',
            'slug' => 'strangles',
            'domain' => Disease::DOMAIN_LARGE_ANIMAL,
        ]);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListDiseases::class)
            ->assertTableColumnVisible('domain')
            ->assertTableFilterExists('domain')
            // Unfiltered, both sections share the one list.
            ->assertCanSeeTableRecords([$poultry, $largeAnimal])
            ->filterTable('domain', Disease::DOMAIN_POULTRY)
            ->assertCanSeeTableRecords([$poultry])
            ->assertCanNotSeeTableRecords([$largeAnimal]);
    }

    #[Test]
    public function creating_a_disease_persists_the_chosen_domain(): void
    {
        Livewire::actingAs($this->admin(), 'admin')
            ->test(CreateDisease::class)
            ->fillForm([
                'domain' => Disease::DOMAIN_POULTRY,
                'name' => 'Duck Viral Hepatitis Type 4',
                'slug' => 'duck-viral-hepatitis-type-4',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('diseases', [
            'slug' => 'duck-viral-hepatitis-type-4',
            'domain' => Disease::DOMAIN_POULTRY,
        ]);
    }

    #[Test]
    public function a_miscategorised_disease_can_be_corrected(): void
    {
        // A Disease that was filed under the wrong section must be fixable
        // through the admin, since the form is the only editor-facing surface.
        $disease = Disease::create([
            'name' => 'Osporein Toxicosis',
            'slug' => 'oosporein-toxicosis',
            'domain' => Disease::DOMAIN_LARGE_ANIMAL,
        ]);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(EditDisease::class, ['record' => $disease->getKey()])
            ->fillForm(['domain' => Disease::DOMAIN_POULTRY])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(Disease::DOMAIN_POULTRY, $disease->fresh()->domain);
    }

    #[Test]
    public function the_species_list_can_be_filtered_to_one_domain(): void
    {
        $poultry = Species::create([
            'name' => 'Ostrich',
            'name_ar' => 'نعام',
            'slug' => 'ostrich',
            'domain' => Species::DOMAIN_POULTRY,
        ]);

        $largeAnimal = Species::create([
            'name' => 'Camel',
            'name_ar' => 'جمل',
            'slug' => 'camel',
            'domain' => Species::DOMAIN_LARGE_ANIMAL,
        ]);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListSpecies::class)
            ->assertTableColumnVisible('domain')
            ->assertTableFilterExists('domain')
            ->filterTable('domain', Species::DOMAIN_POULTRY)
            ->assertCanSeeTableRecords([$poultry])
            ->assertCanNotSeeTableRecords([$largeAnimal]);
    }

    #[Test]
    public function both_domain_values_are_offered_on_both_resources(): void
    {
        $expected = [
            Disease::DOMAIN_LARGE_ANIMAL => 'Large Animals',
            Disease::DOMAIN_POULTRY => 'Poultry',
        ];

        $this->assertSame($expected, Disease::domainOptions());
        $this->assertSame($expected, Species::domainOptions(), 'Both resources must share one list of domains.');
    }

    private function admin(): User
    {
        return User::query()->where('role', UserRole::Admin)->firstOrFail();
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
        ], $attributes));
    }
}
