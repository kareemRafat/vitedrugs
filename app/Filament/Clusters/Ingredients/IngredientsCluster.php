<?php

namespace App\Filament\Clusters\Ingredients;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class IngredientsCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}