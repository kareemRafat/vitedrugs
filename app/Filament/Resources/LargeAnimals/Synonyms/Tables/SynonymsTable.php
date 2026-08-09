<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Tables;

use App\Models\Disease;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use stdClass;
use Str;

class SynonymsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->state(fn (stdClass $rowLoop, $livewire): string => (string) ($livewire->getTableRecords()->firstItem() + $rowLoop->iteration - 1)),
                TextColumn::make('term')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('normalized_term')
                    ->searchable(),
                TextColumn::make('source_type')
                    ->searchable(),
                TextColumn::make('synonymable')
                    ->label('Linked To')
                    ->state(function ($record): string {
                        $related = $record->synonymable;

                        if (! $related) {
                            return Str::afterLast($record->synonymable_type, '\\').' #'.$record->synonymable_id;
                        }

                        $title = $related instanceof Disease
                            ? $related->name
                            : $related->display_name ?? $related->getKey();

                        return Str::afterLast($record->synonymable_type, '\\').' — '.$title;
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
