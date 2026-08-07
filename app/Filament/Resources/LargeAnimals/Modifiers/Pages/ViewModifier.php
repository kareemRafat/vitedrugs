<?php

namespace App\Filament\Resources\LargeAnimals\Modifiers\Pages;

use App\Filament\Resources\LargeAnimals\Modifiers\ModifierResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewModifier extends ViewRecord
{
    protected static string $resource = ModifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function getHeading(): string
    {
        return $this->getRecord()->display_label;
    }
}
