<?php

namespace App\Filament\Resources\ActiveIngredients\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DrugInteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'drugInteractions';

    protected static ?string $title = 'Drug Interactions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('interacting_drug')
                    ->required()
                    ->maxLength(255),

                Select::make('severity')
                    ->options([
                        'minor' => 'Minor',
                        'moderate' => 'Moderate',
                        'major' => 'Major',
                    ])
                    ->default('moderate')
                    ->required(),

                Textarea::make('effect')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('recommendation')
                    ->columnSpanFull(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('interacting_drug')
                    ->searchable(),

                Tables\Columns\TextColumn::make('severity')
                    ->badge(),

                Tables\Columns\TextColumn::make('effect')
                    ->limit(60),

            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}