<?php

namespace App\Filament\Resources\LargeAnimals\BodySystems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BodySystemForm
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

            ]);
    }
}
