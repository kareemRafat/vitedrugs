<?php

namespace App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages;

use App\Filament\Resources\LargeAnimals\ClinicalSigns\ClinicalSignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClinicalSigns extends ListRecords
{
    protected static string $resource = ClinicalSignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
