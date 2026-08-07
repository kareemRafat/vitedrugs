<?php

namespace App\Filament\Resources\LargeAnimals\Modifiers\Pages;

use App\Filament\Resources\LargeAnimals\Modifiers\ModifierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListModifiers extends ListRecords
{
    protected static string $resource = ModifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
