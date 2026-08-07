<?php

namespace App\Filament\Clusters\Diseases;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DiseasesCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBugAnt;

    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
