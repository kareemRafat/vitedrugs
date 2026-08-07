<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use stdClass;

class DiseaseClassificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->state(fn (stdClass $rowLoop, $livewire): string => (string) ($livewire->getTableRecords()->firstItem() + $rowLoop->iteration - 1)),
                TextColumn::make('disease.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('infectiousness')
                    ->searchable(),
                TextColumn::make('transmissibility')
                    ->searchable(),
                TextColumn::make('etiology_type')
                    ->searchable(),
                IconColumn::make('notifiable')
                    ->boolean(),
                IconColumn::make('zoonotic')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('etiology_type')
                    ->options([
                        'viral' => 'Viral',
                        'bacterial' => 'Bacterial',
                        'fungal' => 'Fungal',
                        'parasitic' => 'Parasitic',
                        'protozoal' => 'Protozoal',
                        'other' => 'Other',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
