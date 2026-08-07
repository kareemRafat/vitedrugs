<?php

namespace App\Filament\Resources\LargeAnimals\HostSpecies\Pages;

use App\Filament\Resources\LargeAnimals\HostSpecies\HostSpeciesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHostSpecies extends ListRecords
{
    protected static string $resource = HostSpeciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
