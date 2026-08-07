<?php

namespace App\Filament\Resources\Diseases\RelationManagers;

use App\Filament\Resources\LargeAnimals\DiseaseClassifications\Schemas\DiseaseClassificationForm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DiseaseClassificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'diseaseClassification';

    protected static ?string $title = 'Disease Classification';

    public function form(Schema $schema): Schema
    {
        return DiseaseClassificationForm::configure($schema, includeDisease: false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('infectiousness'),
                TextColumn::make('transmissibility'),
                TextColumn::make('etiology_type'),
                TextColumn::make('oie_category'),
                IconColumn::make('notifiable')
                    ->boolean(),
                IconColumn::make('zoonotic')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add classification')
                    ->visible(fn (): bool => $this->getOwnerRecord()->diseaseClassification()->doesntExist()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
