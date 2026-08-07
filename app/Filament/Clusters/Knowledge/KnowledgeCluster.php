<?php

namespace App\Filament\Clusters\Knowledge;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class KnowledgeCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
