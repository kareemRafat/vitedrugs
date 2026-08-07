<?php

namespace App\Filament\Resources\LargeAnimals\DifferentialSyndromes\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiseasesRelationManager extends RelationManager
{
    protected static string $relationship = 'diseases';

    protected static ?string $title = 'Diseases';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Disease')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.boost_score')
                    ->numeric()
                    ->label('Boost Score')
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('boost_score')
                            ->label('Boost Score')
                            ->numeric()
                            ->nullable(),
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
