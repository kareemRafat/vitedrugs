<?php

namespace App\Filament\Resources\LargeAnimals\BodySystems\Schemas;

use App\Models\LargeAnimals\BodySystem;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BodySystemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Details')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('canonical_name'),
                        TextEntry::make('display_name'),
                        TextEntry::make('display_name_ar')
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
                            ->visible(fn (BodySystem $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}
