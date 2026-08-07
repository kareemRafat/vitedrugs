<?php

namespace App\Filament\Resources\LargeAnimals\BodySystems\Pages;

use App\Filament\Resources\LargeAnimals\BodySystems\BodySystemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBodySystems extends ListRecords
{
    protected static string $resource = BodySystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
