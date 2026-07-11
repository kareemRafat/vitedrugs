<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Product;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlternativesRelationManager extends RelationManager
{
    protected static string $relationship = 'alternatives';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('alternative_product_id')
                ->label('Alternative Product')
                ->options(
                    Product::query()->pluck('trade_name', 'id')
                )
                ->searchable()
                ->required(),

            Select::make('type')
                ->options([
                    'commercial' => 'Commercial',
                    'therapeutic' => 'Therapeutic',
                    'economic' => 'Economic',
                ])
                ->required(),

            Textarea::make('notes'),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('alternativeProduct.trade_name')
                    ->label('Alternative'),

                TextColumn::make('type')
                    ->badge(),

            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}
