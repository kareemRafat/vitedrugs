<?php

namespace App\Filament\Resources\LargeAnimals\Microorganisms\RelationManagers;

use App\Enums\AntibioticSensitivity;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
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
                TextColumn::make('name')
                    ->label('Active Ingredient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.sensitivity')
                    ->badge()
                    ->label('Sensitivity')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? ucfirst($state) : null),
                TextColumn::make('pivot.notes')
                    ->label('Notes')
                    ->limit(60),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('sensitivity')
                            ->options(AntibioticSensitivity::class)
                            ->nullable(),
                        Textarea::make('notes')
                            ->columnSpanFull(),
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
