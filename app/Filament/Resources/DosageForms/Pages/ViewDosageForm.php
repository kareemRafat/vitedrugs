<?php

namespace App\Filament\Resources\DosageForms\Pages;

use App\Filament\Resources\DosageForms\DosageFormResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDosageForm extends ViewRecord
{
    protected static string $resource = DosageFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
