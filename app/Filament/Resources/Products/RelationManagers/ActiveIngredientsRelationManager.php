<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActiveIngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'activeIngredients';

    protected static ?string $title = 'Active Ingredients';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Active Ingredient')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.strength')
                    ->label('Strength')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pivot.unit')
                    ->label('Unit')
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),

                        TextInput::make('strength')
                            ->label('Strength')
                            ->numeric()
                            ->nullable(),

                        TextInput::make('unit')
                            ->label('Unit')
                            ->maxLength(50)
                            ->nullable(),
                    ]),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
