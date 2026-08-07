<?php

namespace App\Filament\Resources\LargeAnimals\AnatomicalStructures\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnatomicalStructureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('body_system_id')
                    ->relationship('bodySystem', 'display_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('parent_id')
                    ->relationship('parent', 'display_name')
                    ->searchable()
                    ->preload()
                    ->nullable(),

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

            ]);
    }
}
