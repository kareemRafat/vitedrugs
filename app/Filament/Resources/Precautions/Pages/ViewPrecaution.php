<?php

namespace App\Filament\Resources\Precautions\Pages;

use App\Filament\Resources\Precautions\PrecautionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrecaution extends ViewRecord
{
    protected static string $resource = PrecautionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
