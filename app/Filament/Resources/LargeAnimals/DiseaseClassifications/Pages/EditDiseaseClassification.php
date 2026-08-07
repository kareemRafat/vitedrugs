<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages;

use App\Filament\Resources\LargeAnimals\DiseaseClassifications\DiseaseClassificationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDiseaseClassification extends EditRecord
{
    protected static string $resource = DiseaseClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
