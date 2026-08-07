<?php

namespace App\Filament\Clusters\Search;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SearchCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
