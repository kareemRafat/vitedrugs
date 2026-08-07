<?php

namespace App\Filament\Resources\LargeAnimals\ClinicalSigns\Pages;

use App\Filament\Resources\LargeAnimals\ClinicalSigns\ClinicalSignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClinicalSign extends CreateRecord
{
    protected static string $resource = ClinicalSignResource::class;
}
