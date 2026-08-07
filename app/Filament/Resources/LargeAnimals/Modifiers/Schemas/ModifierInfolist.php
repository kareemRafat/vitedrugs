<?php

namespace App\Filament\Resources\LargeAnimals\Modifiers\Schemas;

use App\Models\LargeAnimals\Modifier;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ModifierInfolist
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
                        TextEntry::make('type')
                            ->placeholder('-'),
                        TextEntry::make('modifier_group')
                            ->placeholder('-'),
                        IconEntry::make('is_noisy')
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
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (Modifier $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}
