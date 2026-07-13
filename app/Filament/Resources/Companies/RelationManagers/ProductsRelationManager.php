<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('trade_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('dosageForm.name')
                    ->label('Dosage Form')
                    ->badge(),

                TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

            ])
            ->headerActions([
                AttachAction::make()
                    ->recordSelectSearchColumns(['trade_name', 'trade_name_ar'])
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->options([
                                'manufacturer' => 'Manufacturer',
                                'agent' => 'Agent',
                                'distributor' => 'Distributor',
                            ])
                            ->required(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DetachAction::make(),
            ]);
    }
}
