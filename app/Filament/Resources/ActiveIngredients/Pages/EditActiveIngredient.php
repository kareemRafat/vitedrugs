<?php

namespace App\Filament\Resources\ActiveIngredients\Pages;

use App\Filament\Resources\ActiveIngredients\ActiveIngredientResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditActiveIngredient extends EditRecord
{
    protected static string $resource = ActiveIngredientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
