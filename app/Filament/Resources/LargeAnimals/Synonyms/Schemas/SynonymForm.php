<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Schemas;

use App\Models\Disease;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SynonymForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('term')
                    ->required()
                    ->maxLength(255),

                TextInput::make('normalized_term')
                    ->maxLength(255),

                TextInput::make('source_type')
                    ->maxLength(255),

                Select::make('synonymable_type')
                    ->options([
                        Disease::class => 'Disease',
                    ])
                    ->required(),

                TextInput::make('synonymable_id')
                    ->required(),

            ]);
    }
}
