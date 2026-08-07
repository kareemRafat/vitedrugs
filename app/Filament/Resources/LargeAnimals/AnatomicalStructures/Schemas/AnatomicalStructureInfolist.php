<?php

namespace App\Filament\Resources\LargeAnimals\AnatomicalStructures\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnatomicalStructureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Details')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('bodySystem.display_name')
                            ->placeholder('-'),
                        TextEntry::make('parent.display_name')
                            ->placeholder('-'),
                        TextEntry::make('canonical_name'),
                        TextEntry::make('display_name'),
                        TextEntry::make('display_name_ar')
                            ->placeholder('-'),
                        TextEntry::make('type')
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
