<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Company;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('company_type')
                    ->badge(),

                TextColumn::make('pivot.role')
                    ->badge()
                    ->label('Role'),

            ])

            ->headerActions([

                AttachAction::make()
                    ->form([

                        Select::make('recordId')
                            ->label('Company')
                            ->options(
                                Company::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required(),

                        Select::make('role')
                            ->options([
                                'manufacturer' => 'Manufacturer',
                                'agent' => 'Agent',
                                'distributor' => 'Distributor',
                                'marketing' => 'Marketing',
                            ])
                            ->required(),

                    ]),

            ])

            ->recordActions([

                DetachAction::make(),

            ]);
    }
}
