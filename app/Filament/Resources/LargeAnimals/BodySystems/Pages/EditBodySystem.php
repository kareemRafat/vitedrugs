<?php

namespace App\Filament\Resources\LargeAnimals\BodySystems\Pages;

use App\Filament\Resources\LargeAnimals\BodySystems\BodySystemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBodySystem extends EditRecord
{
    protected static string $resource = BodySystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
