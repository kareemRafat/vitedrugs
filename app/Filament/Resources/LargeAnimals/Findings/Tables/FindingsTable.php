<?php

namespace App\Filament\Resources\LargeAnimals\Findings\Tables;

use App\Models\Disease;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class FindingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')
                    ->state(fn (stdClass $rowLoop, $livewire): string => (string) ($livewire->getTableRecords()->firstItem() + $rowLoop->iteration - 1)),
                TextColumn::make('display_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('ontology_type')
                    ->searchable(),
                IconColumn::make('is_noisy')
                    ->boolean(),
                IconColumn::make('is_general_sign')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Derived, not stored. A finding like "diarrhea" is used by both
                // sections, so a single domain column would have to lie about it.
                // This answers "what does the poultry section actually use?" from
                // the links themselves, so it cannot drift out of date.
                SelectFilter::make('used_by')
                    ->label('Used by section')
                    ->options(Disease::domainOptions())
                    ->query(function (Builder $query, array $data): void {
                        // A custom query() bypasses SelectFilter's own
                        // empty-value guard, so the "All" option would arrive
                        // here as null and become whereNull('domain'), which
                        // hides every row.
                        if (blank($data['value'] ?? null)) {
                            return;
                        }

                        $query->whereHas(
                            'clinicalSigns.diseases',
                            fn (Builder $diseases): Builder => $diseases->where('domain', $data['value']),
                        );
                    }),
            ])
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
