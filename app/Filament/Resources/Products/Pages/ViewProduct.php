<?php

namespace App\Filament\Resources\Products\Pages;

use App\Enums\ProductStatus;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->visible(fn (): bool => $this->record?->isPending() ?? false)
                    ->requiresConfirmation()
                    ->modalIcon(Heroicon::OutlinedCheckBadge)
                    ->modalHeading('Approve Product')
                    ->modalDescription('This product will become visible on the public site.')
                    ->action(function (): void {
                        $this->record->update([
                            'status' => ProductStatus::Approved,
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Product approved successfully.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (): bool => $this->record?->isPending() ?? false)
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Reason for rejection')
                            ->required()
                            ->maxLength(5000),
                    ])
                    ->action(function (array $data): void {
                        $this->record->update([
                            'status' => ProductStatus::Rejected,
                            'admin_notes' => $data['admin_notes'],
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Product rejected.')
                            ->warning()
                            ->send();
                    }),
            ])
                ->label('Review')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->button()
                ->visible(fn (): bool => $this->record?->isPending() ?? false),

            EditAction::make(),
        ];
    }
}
