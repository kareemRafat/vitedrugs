<?php

namespace App\Filament\Resources\LargeAnimals\MedicalArticles\Pages;

use App\Filament\Resources\LargeAnimals\MedicalArticles\MedicalArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalArticle extends CreateRecord
{
    protected static string $resource = MedicalArticleResource::class;
}
