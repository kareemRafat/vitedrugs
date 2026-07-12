<?php

namespace App\Filament\Resources\DrugInteractions\Pages;

use App\Filament\Resources\DrugInteractions\DrugInteractionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDrugInteractions extends ListRecords
{
    protected static string $resource = DrugInteractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
