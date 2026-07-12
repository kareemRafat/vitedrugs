<?php

namespace App\Filament\Pages;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\Product;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class DataQuality extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected Width|string|null $maxContentWidth = 'full';

    protected string $view = 'filament.pages.data-quality';

    protected ?string $heading = 'Orphan Records';

    protected ?string $subheading = 'Entities missing required associations';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (): array => [
                ['icon' => 'heroicon-o-cube', 'label' => 'Products without Diseases', 'count' => Product::doesntHave('diseases')->count()],
                ['icon' => 'heroicon-o-beaker', 'label' => 'Products without Ingredients', 'count' => Product::doesntHave('activeIngredients')->count()],
                ['icon' => 'heroicon-o-building-office-2', 'label' => 'Companies without Products', 'count' => Company::doesntHave('products')->count()],
                ['icon' => 'heroicon-o-bug-ant', 'label' => 'Diseases without Products', 'count' => Disease::doesntHave('products')->count()],
                ['icon' => 'heroicon-o-magnifying-glass', 'label' => 'Ingredients without Products', 'count' => ActiveIngredient::doesntHave('products')->count()],
            ])
            ->paginated(false)
            ->columns([
                TextColumn::make('label')
                    ->label('Issue')
                    ->icon(fn (array $record): string => $record['icon'])
                    ->searchable(),
                TextColumn::make('count')
                    ->label('Count')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (array $record): string => $record['count'] > 0 ? 'Needs attention' : 'All good')
                    ->color(fn (string $state): string => $state === 'Needs attention' ? 'danger' : 'success')
                    ->icon(fn (string $state): string => $state === 'Needs attention' ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle'),
            ]);
    }
}
