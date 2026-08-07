<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DiseaseClassificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Classification')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('disease.name'),
                        TextEntry::make('infectiousness')
                            ->placeholder('-'),
                        TextEntry::make('transmissibility')
                            ->placeholder('-'),
                        TextEntry::make('etiology_type')
                            ->placeholder('-'),
                        TextEntry::make('oie_category')
                            ->placeholder('-'),
                    ]),

                Section::make('Patterns')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('occurrence_patterns')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('disease_courses')
                            ->badge()
                            ->placeholder('-'),
                        IconEntry::make('notifiable')
                            ->boolean(),
                        IconEntry::make('zoonotic')
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
