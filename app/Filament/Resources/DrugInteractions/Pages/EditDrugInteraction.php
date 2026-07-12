<?php

namespace App\Filament\Resources\DrugInteractions\Pages;

use App\Filament\Resources\DrugInteractions\DrugInteractionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDrugInteraction extends EditRecord
{
    protected static string $resource = DrugInteractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
