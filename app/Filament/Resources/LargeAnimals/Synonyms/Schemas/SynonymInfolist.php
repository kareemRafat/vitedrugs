<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Schemas;

use App\Models\Disease;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Str;

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
                            ->state(fn ($record): string => Str::afterLast($record->synonymable_type, '\\')),
                        TextEntry::make('synonymable')
                            ->label('Linked To')
                            ->state(fn ($record): string => (string) ($record->synonymable
                                ? (Str::afterLast($record->synonymable_type, '\\').' — '.(($record->synonymable instanceof Disease)
                                    ? $record->synonymable->name
                                    : ($record->synonymable->display_name ?? $record->synonymable->getKey())))
                                : '-')),
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
