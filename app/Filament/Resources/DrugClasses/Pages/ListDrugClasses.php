<?php

namespace App\Filament\Resources\DrugClasses\Pages;

use App\Filament\Resources\DrugClasses\DrugClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDrugClasses extends ListRecords
{
    protected static string $resource = DrugClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
