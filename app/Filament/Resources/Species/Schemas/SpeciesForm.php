<?php

namespace App\Filament\Resources\Species\Schemas;

use App\Models\Species;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SpeciesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('domain')
                    ->label('Section')
                    ->options(Species::domainOptions())
                    ->default(Species::DOMAIN_LARGE_ANIMAL)
                    ->required()
                    ->native(false),

                TextInput::make('name')
                    ->required(),
                TextInput::make('name_ar')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('description_ar')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
