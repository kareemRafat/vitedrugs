<?php

namespace App\Filament\Resources\LargeAnimals\Microorganisms\Pages;

use App\Filament\Resources\LargeAnimals\Microorganisms\MicroorganismResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMicroorganism extends ViewRecord
{
    protected static string $resource = MicroorganismResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
