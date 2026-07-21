<?php

namespace App\Filament\Resources\ProductSubmissions\Pages;

use App\Actions\Products\ApproveProductSubmissionAction;
use App\Enums\SubmissionStatus;
use App\Filament\Resources\ProductSubmissions\ProductSubmissionResource;
use App\Models\ProductSubmission;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewProductSubmission extends ViewRecord
{
    protected static string $resource = ProductSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (ProductSubmission $record): bool => $record->status === SubmissionStatus::Pending)
                    ->requiresConfirmation()
                    ->modalIcon(Heroicon::OutlinedCheckBadge)
                    ->modalHeading('Approve Product Submission')
                    ->modalDescription('This will create the product and all related records from the submitted data.')
                    ->action(function (ProductSubmission $record, ApproveProductSubmissionAction $action): void {
                        try {
                            $action->execute($record);
                            Notification::make()
                                ->title('Product submission approved and created successfully.')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Failed to approve submission: '.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (ProductSubmission $record): bool => $record->status === SubmissionStatus::Pending)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Reason for rejection')
                            ->required()
                            ->maxLength(5000),
                    ])
                    ->action(function (ProductSubmission $record, array $data): void {
                        $record->update([
                            'status' => SubmissionStatus::Rejected,
                            'admin_notes' => $data['admin_notes'],
                        ]);

                        Notification::make()
                            ->title('Submission rejected.')
                            ->warning()
                            ->send();
                    }),
            ])
                ->label('Review')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->button(),
        ];
    }
}
