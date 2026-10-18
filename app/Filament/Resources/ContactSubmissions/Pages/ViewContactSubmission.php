<?php

namespace App\Filament\Resources\ContactSubmissions\Pages;

use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewContactSubmission extends ViewRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    /**
     * Opening the message is what marks it read. Guarded so a re-mount does not
     * stamp a new `read_at` over the original one.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->markAsRead();

        $this->dispatch('refresh-sidebar');
    }

    /**
     * Stored messages are read-only, matching the documented intent in
     * plans/durgs/filament/PLAN.md — view and delete only, never edit.
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleRead')
                ->label(fn (): string => $this->record->is_read ? 'Mark as unread' : 'Mark as read')
                ->icon(fn (): Heroicon => $this->record->is_read
                    ? Heroicon::OutlinedEnvelope
                    : Heroicon::OutlinedCheckCircle)
                ->color(fn (): string => $this->record->is_read ? 'warning' : 'success')
                ->action(function (): void {
                    $this->record->is_read
                        ? $this->record->markAsUnread()
                        : $this->record->markAsRead();

                    $this->dispatch('refresh-sidebar');
                }),

            DeleteAction::make(),
        ];
    }
}
