<?php

namespace App\Filament\Resources\ActiveIngredients\Pages;

use App\Filament\Resources\ActiveIngredients\ActiveIngredientResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewActiveIngredient extends ViewRecord
{
    protected static string $resource = ActiveIngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
