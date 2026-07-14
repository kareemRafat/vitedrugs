<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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

                TextColumn::make('product_type')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('package_size'),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->date()
                    ->sortable(),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
