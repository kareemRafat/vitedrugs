<?php

namespace App\Filament\Resources\LargeAnimals\Abbreviations\Pages;

use App\Filament\Resources\LargeAnimals\Abbreviations\AbbreviationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAbbreviation extends ViewRecord
{
    protected static string $resource = AbbreviationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
