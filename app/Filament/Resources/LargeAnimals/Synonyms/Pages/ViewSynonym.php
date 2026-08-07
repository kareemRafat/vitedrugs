<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Pages;

use App\Filament\Resources\LargeAnimals\Synonyms\SynonymResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSynonym extends ViewRecord
{
    protected static string $resource = SynonymResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
