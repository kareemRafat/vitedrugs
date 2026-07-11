<?php

namespace App\Filament\Resources\Contraindications\Pages;

use App\Filament\Resources\Contraindications\ContraindicationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContraindications extends ListRecords
{
    protected static string $resource = ContraindicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
