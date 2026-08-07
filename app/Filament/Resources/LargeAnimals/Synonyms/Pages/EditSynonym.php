<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Pages;

use App\Filament\Resources\LargeAnimals\Synonyms\SynonymResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSynonym extends EditRecord
{
    protected static string $resource = SynonymResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
