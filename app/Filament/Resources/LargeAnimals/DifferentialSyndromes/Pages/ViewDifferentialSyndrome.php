<?php

namespace App\Filament\Resources\LargeAnimals\DifferentialSyndromes\Pages;

use App\Filament\Resources\LargeAnimals\DifferentialSyndromes\DifferentialSyndromeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDifferentialSyndrome extends ViewRecord
{
    protected static string $resource = DifferentialSyndromeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
