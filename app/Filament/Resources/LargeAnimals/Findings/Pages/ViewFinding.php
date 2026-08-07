<?php

namespace App\Filament\Resources\LargeAnimals\Findings\Pages;

use App\Filament\Resources\LargeAnimals\Findings\FindingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFinding extends ViewRecord
{
    protected static string $resource = FindingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
