<?php

namespace App\Filament\Resources\ContactSubmissions\Tables;

use App\Enums\ContactCategory;
use App\Models\ContactSubmission;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

class ContactSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedEnvelope)
                    ->falseIcon(Heroicon::OutlinedEnvelopeOpen)
                    ->trueColor('gray')
                    ->falseColor('warning')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('From')
                    ->searchable()
                    ->sortable()
                    ->weight(fn (ContactSubmission $record): string => $record->is_read ? 'normal' : 'bold')
                    ->color(fn (ContactSubmission $record): ?string => $record->is_read ? null : 'warning'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(30),

                TextColumn::make('role')
                    ->label('Role')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (ContactSubmission $record): ?string => $record->category?->color())
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('locale')
                    ->label('Lang')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->dateTimeTooltip('M j, Y \a\t H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_read')
                    ->label('Status')
                    ->options([
                        '0' => 'Unread',
                        '1' => 'Read',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        '0' => $query->unread(),
                        '1' => $query->read(),
                        default => $query,
                    }),

                SelectFilter::make('category')
                    ->label('Category')
                    ->options(ContactCategory::class)
                    ->searchable(),

                SelectFilter::make('locale')
                    ->label('Language')
                    ->options([
                        'en' => 'English',
                        'ar' => 'Arabic',
                    ]),

                Filter::make('received_between')
                    ->label('Received between')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('toggleRead')
                    ->label(fn (ContactSubmission $record): string => $record->is_read ? 'Mark as unread' : 'Mark as read')
                    ->icon(fn (ContactSubmission $record): Heroicon => $record->is_read
                        ? Heroicon::OutlinedEnvelope
                        : Heroicon::OutlinedCheckCircle)
                    ->color(fn (ContactSubmission $record): string => $record->is_read ? 'warning' : 'success')
                    ->action(function (ContactSubmission $record, Component $livewire): void {
                        $record->is_read ? $record->markAsUnread() : $record->markAsRead();

                        $livewire->dispatch('refresh-sidebar');
                    }),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markRead')
                        ->label('Mark as read')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->color('success')
                        ->action(function (Collection $records, Component $livewire): void {
                            $records->each(fn (ContactSubmission $record) => $record->markAsRead());

                            $livewire->dispatch('refresh-sidebar');
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('markUnread')
                        ->label('Mark as unread')
                        ->icon(Heroicon::OutlinedEnvelope)
                        ->color('warning')
                        ->action(function (Collection $records, Component $livewire): void {
                            $records->each(fn (ContactSubmission $record) => $record->markAsUnread());

                            $livewire->dispatch('refresh-sidebar');
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50]);
    }
}
