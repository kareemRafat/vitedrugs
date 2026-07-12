<?php

namespace App\Filament\Widgets;

use App\Models\Disease;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopDiseasesTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top Diseases')
            ->query(
                Disease::withCount('products')
                    ->orderByDesc('products_count')
                    ->take(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Disease')
                    ->searchable(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
