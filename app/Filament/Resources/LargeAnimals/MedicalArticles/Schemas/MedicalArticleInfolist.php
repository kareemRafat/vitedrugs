<?php

namespace App\Filament\Resources\LargeAnimals\MedicalArticles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MedicalArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('title_ar')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        TextEntry::make('disease.name')
                            ->placeholder('-'),
                        TextEntry::make('species')
                            ->placeholder('-'),
                        TextEntry::make('species_ar')
                            ->placeholder('-'),
                        TextEntry::make('article_type')
                            ->placeholder('-'),
                        IconEntry::make('is_published')
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

                Section::make('Summary')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('summary')
                            ->placeholder('-'),
                        TextEntry::make('summary_ar')
                            ->placeholder('-'),
                    ]),

            ]);
    }
}
