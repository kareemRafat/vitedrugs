<?php

namespace App\Filament\Resources\LargeAnimals\MedicalArticles\Pages;

use App\Filament\Resources\LargeAnimals\MedicalArticles\MedicalArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicalArticle extends EditRecord
{
    protected static string $resource = MedicalArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
