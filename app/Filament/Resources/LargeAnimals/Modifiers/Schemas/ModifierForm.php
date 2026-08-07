<?php

namespace App\Filament\Resources\LargeAnimals\Modifiers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ModifierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('canonical_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('display_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('display_name_ar')
                    ->maxLength(255),

                TextInput::make('type')
                    ->maxLength(255),

                TextInput::make('modifier_group')
                    ->maxLength(255),

                Toggle::make('is_noisy')
                    ->default(false),

            ]);
    }
}
