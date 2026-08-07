<?php

namespace App\Filament\Resources\LargeAnimals\BodySystems\Pages;

use App\Filament\Resources\LargeAnimals\BodySystems\BodySystemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBodySystem extends ViewRecord
{
    protected static string $resource = BodySystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
