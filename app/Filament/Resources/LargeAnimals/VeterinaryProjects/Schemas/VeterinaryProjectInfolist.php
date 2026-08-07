<?php

namespace App\Filament\Resources\LargeAnimals\VeterinaryProjects\Schemas;

use App\Models\LargeAnimals\VeterinaryProject;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VeterinaryProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Basic Information')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('slug'),
                        TextEntry::make('project_type')
                            ->placeholder('-'),
                        TextEntry::make('sector')
                            ->placeholder('-'),
                        IconEntry::make('featured')
                            ->boolean(),
                        IconEntry::make('is_published')
                            ->boolean(),
                    ]),

                Section::make('Timestamps')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn (VeterinaryProject $record): bool => $record->trashed()),
                    ]),

                Section::make('Summary')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('summary')
                            ->placeholder('-'),
                    ]),

            ]);
    }
}
