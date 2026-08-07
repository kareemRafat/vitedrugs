<?php

namespace App\Filament\Resources\LargeAnimals\AnatomicalStructures\Pages;

use App\Filament\Resources\LargeAnimals\AnatomicalStructures\AnatomicalStructureResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAnatomicalStructure extends ViewRecord
{
    protected static string $resource = AnatomicalStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
