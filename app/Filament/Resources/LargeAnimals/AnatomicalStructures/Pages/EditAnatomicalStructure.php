<?php

namespace App\Filament\Resources\LargeAnimals\AnatomicalStructures\Pages;

use App\Filament\Resources\LargeAnimals\AnatomicalStructures\AnatomicalStructureResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAnatomicalStructure extends EditRecord
{
    protected static string $resource = AnatomicalStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
