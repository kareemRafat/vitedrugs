<?php

namespace App\Filament\Resources\Indications\Pages;

use App\Filament\Resources\Indications\IndicationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIndication extends ViewRecord
{
    protected static string $resource = IndicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
