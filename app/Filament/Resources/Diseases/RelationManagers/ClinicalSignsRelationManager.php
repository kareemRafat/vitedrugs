<?php

namespace App\Filament\Resources\Diseases\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClinicalSignsRelationManager extends RelationManager
{
    protected static string $relationship = 'clinicalSigns';

    protected static ?string $title = 'Clinical Signs';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('display_name')
            ->columns([
                TextColumn::make('display_name')
                    ->label('Clinical Sign')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.weight')
                    ->numeric()
                    ->label('Weight')
                    ->sortable(),
                BooleanColumn::make('pivot.is_specific')
                    ->label('Specific'),
                BooleanColumn::make('pivot.is_required')
                    ->label('Required'),
                BooleanColumn::make('pivot.is_pathognomonic')
                    ->label('Pathognomonic'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['display_name'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('weight')
                            ->label('Weight')
                            ->numeric()
                            ->nullable(),
                        Toggle::make('is_specific')
                            ->label('Specific'),
                        Toggle::make('is_required')
                            ->label('Required'),
                        Toggle::make('is_pathognomonic')
                            ->label('Pathognomonic'),
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
