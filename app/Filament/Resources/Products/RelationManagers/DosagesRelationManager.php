<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Species;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DosagesRelationManager extends RelationManager
{
    protected static string $relationship = 'dosages';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('species_id')
                ->options(
                    Species::query()->pluck('name', 'id')
                )
                ->searchable()
                ->required(),

            Textarea::make('dosage')
                ->required(),

            TextInput::make('route'),

            TextInput::make('duration'),

            Textarea::make('notes'),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('species.name')
                    ->label('Species'),

                TextColumn::make('dosage')
                    ->limit(50),

                TextColumn::make('route'),

                TextColumn::make('duration'),

                TextColumn::make('notes')
                    ->limit(50),

            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}
