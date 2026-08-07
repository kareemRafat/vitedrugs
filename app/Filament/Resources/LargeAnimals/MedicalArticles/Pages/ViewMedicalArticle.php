<?php

namespace App\Filament\Resources\LargeAnimals\MedicalArticles\Pages;

use App\Filament\Resources\LargeAnimals\MedicalArticles\MedicalArticleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMedicalArticle extends ViewRecord
{
    protected static string $resource = MedicalArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
