<?php

namespace App\Filament\Resources\LargeAnimals\DiseaseClassifications\Pages;

use App\Filament\Resources\LargeAnimals\DiseaseClassifications\DiseaseClassificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiseaseClassification extends CreateRecord
{
    protected static string $resource = DiseaseClassificationResource::class;
}
