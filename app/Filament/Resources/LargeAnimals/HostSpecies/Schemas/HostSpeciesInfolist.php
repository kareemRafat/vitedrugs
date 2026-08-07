<?php

namespace App\Filament\Resources\LargeAnimals\HostSpecies\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HostSpeciesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('canonical_name'),
                        TextEntry::make('display_name'),
                        TextEntry::make('display_name_ar')
                            ->placeholder('-'),
                        TextEntry::make('taxonomy_group')
                            ->placeholder('-'),
                    ]),

                Section::make('Attributes')
                    ->columnSpan(1)
                    ->schema([
                        IconEntry::make('is_domestic')
                            ->boolean(),
                        IconEntry::make('is_wildlife')
                            ->boolean(),
                        IconEntry::make('is_human')
                            ->boolean(),
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

            ]);
    }
}
