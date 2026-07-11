<?php

namespace App\Filament\Resources\Diseases\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $title = 'Products';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('trade_name')
            ->columns([

                Tables\Columns\TextColumn::make('trade_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company'),

                Tables\Columns\TextColumn::make('pivot.sort_order')
                    ->label('Sort Order'),

            ])
            ->headerActions([

                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['trade_name'])
                    ->form(fn (AttachAction $action): array => [

                        $action->getRecordSelect(),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),

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