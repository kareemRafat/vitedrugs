<?php

namespace App\Filament\Resources\LargeAnimals\Findings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FindingForm
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

                TextInput::make('category')
                    ->maxLength(255),

                TextInput::make('ontology_type')
                    ->maxLength(255),

                Select::make('parent_id')
                    ->relationship('parent', 'display_name')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Toggle::make('is_noisy')
                    ->default(false),

                Toggle::make('is_general_sign')
                    ->default(false),

                TextInput::make('slug')
                    ->maxLength(255),

            ]);
    }
}
