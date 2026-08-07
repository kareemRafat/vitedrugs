<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DiseaseClassificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('disease_id')
                    ->relationship('disease', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('infectiousness')
                    ->options([
                        'high' => 'High',
                        'moderate' => 'Moderate',
                        'low' => 'Low',
                    ]),

                Select::make('transmissibility')
                    ->options([
                        'high' => 'High',
                        'moderate' => 'Moderate',
                        'low' => 'Low',
                    ]),

                Select::make('etiology_type')
                    ->options([
                        'viral' => 'Viral',
                        'bacterial' => 'Bacterial',
                        'fungal' => 'Fungal',
                        'parasitic' => 'Parasitic',
                        'protozoal' => 'Protozoal',
                        'other' => 'Other',
                    ]),

                TagsInput::make('occurrence_patterns'),

                TagsInput::make('disease_courses'),

                Toggle::make('notifiable'),

                Toggle::make('zoonotic'),

                TextInput::make('oie_category'),

            ]);
    }
}
