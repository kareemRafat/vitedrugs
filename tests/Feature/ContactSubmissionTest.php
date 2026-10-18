<?php

namespace Tests\Feature;

use App\Enums\ContactCategory;
use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The landing form and the standalone /contact form are the same Livewire
 * component in two variants. These tests pin the contract both depend on:
 * `category` stores the stable enum value for filtering, while `subject` keeps
 * the label the visitor actually saw — which is also why `subject` never had to
 * be made nullable.
 */
class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private const LANDING_VALID = [
        'variant' => 'landing',
        'name' => 'Dr. Sarah Jenkins',
        'email' => 's.jenkins@vetclinic.org',
        'role' => 'DVM, Clinical Toxicologist',
        'category' => ContactCategory::MonographCorrection->value,
        'message' => 'The withdrawal period on this monograph looks wrong.',
    ];

    /**
     * Livewire v4 dropped `assertHasErrors()` / `assertHasNoErrors()`, and this is
     * a plain Livewire component so it cannot use Filament's form assertions
     * either. The error bag still lives on the component instance.
     */
    private function assertRejected(Testable $component, string ...$fields): void
    {
        $bag = $component->instance()->getErrorBag();

        foreach ($fields as $field) {
            $this->assertTrue($bag->has($field), "Expected a validation error on [{$field}].");
        }
    }

    private function assertAccepted(Testable $component): void
    {
        $bag = $component->instance()->getErrorBag();

        $this->assertTrue($bag->isEmpty(), 'Unexpected validation errors: '.$bag->toJson());
    }

    // ------------------------------------------------------- the landing variant

    #[Test]
    public function the_landing_form_persists_the_role_category_and_locale(): void
    {
        app()->setLocale('ar');

        $component = Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->call('submit');

        $this->assertAccepted($component);

        $submission = ContactSubmission::sole();

        $this->assertSame('Dr. Sarah Jenkins', $submission->name);
        $this->assertSame('DVM, Clinical Toxicologist', $submission->role);
        $this->assertSame('ar', $submission->locale);
        $this->assertSame(ContactCategory::MonographCorrection, $submission->category);
        $this->assertFalse($submission->is_read);
        $this->assertNull($submission->read_at);
    }

    /**
     * The split that keeps the schema additive: the enum value is what the admin
     * filters on, the label is the display snapshot.
     */
    #[Test]
    public function category_stores_the_enum_value_and_subject_stores_the_seen_label(): void
    {
        app()->setLocale('en');

        Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->call('submit');

        $submission = ContactSubmission::sole();

        $this->assertSame('monograph_correction', $submission->getRawOriginal('category'));
        $this->assertSame('Monograph Correction', $submission->subject);
    }

    /**
     * The snapshot is taken in the visitor's language, so the Arabic inbox shows
     * back exactly what the Arabic visitor saw.
     */
    #[Test]
    public function the_subject_snapshot_follows_the_submitting_locale(): void
    {
        app()->setLocale('ar');

        Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->call('submit');

        $this->assertSame('تصحيح نشرة الدواء', ContactSubmission::sole()->subject);
    }

    #[Test]
    public function the_landing_form_requires_a_category(): void
    {
        $component = Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->set('category', null)
            ->call('submit');

        $this->assertRejected($component, 'category');
        $this->assertSame(0, ContactSubmission::count());
    }

    #[Test]
    public function the_landing_form_rejects_a_category_outside_the_enum(): void
    {
        $component = Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->set('category', 'not-a-real-category')
            ->call('submit');

        $this->assertRejected($component, 'category');
        $this->assertSame(0, ContactSubmission::count());
    }

    #[Test]
    public function the_landing_form_requires_a_name_and_an_email(): void
    {
        $component = Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->set('name', '')
            ->set('email', 'not-an-email')
            ->call('submit');

        $this->assertRejected($component, 'name', 'email');
        $this->assertSame(0, ContactSubmission::count());
    }

    #[Test]
    public function the_landing_form_treats_the_role_as_optional(): void
    {
        $component = Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->set('role', '')
            ->call('submit');

        $this->assertAccepted($component);
        $this->assertNull(ContactSubmission::sole()->role);
    }

    // -------------------------------------------------------- the page variant

    /**
     * Regression gate: /contact has no category field, so it must keep accepting
     * a free-text subject and leave `category` null.
     */
    #[Test]
    public function the_page_variant_still_stores_a_free_text_subject(): void
    {
        $component = Livewire::test('contact.contact-form')
            ->set([
                'name' => 'Kareem',
                'email' => 'kareem@example.com',
                'subject' => 'Question about the formulary',
                'message' => 'Hello there.',
            ])
            ->call('submit');

        $this->assertAccepted($component);

        $submission = ContactSubmission::sole();

        $this->assertSame('Question about the formulary', $submission->subject);
        $this->assertNull($submission->category);
        $this->assertNull($submission->role);
        $this->assertFalse($submission->is_read);
    }

    #[Test]
    public function the_page_variant_still_requires_a_subject(): void
    {
        $component = Livewire::test('contact.contact-form')
            ->set([
                'name' => 'Kareem',
                'email' => 'kareem@example.com',
                'subject' => '',
                'message' => 'Hello there.',
            ])
            ->call('submit');

        $this->assertRejected($component, 'subject');
        $this->assertSame(0, ContactSubmission::count());
    }

    // ------------------------------------------------------------------ the model

    #[Test]
    public function marking_read_stamps_read_at_and_is_idempotent(): void
    {
        $submission = ContactSubmission::factory()->unread()->create();

        $submission->markAsRead();

        $this->assertTrue($submission->is_read);
        $firstReadAt = $submission->read_at;

        $submission->markAsRead();

        $this->assertEquals($firstReadAt, $submission->fresh()->read_at);
    }

    #[Test]
    public function marking_unread_clears_read_at(): void
    {
        $submission = ContactSubmission::factory()->read()->create();

        $submission->markAsUnread();

        $this->assertFalse($submission->is_read);
        $this->assertNull($submission->read_at);
    }

    #[Test]
    public function the_unread_and_read_scopes_partition_the_table(): void
    {
        ContactSubmission::factory()->unread()->count(2)->create();
        ContactSubmission::factory()->read()->count(3)->create();

        $this->assertSame(2, ContactSubmission::unread()->count());
        $this->assertSame(3, ContactSubmission::read()->count());
        $this->assertSame(5, ContactSubmission::count());
    }

    #[Test]
    public function every_category_has_a_label_in_both_locales(): void
    {
        foreach (['en', 'ar'] as $locale) {
            app()->setLocale($locale);

            foreach (ContactCategory::cases() as $category) {
                $this->assertNotSame(
                    "messages.contact_categories.{$category->value}",
                    $category->getLabel(),
                    "Missing {$locale} label for {$category->value}.",
                );
            }
        }
    }
}
