<?php

namespace Tests\Feature;

use App\Enums\ContactCategory;
use App\Enums\UserRole;
use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use App\Filament\Resources\ContactSubmissions\Pages\ListContactSubmissions;
use App\Filament\Resources\ContactSubmissions\Pages\ViewContactSubmission;
use App\Models\ContactSubmission;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The inbox used to render an `EditAction` on a resource that has no `form()`
 * and no edit route, so the button could never work. These tests lock in the
 * replacement: a real unread badge, read-on-open, and view/delete only — which
 * is what plans/durgs/filament/PLAN.md documented the intent to be.
 */
class ContactSubmissionAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    // --------------------------------------------------------------- the badge

    #[Test]
    public function the_badge_counts_unread_messages_only(): void
    {
        ContactSubmission::factory()->unread()->count(3)->create();
        ContactSubmission::factory()->read()->count(5)->create();

        $this->assertSame('3', ContactSubmissionResource::getNavigationBadge());
    }

    /**
     * `null` rather than `'0'` so Filament renders no badge at all instead of an
     * empty counter sitting next to "Messages".
     */
    #[Test]
    public function the_badge_is_hidden_when_nothing_is_unread(): void
    {
        $this->assertNull(ContactSubmissionResource::getNavigationBadge());

        ContactSubmission::factory()->read()->count(2)->create();

        $this->assertNull(ContactSubmissionResource::getNavigationBadge());
    }

    #[Test]
    public function the_badge_drops_once_the_message_is_opened(): void
    {
        $submission = ContactSubmission::factory()->unread()->create();

        $this->assertSame('1', ContactSubmissionResource::getNavigationBadge());

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ViewContactSubmission::class, ['record' => $submission->getKey()]);

        $this->assertNull(ContactSubmissionResource::getNavigationBadge());
    }

    // --------------------------------------------------------- opening a message

    #[Test]
    public function opening_a_message_marks_it_read_and_stamps_read_at(): void
    {
        $submission = ContactSubmission::factory()->unread()->create();

        $this->assertFalse($submission->is_read);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ViewContactSubmission::class, ['record' => $submission->getKey()]);

        $submission->refresh();

        $this->assertTrue($submission->is_read);
        $this->assertNotNull($submission->read_at);
    }

    /**
     * Reading is idempotent — a re-render must not overwrite the moment the
     * message was actually first opened.
     */
    #[Test]
    public function opening_an_already_read_message_does_not_restamp_read_at(): void
    {
        $submission = ContactSubmission::factory()->read()->create();
        $readAt = $submission->read_at;

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ViewContactSubmission::class, ['record' => $submission->getKey()]);

        $this->assertEquals($readAt, $submission->fresh()->read_at);
    }

    // ---------------------------------------------------------------- read-only

    #[Test]
    public function stored_messages_cannot_be_edited(): void
    {
        $submission = ContactSubmission::factory()->unread()->create();

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ViewContactSubmission::class, ['record' => $submission->getKey()])
            ->assertActionDoesNotExist('edit')
            ->assertActionExists('delete');
    }

    #[Test]
    public function the_message_body_is_still_readable_on_the_view_page(): void
    {
        $submission = ContactSubmission::factory()->unread()->create([
            'message' => 'The withdrawal period on this monograph looks wrong.',
        ]);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ViewContactSubmission::class, ['record' => $submission->getKey()])
            ->assertSee('The withdrawal period on this monograph looks wrong.');
    }

    // -------------------------------------------------------------- the toggles

    #[Test]
    public function the_table_toggles_a_message_back_to_unread(): void
    {
        $submission = ContactSubmission::factory()->read()->create();

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListContactSubmissions::class)
            ->callAction(TestAction::make('toggleRead')->table($submission));

        $submission->refresh();

        $this->assertFalse($submission->is_read);
        $this->assertNull($submission->read_at);
        $this->assertSame('1', ContactSubmissionResource::getNavigationBadge());
    }

    #[Test]
    public function the_table_marks_an_unread_message_as_read(): void
    {
        $submission = ContactSubmission::factory()->unread()->create();

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListContactSubmissions::class)
            ->callAction(TestAction::make('toggleRead')->table($submission));

        $this->assertTrue($submission->fresh()->is_read);
    }

    // ----------------------------------------------------------------- the table

    #[Test]
    public function the_list_exposes_the_status_category_language_and_date_filters(): void
    {
        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListContactSubmissions::class)
            ->assertTableFilterExists('is_read')
            ->assertTableFilterExists('category')
            ->assertTableFilterExists('locale')
            ->assertTableFilterExists('received_between');
    }

    #[Test]
    public function filtering_by_status_narrows_the_records(): void
    {
        $unread = ContactSubmission::factory()->unread()->create(['name' => 'Unread Sender']);
        $read = ContactSubmission::factory()->read()->create(['name' => 'Read Sender']);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListContactSubmissions::class)
            ->filterTable('is_read', '0')
            ->assertCanSeeTableRecords([$unread])
            ->assertCanNotSeeTableRecords([$read]);
    }

    #[Test]
    public function filtering_by_category_narrows_the_records(): void
    {
        $correction = ContactSubmission::factory()
            ->category(ContactCategory::MonographCorrection)
            ->create(['name' => 'Correction Sender']);

        $other = ContactSubmission::factory()
            ->category(ContactCategory::GeneralFeedback)
            ->create(['name' => 'Feedback Sender']);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListContactSubmissions::class)
            ->filterTable('category', ContactCategory::MonographCorrection->value)
            ->assertCanSeeTableRecords([$correction])
            ->assertCanNotSeeTableRecords([$other]);
    }

    /**
     * The table used to call `paginated(false)` and loaded every row into the
     * page. Ten per page with a 10/25/50 chooser is the replacement.
     *
     * Timestamps are staggered so the newest-first default sort is deterministic
     * and the two oldest rows are provably the ones pushed off the page.
     */
    #[Test]
    public function the_list_is_paginated_instead_of_loading_every_row(): void
    {
        $records = collect(range(0, 11))->map(fn (int $daysAgo): ContactSubmission => ContactSubmission::factory()->create([
            'created_at' => now()->subDays($daysAgo),
        ]));

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListContactSubmissions::class)
            ->assertCanSeeTableRecords($records->take(10))
            ->assertCanNotSeeTableRecords($records->skip(10));
    }

    #[Test]
    public function the_list_sorts_newest_first(): void
    {
        $older = ContactSubmission::factory()->create(['created_at' => now()->subWeek()]);
        $newer = ContactSubmission::factory()->create(['created_at' => now()]);

        Livewire::actingAs($this->admin(), 'admin')
            ->test(ListContactSubmissions::class)
            ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
    }
}
