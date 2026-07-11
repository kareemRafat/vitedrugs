<?php

namespace App\Filament\Resources\Contraindications\Pages;

use App\Filament\Resources\Contraindications\ContraindicationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContraindication extends ViewRecord
{
    protected static string $resource = ContraindicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
