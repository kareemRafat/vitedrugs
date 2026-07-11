<?php

namespace App\Filament\Resources\SideEffects\Pages;

use App\Filament\Resources\SideEffects\SideEffectResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSideEffect extends ViewRecord
{
    protected static string $resource = SideEffectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
