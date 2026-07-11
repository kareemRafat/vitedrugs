<?php

namespace App\Filament\Resources\ActiveIngredients\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DrugClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'drugClasses';

    protected static ?string $title = 'Drug Classes';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_ar'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

            ])
            ->headerActions([

                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns([
                        'name',
                        'name_ar',
                    ]),

            ])
            ->recordActions([

                DetachAction::make(),

            ])
            ->toolbarActions([

                DetachBulkAction::make(),

            ]);
    }
}