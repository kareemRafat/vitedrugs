<?php

namespace App\Filament\Resources\LargeAnimals\MedicalArticles\Pages;

use App\Filament\Resources\LargeAnimals\MedicalArticles\MedicalArticleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedicalArticles extends ListRecords
{
    protected static string $resource = MedicalArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
