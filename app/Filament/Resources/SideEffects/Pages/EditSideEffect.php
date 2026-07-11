<?php

namespace App\Filament\Resources\SideEffects\Pages;

use App\Filament\Resources\SideEffects\SideEffectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSideEffect extends EditRecord
{
    protected static string $resource = SideEffectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
