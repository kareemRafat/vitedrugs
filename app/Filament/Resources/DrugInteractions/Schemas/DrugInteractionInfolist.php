<?php

namespace App\Filament\Resources\DrugInteractions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DrugInteractionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Interaction Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('activeIngredient.name')
                            ->label('Active Ingredient'),
                        TextEntry::make('interacting_drug'),
                        TextEntry::make('severity')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'major' => 'danger',
                                'moderate' => 'warning',
                                'minor' => 'success',
                                default => 'gray',
                            }),
                    ]),

                Section::make('Timestamps')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),

                Section::make('Details')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('effect'),
                        TextEntry::make('recommendation')
                            ->placeholder('-'),
                    ]),

            ]);
    }
}
