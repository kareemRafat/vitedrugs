<?php

namespace App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages;

use App\Filament\Resources\LargeAnimals\ClinicalSigns\ClinicalSignResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClinicalSign extends EditRecord
{
    protected static string $resource = ClinicalSignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
