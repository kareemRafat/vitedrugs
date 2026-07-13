<?php

namespace App\Filament\Resources\ActiveIngredients\Schemas;

use App\Models\ActiveIngredient;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActiveIngredientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('name_ar')
                            ->placeholder('-'),
                        TextEntry::make('slug'),
                        IconEntry::make('is_active')
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
                            ->visible(fn (ActiveIngredient $record): bool => $record->trashed()),
                    ]),

                Section::make('Description')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('description')
                            ->placeholder('-'),
                        TextEntry::make('description_ar')
                            ->placeholder('-'),
                    ]),

                Section::make('Clinical Data')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('indications')
                            ->placeholder('-'),
                        TextEntry::make('contraindications')
                            ->placeholder('-'),
                        TextEntry::make('precautions')
                            ->placeholder('-'),
                        TextEntry::make('side_effects')
                            ->placeholder('-'),
                    ]),

            ]);
    }
}
