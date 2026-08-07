<?php

namespace App\Filament\Resources\LargeAnimals\Microorganisms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class MicroorganismForm
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

                TextInput::make('normalized_name'),

                TextInput::make('kingdom'),

                TextInput::make('phylum'),

                TextInput::make('class'),

                TextInput::make('order'),

                TextInput::make('family'),

                TextInput::make('genus'),

                TextInput::make('species'),

                Select::make('microorganism_type')
                    ->options([
                        'bacteria' => 'Bacteria',
                        'virus' => 'Virus',
                        'fungus' => 'Fungus',
                        'parasite' => 'Parasite',
                    ]),

                Toggle::make('is_pathogenic'),

                Textarea::make('searchable_text')
                    ->columnSpanFull(),

                Textarea::make('json_data')
                    ->columnSpanFull()
                    ->json(),

                TextInput::make('source_json_file'),

                TagsInput::make('tags'),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Toggle::make('is_topic'),

            ]);
    }
}
