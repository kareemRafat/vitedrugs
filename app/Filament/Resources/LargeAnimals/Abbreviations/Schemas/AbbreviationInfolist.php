<?php

namespace App\Filament\Resources\LargeAnimals\Abbreviations\Schemas;

use App\Models\LargeAnimals\Abbreviation;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AbbreviationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Details')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('abbreviation'),
                        TextEntry::make('full_term'),
                        TextEntry::make('category')
                            ->placeholder('-'),
                    ]),

                Section::make('Description')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('description')
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
                            ->visible(fn (Abbreviation $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}
