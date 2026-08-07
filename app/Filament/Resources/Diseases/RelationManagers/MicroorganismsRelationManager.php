<?php

namespace App\Filament\Resources\Diseases\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MicroorganismsRelationManager extends RelationManager
{
    protected static string $relationship = 'microorganisms';

    protected static ?string $title = 'Microorganisms';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Microorganism')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.role')
                    ->badge()
                    ->label('Role'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->options([
                                'cause' => 'Cause',
                                'associated' => 'Associated',
                            ])
                            ->default('cause'),
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
