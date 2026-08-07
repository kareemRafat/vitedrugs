<?php

namespace App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DifferentialSyndromeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->required(),

                TextInput::make('name_ar'),

                Textarea::make('description')
                    ->columnSpanFull(),

            ]);
    }
}
