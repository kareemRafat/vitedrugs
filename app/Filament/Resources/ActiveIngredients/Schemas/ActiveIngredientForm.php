<?php

namespace App\Filament\Resources\ActiveIngredients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ActiveIngredientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

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

                Textarea::make('contraindications')
                    ->label('Contraindications')
                    ->columnSpanFull(),

                Textarea::make('precautions')
                    ->label('Precautions')
                    ->columnSpanFull(),

                Textarea::make('side_effects')
                    ->label('Side Effects')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->default(true),

            ]);
    }
}