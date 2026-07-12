<?php

namespace App\Filament\Widgets;

use App\Models\DosageForm;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopDosageFormsTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top Dosage Forms')
            ->query(
                DosageForm::withCount('products')
                    ->orderByDesc('products_count')
                    ->take(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Dosage Form')
                    ->searchable(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
