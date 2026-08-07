<?php

namespace App\Filament\Resources\LargeAnimals\Findings\Pages;

use App\Filament\Resources\LargeAnimals\Findings\FindingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFindings extends ListRecords
{
    protected static string $resource = FindingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
