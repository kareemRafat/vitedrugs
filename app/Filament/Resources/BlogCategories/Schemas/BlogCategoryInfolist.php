<?php

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BlogCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('name_ar')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        IconEntry::make('is_active')
                            ->boolean(),
                    ]),

                Section::make('Timestamps')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
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
