<?php

namespace App\Filament\Resources\LargeAnimals\ClinicalSigns\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClinicalSignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('anatomical_structure_id')
                    ->relationship('anatomicalStructure', 'display_name')
                    ->searchable()
                    ->preload(),

                Select::make('finding_id')
                    ->relationship('finding', 'display_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('modifier_id')
                    ->relationship('modifier', 'display_name')
                    ->searchable()
                    ->preload(),

                TextInput::make('canonical_name')
                    ->required(),

                TextInput::make('display_name')
                    ->required(),

                TextInput::make('display_name_ar'),

                TextInput::make('stage'),

                TextInput::make('severity_level_id'),

                TextInput::make('semantic_slug'),

            ]);
    }
}
