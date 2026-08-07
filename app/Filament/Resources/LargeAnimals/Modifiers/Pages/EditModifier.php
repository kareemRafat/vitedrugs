<?php

namespace App\Filament\Resources\LargeAnimals\Modifiers\Pages;

use App\Filament\Resources\LargeAnimals\Modifiers\ModifierResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditModifier extends EditRecord
{
    protected static string $resource = ModifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
