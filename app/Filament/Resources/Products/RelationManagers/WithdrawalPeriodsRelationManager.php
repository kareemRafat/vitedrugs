<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WithdrawalPeriodsRelationManager extends RelationManager
{
    protected static string $relationship = 'withdrawalPeriods';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('species_id')
                ->label('Species')
                ->relationship('species', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('meat_days')
                ->numeric(),

            TextInput::make('milk_days')
                ->numeric(),

            TextInput::make('egg_days')
                ->numeric(),

            Textarea::make('notes'),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('species.name')
                    ->label('Species'),

                TextColumn::make('meat_days'),

                TextColumn::make('milk_days'),

                TextColumn::make('egg_days'),

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
