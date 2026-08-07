<?php

namespace App\Filament\Resources\LargeAnimals\Microorganisms\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MicroorganismInfolist
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
                        TextEntry::make('normalized_name')
                            ->placeholder('-'),
                        TextEntry::make('microorganism_type')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        IconEntry::make('is_pathogenic')
                            ->boolean(),
                        IconEntry::make('is_topic')
                            ->boolean(),
                    ]),

                Section::make('Taxonomy')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('kingdom')
                            ->placeholder('-'),
                        TextEntry::make('phylum')
                            ->placeholder('-'),
                        TextEntry::make('class')
                            ->placeholder('-'),
                        TextEntry::make('order')
                            ->placeholder('-'),
                        TextEntry::make('family')
                            ->placeholder('-'),
                        TextEntry::make('genus')
                            ->placeholder('-'),
                        TextEntry::make('species')
                            ->placeholder('-'),
                    ]),

                Section::make('Additional Data')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('searchable_text')
                            ->placeholder('-'),
                        TextEntry::make('json_data')
                            ->placeholder('-'),
                        TextEntry::make('source_json_file')
                            ->placeholder('-'),
                        TextEntry::make('tags')
                            ->badge()
                            ->placeholder('-'),
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
