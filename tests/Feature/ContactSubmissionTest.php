<?php

namespace Tests\Feature;

use App\Enums\ContactCategory;
use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
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

    // ------------------------------------------------- the submitting state

    /**
     * Both variants share one submit button, and three things about its
     * loading state failed silently before.
     *
     * @return array<string, array{0: string}>
     */
    public static function variants(): array
    {
        return [
            'landing' => ['landing'],
            'page' => ['page'],
        ];
    }

    /**
     * Livewire already disables a submit button for the length of its own form
     * submission. The extra *untargeted* `wire:loading.attr="disabled"` on top of
     * that fired on every `wire:model.blur` in the form, so the button greyed
     * out whenever the visitor tabbed out of a field.
     */
    #[Test]
    #[DataProvider('variants')]
    public function the_submit_button_is_not_disabled_by_untargeted_loading(string $variant): void
    {
        $html = Livewire::test('contact.contact-form', ['variant' => $variant])->html();

        $this->assertStringNotContainsString(
            'wire:loading.attr="disabled"',
            $html,
            "The [{$variant}] submit button greys out on field blur again."
        );
    }

    /**
     * The button used to carry hardcoded English loading text: the /contact
     * variant shipped a literal "Sending..." that Arabic visitors saw verbatim,
     * and the landing variant shipped a bare "..." that said nothing at all.
     *
     * Asserted against the template source: the button now swaps an icon and
     * leaves the label alone, so there is no translated loading string to find
     * in the rendered page.
     */
    #[Test]
    public function the_button_carries_no_hardcoded_english_loading_text(): void
    {
        $source = (string) file_get_contents(
            resource_path('views/app/components/contact/⚡contact-form.blade.php')
        );

        $this->assertStringNotContainsString(
            '>Sending...<',
            $source,
            'The submitting label is hardcoded English rather than translated.'
        );
    }

    /**
     * The submitting state swaps the icon, it does not swap the label.
     *
     * This is the whole shape of the fix. An earlier attempt swapped the text for
     * "Sending...", which resized the button under the visitor's cursor and
     * needed a new translation in both locales to say nothing the spinner did not
     * already say.
     */
    #[Test]
    #[DataProvider('variants')]
    public function the_submit_button_swaps_its_icon_not_its_label(string $variant): void
    {
        $html = Livewire::test('contact.contact-form', ['variant' => $variant])->html();

        preg_match('~<button type="submit".*?</button>~s', $html, $button);

        $this->assertNotEmpty($button, "The [{$variant}] submit button is missing.");

        $markup = $button[0];
        $label = __('messages.pages.contact.submit');

        $this->assertSame(
            1,
            substr_count($markup, $label),
            "[{$variant}] should render the submit label exactly once."
        );

        // The label sits outside every loading directive, so the swap cannot
        // touch it. Checked by position: the label has to come first in source
        // order, ahead of the element that toggles.
        $labelPosition = strpos($markup, $label);
        $loadingPosition = strpos($markup, 'wire:loading wire:target="submit"');

        $this->assertIsInt($labelPosition, "[{$variant}] has no submit label.");
        $this->assertIsInt($loadingPosition, "[{$variant}] has no loading state.");

        $this->assertLessThan(
            $loadingPosition,
            $labelPosition,
            "[{$variant}] puts the label inside the toggled region, so it changes while submitting."
        );
    }

    /**
     * Both icons are the same size and neither may be squeezed, which is what
     * keeps them on one baseline and stops the label drifting when it wraps.
     *
     * The old arrow was the `→` glyph at the text size — 14px beside a 16px
     * spinner — so the two could not line up.
     */
    #[Test]
    #[DataProvider('variants')]
    public function both_icons_share_one_aligned_slot(string $variant): void
    {
        $html = Livewire::test('contact.contact-form', ['variant' => $variant])->html();

        preg_match('~<button type="submit".*?</button>~s', $html, $button);
        $markup = $button[0] ?? '';

        // Idle icon and loading icon are both present and both fixed-width.
        $this->assertSame(
            2,
            substr_count($markup, 'shrink-0'),
            "[{$variant}] icons need shrink-0 so the label cannot squeeze them."
        );

        $this->assertMatchesRegularExpression(
            '~wire:loading\.remove[^>]*class="[^"]*shrink-0[^"]*"[^>]*>\s*<(svg|x-lucide-send)~s',
            $markup,
            "[{$variant}] idle icon is not a fixed-size element in its own slot."
        );

        $this->assertMatchesRegularExpression(
            '~wire:loading wire:target="submit"[^>]*>\s*<svg class="animate-spin w-4 h-4"~s',
            $markup,
            "[{$variant}] loading icon is not a 16px spinner in its own slot."
        );
    }

    /**
     * Both variants hold the swap back briefly. The delay is the point: a
     * validation failure returns in well under 50ms, and without it the spinner
     * flickered on every failed submit.
     */
    #[Test]
    #[DataProvider('variants')]
    public function the_sending_state_is_held_back_so_it_does_not_flicker(string $variant): void
    {
        $html = Livewire::test('contact.contact-form', ['variant' => $variant])->html();

        $this->assertSame(
            1,
            substr_count($html, 'wire:loading wire:target="submit"'),
            "[{$variant}] should toggle exactly one sending state."
        );

        $this->assertStringContainsString(
            'wire:loading.delay.shortest',
            $html,
            "[{$variant}] flickers its sending state on a fast validation failure."
        );
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

    /**
     * The success toast is driven entirely by the `contact-sent` event, so the
     * dispatch is what proves the visitor is told the message went through.
     */
    #[Test]
    public function a_successful_submit_dispatches_contact_sent(): void
    {
        Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->call('submit')
            ->assertDispatched('contact-sent');
    }

    /**
     * The dispatch has to be registered *before* the ValidationException is
     * rethrown: Livewire swallows that exception only after the error bag is set,
     * which is what lets the already-registered effect still reach the browser.
     */
    #[Test]
    public function a_rejected_submit_dispatches_contact_failed(): void
    {
        Livewire::test('contact.contact-form', ['variant' => 'landing'])
            ->set(self::LANDING_VALID)
            ->set('email', 'not-an-email')
            ->call('submit')
            ->assertDispatched('contact-failed')
            ->assertNotDispatched('contact-sent');
    }

    #[Test]
    public function the_page_variant_reports_failure_the_same_way(): void
    {
        Livewire::test('contact.contact-form')
            ->set([
                'name' => 'Dr. Sarah Jenkins',
                'email' => 'not-an-email',
                'subject' => 'Question',
                'message' => 'Hello.',
            ])
            ->call('submit')
            ->assertDispatched('contact-failed');
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
