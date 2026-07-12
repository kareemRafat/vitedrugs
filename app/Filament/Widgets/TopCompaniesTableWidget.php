<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopCompaniesTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top Companies')
            ->query(
                Company::withCount('products')
                    ->orderByDesc('products_count')
                    ->take(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Company')
                    ->searchable(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
