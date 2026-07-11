<?php

namespace App\Filament\Resources\ImportJobs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImportJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('source_file')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('source_type')
                    ->badge(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('total_products')
                    ->numeric(),

                TextColumn::make('imported_products')
                    ->numeric(),

                TextColumn::make('failed_products')
                    ->numeric(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

            ])
            ->filters([
                //
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
