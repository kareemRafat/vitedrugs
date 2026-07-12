<?php

namespace App\Filament\Resources\DrugInteractions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DrugInteractionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('active_ingredient_id')
                    ->label('Active Ingredient')
                    ->relationship('activeIngredient', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('interacting_drug')
                    ->required()
                    ->maxLength(255),

                Select::make('severity')
                    ->options([
                        'minor' => 'Minor',
                        'moderate' => 'Moderate',
                        'major' => 'Major',
                    ])
                    ->required()
                    ->default('moderate'),

                Textarea::make('effect')
                    ->columnSpanFull(),

                Textarea::make('recommendation')
                    ->columnSpanFull(),
            ]);
    }
}
