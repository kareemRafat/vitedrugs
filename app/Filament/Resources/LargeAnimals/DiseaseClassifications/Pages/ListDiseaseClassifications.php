<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages;

use App\Filament\Resources\LargeAnimals\DiseaseClassifications\DiseaseClassificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDiseaseClassifications extends ListRecords
{
    protected static string $resource = DiseaseClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
