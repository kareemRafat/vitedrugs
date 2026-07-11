<?php

namespace App\Filament\Resources\ImportJobs\Pages;

use App\Filament\Resources\ImportJobs\ImportJobResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewImportJob extends ViewRecord
{
    protected static string $resource = ImportJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
