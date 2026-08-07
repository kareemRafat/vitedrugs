<?php

namespace App\Filament\Clusters\Filter;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class FilterCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
