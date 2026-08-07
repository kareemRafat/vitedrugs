<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Schemas;

use App\Models\LargeAnimals\Synonym;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SynonymInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Details')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('term'),
                        TextEntry::make('normalized_term')
                            ->placeholder('-'),
                        TextEntry::make('source_type')
                            ->placeholder('-'),
                        TextEntry::make('synonymable_type')
                            ->placeholder('-'),
                        TextEntry::make('synonymable_id')
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
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (Synonym $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}
