<?php

namespace App\Filament\Resources\DrugInteractions\Pages;

use App\Filament\Resources\DrugInteractions\DrugInteractionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDrugInteraction extends ViewRecord
{
    protected static string $resource = DrugInteractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
