<?php

namespace App\Filament\Widgets;

use App\Models\ActiveIngredient;
use App\Models\Company;
use App\Models\Disease;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Products', Product::count())
                ->description('Total products in database')
                ->color('primary'),

            Stat::make('Companies', Company::count())
                ->description('Manufacturers, agents & distributors')
                ->color('success'),

            Stat::make('Diseases', Disease::count())
                ->description('Conditions treated')
                ->color('warning'),

            Stat::make('Active Ingredients', ActiveIngredient::count())
                ->description('Pharmaceutical agents')
                ->color('info'),
        ];
    }
}
