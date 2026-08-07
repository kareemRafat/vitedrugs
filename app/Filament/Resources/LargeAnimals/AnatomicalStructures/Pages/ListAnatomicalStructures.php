<?php

namespace App\Filament\Resources\LargeAnimals\AnatomicalStructures\Pages;

use App\Filament\Resources\LargeAnimals\AnatomicalStructures\AnatomicalStructureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnatomicalStructures extends ListRecords
{
    protected static string $resource = AnatomicalStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
