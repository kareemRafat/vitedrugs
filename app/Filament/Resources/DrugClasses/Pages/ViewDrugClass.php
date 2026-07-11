<?php

namespace App\Filament\Resources\DrugClasses\Pages;

use App\Filament\Resources\DrugClasses\DrugClassResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDrugClass extends ViewRecord
{
    protected static string $resource = DrugClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
