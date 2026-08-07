<?php

namespace App\Filament\Resources\LargeAnimals\Findings\Schemas;

use App\Models\LargeAnimals\Finding;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FindingInfolist
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
                        TextEntry::make('category')
                            ->placeholder('-'),
                        TextEntry::make('ontology_type')
                            ->placeholder('-'),
                        TextEntry::make('parent.display_name')
                            ->placeholder('-'),
                        TextEntry::make('slug')
                            ->placeholder('-'),
                    ]),

                Section::make('Flags & Timestamps')
                    ->columnSpan(1)
                    ->schema([
                        IconEntry::make('is_noisy')
                            ->boolean(),
                        IconEntry::make('is_general_sign')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (Finding $record): bool => $record->trashed()),
                    ]),

            ]);
    }
}
