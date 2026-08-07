<?php

namespace App\Filament\Resources\LargeAnimals\VeterinaryProjects\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VeterinaryProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Textarea::make('summary')
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->columnSpanFull()
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('projects'),

                Select::make('project_type')
                    ->options([
                        'feasibility_study' => 'Feasibility Study',
                        'investment_guide' => 'Investment Guide',
                        'guideline' => 'Guideline',
                    ])
                    ->required(),

                TextInput::make('sector'),

                Toggle::make('featured'),

                Toggle::make('is_published')
                    ->default(true),

            ]);
    }
}
