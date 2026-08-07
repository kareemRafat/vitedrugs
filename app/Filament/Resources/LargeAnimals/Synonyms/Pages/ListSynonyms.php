<?php

namespace App\Filament\Resources\LargeAnimals\Synonyms\Pages;

use App\Filament\Resources\LargeAnimals\Synonyms\SynonymResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSynonyms extends ListRecords
{
    protected static string $resource = SynonymResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
