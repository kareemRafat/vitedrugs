<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('trade_name'),
                        TextEntry::make('trade_name_ar')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        TextEntry::make('product_type')
                            ->badge()
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make('Classification')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('company.name')
                            ->label('Company')
                            ->placeholder('-'),
                        TextEntry::make('dosageForm.name')
                            ->label('Dosage Form')
                            ->placeholder('-'),
                        TextEntry::make('package_size')
                            ->placeholder('-'),
                        TextEntry::make('storage_conditions')
                            ->placeholder('-'),
                    ]),

                Section::make('Description')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('description')
                            ->placeholder('-'),
                        TextEntry::make('description_ar')
                            ->placeholder('-'),
                    ]),

                Section::make('Timestamps')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (Product $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}
