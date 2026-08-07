<?php

namespace App\Filament\Resources\LargeAnimals\Abbreviations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AbbreviationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('abbreviation')
                    ->required()
                    ->maxLength(255),

                TextInput::make('full_term')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),

                TextInput::make('category')
                    ->maxLength(255),

            ]);
    }
}
