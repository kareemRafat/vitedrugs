<?php

namespace App\Filament\Resources\LargeAnimals\VeterinaryProjects\Pages;

use App\Filament\Resources\LargeAnimals\VeterinaryProjects\VeterinaryProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVeterinaryProjects extends ListRecords
{
    protected static string $resource = VeterinaryProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
