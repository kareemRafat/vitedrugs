<?php

namespace App\Filament\Resources\ActiveIngredients\Pages;

use App\Filament\Resources\ActiveIngredients\ActiveIngredientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActiveIngredients extends ListRecords
{
    protected static string $resource = ActiveIngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
