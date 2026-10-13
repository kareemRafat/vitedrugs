<?php

namespace Tests\Feature;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\Dosage;
use App\Models\DosageForm;
use App\Models\Product;
use App\Models\Species;
use App\Models\WithdrawalPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The part drug catalogue, `/poultry/products` and `/large-animals/products`.
 *
 * The rule these tests exist to pin is the inclusion rule. `products` is one
 * shared table with no species column, so "is this drug for poultry?" is not
 * recorded anywhere directly and had to be derived — from three signals that
 * disagree with each other. A catalogue that quietly widened or narrowed that
 * rule would still render a plausible page, which is the failure mode that made
 * it worth writing down.
 */
class PartProductCatalogueTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------- inclusion

    /**
     * A poultry dose puts a product on the poultry page.
     */
    #[Test]
    public function a_poultry_dose_makes_a_product_poultry_applicable(): void
    {
        $product = $this->productDosedFor($this->poultrySpecies());

        $this->assertTrue(
            Product::applicableTo(Species::DOMAIN_POULTRY)->whereKey($product)->exists(),
        );
    }

    /**
     * The converse, which is the half that matters: a cattle dose does not.
     *
     * Without this the page could pass its inclusion test by listing everything,
     * and 15 of 30 becoming 30 would look like a successful import rather than
     * the loss of the filter it is.
     */
    #[Test]
    public function a_cattle_dose_does_not_make_a_product_poultry_applicable(): void
    {
        $product = $this->productDosedFor($this->cattleSpecies());

        $this->assertFalse(
            Product::applicableTo(Species::DOMAIN_POULTRY)->whereKey($product)->exists(),
        );
        $this->assertTrue(
            Product::applicableTo(Species::DOMAIN_LARGE_ANIMAL)->whereKey($product)->exists(),
        );
    }

    /**
     * Dosing is the rule, and the other two signals do not substitute for it.
     *
     * Twelve products treat a poultry disease and six carry a poultry withdrawal
     * figure, against fifteen with a poultry dose. Using either of the others
     * instead would be a different page, so the difference is asserted rather
     * than left to a comment.
     */
    #[Test]
    public function a_poultry_disease_link_alone_does_not_qualify_a_product(): void
    {
        $product = Product::factory()->create();
        $disease = Disease::factory()->poultry()->named('Coccidiosis')->create();

        $product->diseases()->attach($disease);

        $this->assertFalse(
            Product::applicableTo(Species::DOMAIN_POULTRY)->whereKey($product)->exists(),
        );
    }

    #[Test]
    public function a_poultry_withdrawal_period_alone_does_not_qualify_a_product(): void
    {
        $product = Product::factory()->create();

        WithdrawalPeriod::factory()->for($product)->for($this->poultrySpecies())->create([
            'meat_days' => 28,
        ]);

        $this->assertFalse(
            Product::applicableTo(Species::DOMAIN_POULTRY)->whereKey($product)->exists(),
        );
    }

    // ------------------------------------------------------------ part isolation

    /**
     * Each part lists only its own drugs.
     *
     * The same defect class as the Phase 6 search page and the Phase 7 picker:
     * one shared table, so an unscoped query offers the other half of the
     * catalogue without anything looking wrong.
     */
    #[Test]
    public function the_poultry_page_never_lists_a_large_animal_drug(): void
    {
        $poultry = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $cattle = $this->productDosedFor($this->cattleSpecies(), 'Baytril');

        $html = $this->get(route('poultry.products.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Amoxivet', $html);
        $this->assertStringNotContainsString('Baytril', $html);
        $this->assertNotContains($cattle->id, $this->poultryComponent()->instance()->products->pluck('id')->all());
    }

    #[Test]
    public function the_large_animal_page_never_lists_a_poultry_drug(): void
    {
        $poultry = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $cattle = $this->productDosedFor($this->cattleSpecies(), 'Baytril');

        $html = $this->get(route('large-animals.products.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Baytril', $html);
        $this->assertStringNotContainsString('Amoxivet', $html);
        $this->assertNotContains($poultry->id, $this->largeAnimalComponent()->instance()->products->pluck('id')->all());
    }

    /**
     * A product dosed for both appears on both pages, once each.
     *
     * Not a hypothetical edge case: Ripercol carries a poultry withdrawal period
     * and Ivomec is dosed for cattle while treating a poultry disease, so a drug
     * legitimately belongs to more than one part.
     */
    #[Test]
    public function a_product_dosed_for_both_parts_appears_on_both(): void
    {
        $product = Product::factory()->create(['trade_name' => 'Draxxin', 'slug' => 'draxxin']);
        Dosage::factory()->for($product)->for($this->poultrySpecies())->create();
        Dosage::factory()->for($product)->for($this->cattleSpecies())->create();

        $this->assertContains($product->id, $this->poultryComponent()->instance()->products->pluck('id')->all());
        $this->assertContains($product->id, $this->largeAnimalComponent()->instance()->products->pluck('id')->all());
    }

    // ------------------------------------------------------------------ withdrawal

    /**
     * A product with no withdrawal record says so in words.
     *
     * On a food-animal page an empty box reads as "no restriction", which is the
     * one reading of the absence that can hurt somebody. The page therefore
     * states the gap rather than rendering nothing.
     */
    #[Test]
    public function a_missing_withdrawal_period_is_stated_rather_than_hidden(): void
    {
        $this->productDosedFor($this->poultrySpecies(), 'Tylan');

        $this->get(route('poultry.products.index'))
            ->assertOk()
            ->assertSee(__('poultry.catalogue.withdrawal_unrecorded'));
    }

    #[Test]
    public function a_recorded_withdrawal_period_is_shown_with_its_species(): void
    {
        $product = $this->productDosedFor($this->poultrySpecies(), 'Nuflor');

        WithdrawalPeriod::factory()->for($product)->for($this->poultrySpecies('Broiler'))->create([
            'meat_days' => 28,
            'egg_days' => 5,
            'milk_days' => null,
        ]);

        $this->get(route('poultry.products.index'))
            ->assertOk()
            ->assertSee('Broiler')
            ->assertSee('28')
            ->assertSee(__('poultry.catalogue.egg'));
    }

    /**
     * Only the part's own withdrawal rows are rendered.
     *
     * A card on the poultry page listing a cattle withdrawal figure would be
     * offering a number for the wrong animal — and `milk_days` is populated on
     * poultry rows in the source data, which is meaningless for a bird.
     */
    #[Test]
    public function the_poultry_card_never_shows_a_cattle_withdrawal_period(): void
    {
        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $cattle = Species::factory()->largeAnimals()->named('Cattle')->create();

        WithdrawalPeriod::factory()->for($product)->for($cattle)->create([
            'meat_days' => 30,
            'milk_days' => 30,
        ]);

        $rendered = $this->poultryComponent()->instance()
            ->withdrawalBySpecies($this->poultryProduct($product));

        $this->assertTrue($rendered->isEmpty());
    }

    /**
     * Two rows for the same species are both kept.
     *
     * Excenel carries two Quail withdrawal rows with different egg figures.
     * Silently keeping one would invent a number the data does not support, so
     * the duplication is rendered and the defect stays visible.
     */
    #[Test]
    public function conflicting_rows_for_one_species_are_both_kept(): void
    {
        $product = $this->productDosedFor($this->poultrySpecies(), 'Excenel');
        $quail = $this->poultrySpecies('Quail');

        WithdrawalPeriod::factory()->for($product)->for($quail)->create(['egg_days' => 3]);
        WithdrawalPeriod::factory()->for($product)->for($quail)->create(['egg_days' => 5]);

        $grouped = $this->poultryComponent()->instance()
            ->withdrawalBySpecies($this->poultryProduct($product));

        $this->assertCount(1, $grouped, 'Both rows belong to the same species and should group together.');
        $this->assertCount(2, $grouped->first());
    }

    // -------------------------------------------------------------------- filters

    #[Test]
    public function the_dosage_form_filter_narrows_the_list(): void
    {
        $form = DosageForm::factory()->create(['name' => 'Oral Solution', 'slug' => 'oral-solution']);
        $other = DosageForm::factory()->create(['name' => 'Bolus', 'slug' => 'bolus']);

        // The factory assigns a random dosage form, so every form has to be set
        // explicitly or the filter is testing nothing — and a random form can
        // collide with the two named ones on the unique slug.
        $matching = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $matching->update(['dosage_form_id' => $form->id]);

        $missing = $this->productDosedFor($this->poultrySpecies(), 'Baytril');
        $missing->update(['dosage_form_id' => $other->id]);

        $matching = $this->filteredPoultry(['dosageForm' => $form->id]);

        $this->assertNotEmpty($matching->items());
        $this->assertSame([$form->id], $matching->pluck('dosage_form_id')->unique()->values()->all());

        // The other form selects the other product, not nothing — a filter that
        // always returns an empty page would pass the same assertion.
        $this->assertSame([$missing->id], $this->filteredPoultry(['dosageForm' => $other->id])->pluck('id')->all());
    }

    #[Test]
    public function the_active_ingredient_filter_narrows_the_list(): void
    {
        $ingredient = ActiveIngredient::factory()->create(['name' => 'Amoxicillin', 'slug' => 'amoxicillin']);
        $other = ActiveIngredient::factory()->create(['name' => 'Florfenicol', 'slug' => 'florfenicol']);

        $matching = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $missing = $this->productDosedFor($this->poultrySpecies(), 'Baytril');

        $matching->activeIngredients()->attach($ingredient);
        $missing->activeIngredients()->attach($other);

        $this->assertSame([$matching->id], $this->filteredPoultry(['ingredient' => $ingredient->id])->pluck('id')->all());
    }

    #[Test]
    public function the_manufacturer_filter_narrows_the_list(): void
    {
        $maker = Company::factory()->create(['name' => 'Elanco', 'slug' => 'elanco']);
        $other = Company::factory()->create(['name' => 'Zoetis', 'slug' => 'zoetis']);

        $made = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $notMade = $this->productDosedFor($this->poultrySpecies(), 'Baytril');

        $made->companies()->attach($maker, ['role' => 'manufacturer']);
        $notMade->companies()->attach($other, ['role' => 'manufacturer']);

        $this->assertSame([$made->id], $this->filteredPoultry(['manufacturer' => $maker->id])->pluck('id')->all());
    }

    /**
     * An agent is not a manufacturer.
     *
     * `product_company.role` distinguishes the three, and filtering on the
     * company alone would offer a distributor as a manufacturer.
     */
    #[Test]
    public function the_manufacturer_filter_ignores_other_company_roles(): void
    {
        $company = Company::factory()->create(['name' => 'Elanco', 'slug' => 'elanco']);
        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');

        $product->companies()->attach($company, ['role' => 'distributor']);

        $this->assertSame(0, $this->filteredPoultry(['manufacturer' => $company->id])->total());
    }

    /**
     * Search reaches the molecule and the disease, not just the brand.
     *
     * A vet who knows the molecule or the disease but not the brand is the
     * common case, and a `trade_name`-only search answers none of it.
     */
    #[Test]
    public function search_matches_ingredient_and_disease_not_only_the_brand(): void
    {
        $ingredient = ActiveIngredient::factory()->create(['name' => 'Amoxicillin', 'slug' => 'amoxicillin']);
        $disease = Disease::factory()->poultry()->named('Coccidiosis')->create();

        $byIngredient = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $byIngredient->activeIngredients()->attach($ingredient);

        $byDisease = $this->productDosedFor($this->poultrySpecies(), 'Tylan');
        $byDisease->diseases()->attach($disease);

        $this->productDosedFor($this->poultrySpecies(), 'Baytril');

        $this->assertSame([$byIngredient->id], $this->filteredPoultry(['search' => 'Amoxicillin'])->pluck('id')->all());

        $this->assertSame([$byDisease->id], $this->filteredPoultry(['search' => 'Coccidiosis'])->pluck('id')->all());
    }

    /**
     * Search cannot cross into the other part's diseases.
     *
     * Typing a large-animal disease name on the poultry page must not surface a
     * poultry drug through it.
     */
    #[Test]
    public function search_does_not_leak_through_another_parts_diseases(): void
    {
        $disease = Disease::factory()->largeAnimals()->named('Mastitis')->create();
        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $product->diseases()->attach($disease);

        $this->assertSame(0, $this->filteredPoultry(['search' => 'Mastitis'])->total());
    }

    #[Test]
    public function resetting_clears_every_filter(): void
    {
        $form = DosageForm::factory()->create(['name' => 'Bolus', 'slug' => 'bolus']);
        $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');

        $component = $this->poultryComponent()
            ->set('search', 'nothing-matches-this')
            ->set('dosageForm', $form->id)
            ->set('ingredient', 'x')
            ->set('manufacturer', 'y')
            ->call('resetFilters');

        $this->assertSame('', $component->get('search'));
        $this->assertSame('', $component->get('dosageForm'));
        $this->assertSame('', $component->get('ingredient'));
        $this->assertSame('', $component->get('manufacturer'));
    }

    // --------------------------------------------------------- option lists / states

    /**
     * A filter option is only offered when it matches something on this page.
     *
     * The catalogue holds 30 products, so listing every manufacturer and
     * ingredient in the database would be mostly dead options — and on the
     * poultry half, nearly all of them.
     */
    #[Test]
    public function filter_options_are_scoped_to_the_part(): void
    {
        $poultryMaker = Company::factory()->create(['name' => 'Elanco', 'slug' => 'elanco']);
        $cattleMaker = Company::factory()->create(['name' => 'Zoetis', 'slug' => 'zoetis']);

        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $product->companies()->attach($poultryMaker, ['role' => 'manufacturer']);

        $other = $this->productDosedFor($this->cattleSpecies(), 'Baytril');
        $other->companies()->attach($cattleMaker, ['role' => 'manufacturer']);

        $names = $this->poultryComponent()->instance()->manufacturers->pluck('name');

        $this->assertTrue($names->contains('Elanco'));
        $this->assertFalse($names->contains('Zoetis'));
    }

    #[Test]
    public function an_empty_result_shows_the_empty_state_not_a_blank_page(): void
    {
        $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');

        $this->get(route('poultry.products.index').'?q=nothingmatches')
            ->assertOk()
            ->assertSee(__('poultry.catalogue.empty_title'));
    }

    /**
     * A part with nothing applicable still renders.
     */
    #[Test]
    public function a_part_with_no_applicable_products_still_renders(): void
    {
        $this->productDosedFor($this->cattleSpecies(), 'Amoxivet');

        $this->get(route('poultry.products.index'))
            ->assertOk()
            ->assertSee(__('poultry.catalogue.empty_title'))
            ->assertDontSee('Amoxivet');
    }

    // ------------------------------------------------------------- cross-link out

    /**
     * A card links to the product detail page in the Drugs part.
     *
     * One URL per product is the point. A second detail page scoped to 15 rows
     * would split the catalogue's inbound links and double the maintenance for
     * no reader-facing gain.
     */
    #[Test]
    public function a_card_links_to_the_drugs_product_page(): void
    {
        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');

        $this->get(route('poultry.products.index'))
            ->assertOk()
            ->assertSee(route('products.show', $product), false);
    }

    /**
     * The disease the drug treats links back into the part's disease page.
     *
     * This is the cross-link that ties the section together: a vet on a poultry
     * disease page reaches the drugs, and the drug card reaches back.
     */
    #[Test]
    public function a_card_links_to_the_part_s_disease_page(): void
    {
        $disease = Disease::factory()->poultry()->named('Coccidiosis')->create();
        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $product->diseases()->attach($disease);

        $this->get(route('poultry.products.index'))
            ->assertOk()
            ->assertSee(route('poultry.diseases.show', $disease->slug), false);
    }

    /**
     * A large-animal disease link does not render on the poultry page, and the
     * poultry one does not render on the large-animal page.
     */
    #[Test]
    public function a_card_only_links_to_its_own_parts_diseases(): void
    {
        $poultryDisease = Disease::factory()->poultry()->named('Coccidiosis')->create();
        $cattleDisease = Disease::factory()->largeAnimals()->named('Mastitis')->create();

        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $product->diseases()->attach([$poultryDisease->id, $cattleDisease->id]);

        $component = $this->poultryComponent();
        $rendered = $component->instance()->products->first()->diseases->pluck('slug');

        $this->assertSame([$poultryDisease->slug], $rendered->all());
    }

    // ------------------------------------------------------------- the Drugs part

    /**
     * `/drugs/products` still lists the whole catalogue.
     *
     * The inclusion rule is a scope, not a global one. Applying it model-wide
     * would silently cut the Drugs part from 30 products to 15 and 15 again,
     * which no test on the new pages would notice.
     */
    #[Test]
    public function the_drugs_catalogue_is_unaffected(): void
    {
        $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        $this->productDosedFor($this->cattleSpecies(), 'Baytril');
        Product::factory()->create(['trade_name' => 'Ketofen', 'slug' => 'ketofen']);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Amoxivet')
            ->assertSee('Baytril')
            ->assertSee('Ketofen');
    }

    // --------------------------------------------------------------- localisation

    /**
     * The page renders in Arabic.
     *
     * `hideDefaultLocaleInURL` is on, so `/ar/poultry/products` is not a route —
     * the locale is set on the application and the page is requested at its
     * canonical path, which is how the rest of this suite checks Arabic.
     */
    #[Test]
    public function the_page_renders_in_arabic(): void
    {
        $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');

        app()->setLocale('ar');

        $this->get(route('poultry.products.index'))
            ->assertOk()
            ->assertSee(__('poultry.catalogue.heading', [], 'ar'), false)
            ->assertSee(__('poultry.catalogue.withdrawal', [], 'ar'), false);
    }

    /**
     * Species and product names follow the locale, and fall back to English
     * rather than rendering blank.
     */
    #[Test]
    public function an_arabic_page_shows_arabic_names_where_they_exist(): void
    {
        $product = $this->productDosedFor($this->poultrySpecies('Broiler'), 'Amoxivet');
        $ingredient = ActiveIngredient::factory()->create([
            'name' => 'Amoxicillin',
            'name_ar' => 'أموكسيسيلين',
            'slug' => 'amoxicillin',
        ]);

        $product->activeIngredients()->attach($ingredient);

        app()->setLocale('ar');

        $this->get(route('poultry.products.index'))
            ->assertOk()
            ->assertSee('أموكسيسيلين')
            ->assertSee('Broiler');
    }

    #[Test]
    public function every_translation_key_on_the_page_resolves(): void
    {
        $product = $this->productDosedFor($this->poultrySpecies(), 'Amoxivet');
        WithdrawalPeriod::factory()
            ->for($product)
            ->for($this->poultrySpecies('Broiler'))
            ->create(['meat_days' => 28, 'egg_days' => 5]);

        $this->productDosedFor($this->poultrySpecies(), 'Baytril')->activeIngredients()
            ->attach(ActiveIngredient::factory()->create(['name' => 'Amoxicillin', 'slug' => 'amoxicillin']));

        foreach (['en', 'ar'] as $locale) {
            app()->setLocale($locale);

            $html = $this->get(route('poultry.products.index'))
                ->assertOk()
                ->getContent();

            preg_match_all('/\{\{\s*([a-z]+\.[a-z_.]+)\s*\}\}/', $html, $matches);

            $this->assertSame([], array_values(array_unique($matches[1] ?? [])), "Unresolved keys on the {$locale} page.");
        }

        app()->setLocale('en');
    }

    // ------------------------------------------------------------------- helpers

    private function poultryComponent()
    {
        return Livewire::test('products.part-catalogue', ['part' => 'poultry']);
    }

    private function largeAnimalComponent()
    {
        return Livewire::test('products.part-catalogue', ['part' => 'large-animals']);
    }

    /**
     * One component, filters applied, then read.
     *
     * Two separate `poultryComponent()` calls read as equivalent and are not:
     * the second is a fresh component with no filter set, so the assertion
     * quietly measures the unfiltered list. Every filter assertion goes through
     * here instead of setting on one component and reading off another.
     *
     * @param  array<string, string>  $state
     * @return LengthAwarePaginator<int, Product>
     */
    private function filteredPoultry(array $state)
    {
        return $this->poultryComponent()->set($state)->instance()->products;
    }

    /**
     * The product **as the page loaded it**, relations included.
     *
     * `Model::fresh()` re-queries without the component's eager-load
     * constraints, so it returns every withdrawal period rather than the part's
     * — which made the scoping test pass or fail for the wrong reason.
     */
    private function poultryProduct(Product $product): Product
    {
        return $this->poultryComponent()->instance()
            ->products->firstWhere('id', $product->id);
    }

    private function productDosedFor(Species $species, ?string $tradeName = null): Product
    {
        $product = $tradeName === null
            ? Product::factory()->create()
            : Product::factory()->create(['trade_name' => $tradeName, 'slug' => Str::slug($tradeName)]);

        Dosage::factory()->for($product)->for($species)->create();

        return $product;
    }

    /**
     * Reused across a test rather than created on every call.
     *
     * `species.slug` is unique, so a helper that inserted each time blew up as
     * soon as one test dosed two products for the same bird.
     */
    private function poultrySpecies(string $name = 'Broiler'): Species
    {
        return $this->speciesFor($name, Species::DOMAIN_POULTRY);
    }

    private function cattleSpecies(string $name = 'Cattle'): Species
    {
        return $this->speciesFor($name, Species::DOMAIN_LARGE_ANIMAL);
    }

    private function speciesFor(string $name, string $domain): Species
    {
        return Species::query()->firstOrCreate(
            ['slug' => Str::slug($name)],
            [
                'name' => $name,
                'name_ar' => $name,
                'domain' => $domain,
                'is_active' => true,
            ],
        );
    }
}
