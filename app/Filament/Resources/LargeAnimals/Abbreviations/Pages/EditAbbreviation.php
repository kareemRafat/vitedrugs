<?php

namespace App\Filament\Resources\LargeAnimals\Abbreviations\Pages;

use App\Filament\Resources\LargeAnimals\Abbreviations\AbbreviationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAbbreviation extends EditRecord
{
    protected static string $resource = AbbreviationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
