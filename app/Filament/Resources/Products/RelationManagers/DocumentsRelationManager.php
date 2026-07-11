<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('title')
                ->required(),

            Select::make('type')
                ->options([
                    'leaflet' => 'Leaflet',
                    'datasheet' => 'Datasheet',
                    'brochure' => 'Brochure',
                    'certificate' => 'Certificate',
                ])
                ->required(),

            FileUpload::make('file_path')
                ->disk('public')
                ->directory('documents')
                ->required(),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('type')
                    ->badge(),

            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}