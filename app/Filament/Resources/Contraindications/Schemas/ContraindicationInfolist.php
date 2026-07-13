<?php

namespace App\Filament\Resources\Contraindications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContraindicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('product_id'),
                        TextEntry::make('sort_order')
                            ->numeric(),
                    ]),

                Section::make('Timestamps')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
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

            ]);
    }
}
