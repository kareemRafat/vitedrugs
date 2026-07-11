<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

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

            ]);
    }
}