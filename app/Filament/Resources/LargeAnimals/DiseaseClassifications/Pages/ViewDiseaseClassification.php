<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages;

use App\Filament\Resources\LargeAnimals\DiseaseClassifications\DiseaseClassificationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDiseaseClassification extends ViewRecord
{
    protected static string $resource = DiseaseClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
