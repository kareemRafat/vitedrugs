<?php

namespace App\Filament\Resources\Diseases\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HostSpeciesRelationManager extends RelationManager
{
    protected static string $relationship = 'hostSpecies';

    protected static ?string $title = 'Host Species';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('display_name')
            ->columns([
                TextColumn::make('display_name')
                    ->label('Host Species')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.role')
                    ->badge()
                    ->label('Role'),
                TextColumn::make('pivot.susceptibility')
                    ->label('Susceptibility'),
                BooleanColumn::make('pivot.is_primary_host')
                    ->label('Primary'),
                BooleanColumn::make('pivot.is_reservoir')
                    ->label('Reservoir'),
                BooleanColumn::make('pivot.is_vector')
                    ->label('Vector'),
                BooleanColumn::make('pivot.is_carrier')
                    ->label('Carrier'),
                BooleanColumn::make('pivot.is_incidental_host')
                    ->label('Incidental'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['display_name'])
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->options([
                                'primary' => 'Primary',
                                'secondary' => 'Secondary',
                                'incidental' => 'Incidental',
                            ])
                            ->nullable(),
                        Select::make('susceptibility')
                            ->options([
                                'high' => 'High',
                                'moderate' => 'Moderate',
                                'low' => 'Low',
                                'resistant' => 'Resistant',
                            ])
                            ->nullable(),
                        Toggle::make('is_primary_host')
                            ->label('Primary Host'),
                        Toggle::make('is_reservoir')
                            ->label('Reservoir'),
                        Toggle::make('is_vector')
                            ->label('Vector'),
                        Toggle::make('is_carrier')
                            ->label('Carrier'),
                        Toggle::make('is_incidental_host')
                            ->label('Incidental Host'),
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
