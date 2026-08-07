<?php

namespace App\Filament\Resources\LargeAnimals\VeterinaryProjects\Pages;

use App\Filament\Resources\LargeAnimals\VeterinaryProjects\VeterinaryProjectResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVeterinaryProject extends ViewRecord
{
    protected static string $resource = VeterinaryProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
