<?php

namespace App\Filament\Resources\LargeAnimals\Microorganisms\Pages;

use App\Filament\Resources\LargeAnimals\Microorganisms\MicroorganismResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMicroorganisms extends ListRecords
{
    protected static string $resource = MicroorganismResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
