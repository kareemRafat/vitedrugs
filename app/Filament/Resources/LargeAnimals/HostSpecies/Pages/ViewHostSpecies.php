<?php

namespace App\Filament\Resources\LargeAnimals\HostSpecies\Pages;

use App\Filament\Resources\LargeAnimals\HostSpecies\HostSpeciesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHostSpecies extends ViewRecord
{
    protected static string $resource = HostSpeciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
