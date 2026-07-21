<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductStatus;
use App\Models\Product;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->state(fn (stdClass $rowLoop, $livewire): string => (string) ($livewire->getTableRecords()->firstItem() + $rowLoop->iteration - 1)),

                TextColumn::make('trade_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('manufacturer')
                    ->label('Company')
                    ->limit(20)
                    ->state(fn (Product $record): ?string => $record->manufacturer->first()?->name)
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas('manufacturer', fn ($q) => $q->where('name', 'like', "%{$search}%"))),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (ProductStatus $state): string => $state->color())
                    ->sortable(),

                TextColumn::make('product_type')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('package_size'),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('createdBy.name')
                    ->label('Submitted By')
                    ->searchable()
                    ->sortable()
                    ->placeholder('System'),

                TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProductStatus::class),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => ProductStatus::Approved,
                            'reviewed_at' => now(),
                        ]))
                        ->after(fn () => Notification::make()->title('Selected products approved.')->success()->send()),

                    BulkAction::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => ProductStatus::Rejected,
                            'reviewed_at' => now(),
                        ]))
                        ->after(fn () => Notification::make()->title('Selected products rejected.')->warning()->send()),

                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
