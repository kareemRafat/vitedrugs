<?php

namespace App\Filament\Clusters\Diagnosis;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DiagnosisCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Large Animals';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
