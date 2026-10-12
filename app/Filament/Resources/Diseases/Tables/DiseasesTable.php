<?php

namespace App\Filament\Resources\Diseases\Tables;

use App\Models\Disease;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use stdClass;

class DiseasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('#')
                    ->state(fn (stdClass $rowLoop, $livewire): string => (string) ($livewire->getTableRecords()->firstItem() + $rowLoop->iteration - 1)),

                TextColumn::make('domain')
                    ->badge()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('name_ar')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('slug')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->boolean(),

                IconColumn::make('is_zoonotic')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_internal')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                SelectFilter::make('domain')
                    ->native(false)
                    ->options(Disease::domainOptions())
                    ->label('Section'),

                TernaryFilter::make('is_zoonotic')
                    ->native(false),

                TernaryFilter::make('is_internal')
                    ->native(false),
            ])
            // The section filter is the main way an editor navigates this table,
            // so keep it visible instead of behind a Filters button, and apply it
            // on selection rather than behind an explicit Apply.
            ->filtersLayout(FiltersLayout::AboveContent)
            ->deferFilters(false)
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
