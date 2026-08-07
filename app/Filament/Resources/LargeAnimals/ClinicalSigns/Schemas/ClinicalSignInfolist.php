<?php

namespace App\Filament\Resources\LargeAnimals\ClinicalSigns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClinicalSignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Identifiers')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('canonical_name'),
                        TextEntry::make('display_name'),
                        TextEntry::make('display_name_ar')
                            ->placeholder('-'),
                        TextEntry::make('stage')
                            ->placeholder('-'),
                        TextEntry::make('severity_level_id')
                            ->placeholder('-'),
                    ]),

                Section::make('Relationships')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('anatomicalStructure.display_name')
                            ->placeholder('-'),
                        TextEntry::make('finding.display_name')
                            ->placeholder('-'),
                        TextEntry::make('modifier.display_label')
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
