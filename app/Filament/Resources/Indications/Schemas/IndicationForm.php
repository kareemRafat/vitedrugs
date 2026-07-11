<?php

namespace App\Filament\Resources\Indications\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('product_id')
                    ->label('Product')
                    ->options(
                        Product::query()
                            ->pluck('trade_name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('description_ar')
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
