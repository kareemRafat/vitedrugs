<?php

namespace App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages;

use App\Filament\Resources\LargeAnimals\ClinicalSigns\ClinicalSignResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClinicalSign extends ViewRecord
{
    protected static string $resource = ClinicalSignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
