<?php

namespace App\Filament\Resources\LargeAnimals\Abbreviations\Pages;

use App\Filament\Resources\LargeAnimals\Abbreviations\AbbreviationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbbreviations extends ListRecords
{
    protected static string $resource = AbbreviationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
