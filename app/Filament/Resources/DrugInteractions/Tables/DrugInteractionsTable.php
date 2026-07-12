<?php

namespace App\Filament\Resources\DrugInteractions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DrugInteractionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activeIngredient.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('interacting_drug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('severity')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'major' => 'danger',
                        'moderate' => 'warning',
                        'minor' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
