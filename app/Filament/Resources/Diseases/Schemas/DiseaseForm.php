<?php

namespace App\Filament\Resources\Diseases\Schemas;

use App\Models\Disease;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DiseaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // First, because every other surface is scoped by it: a Disease
                // saved into the wrong domain silently vanishes from the poultry
                // section.
                Select::make('domain')
                    ->label('Section')
                    ->options(Disease::domainOptions())
                    ->default(Disease::DOMAIN_LARGE_ANIMAL)
                    ->required()
                    ->native(false),

                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('name_ar'),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('description')
                    ->columnSpanFull(),

                Textarea::make('description_ar')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->default(true),

                Toggle::make('is_zoonotic')
                    ->default(false),

                Toggle::make('is_internal')
                    ->default(false),

            ]);
    }
}
