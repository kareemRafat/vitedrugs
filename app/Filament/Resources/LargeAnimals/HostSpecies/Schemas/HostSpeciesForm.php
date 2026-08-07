<?php

namespace App\Filament\Resources\LargeAnimals\HostSpecies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HostSpeciesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('canonical_name')
                    ->required(),

                TextInput::make('display_name')
                    ->required(),

                TextInput::make('display_name_ar'),

                Select::make('taxonomy_group')
                    ->options([
                        'ruminant' => 'Ruminant',
                        'poultry' => 'Poultry',
                        'fish' => 'Fish',
                    ]),

                Toggle::make('is_domestic'),

                Toggle::make('is_wildlife'),

                Toggle::make('is_human'),

            ]);
    }
}
