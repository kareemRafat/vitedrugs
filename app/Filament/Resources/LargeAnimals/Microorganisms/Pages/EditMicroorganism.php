<?php

namespace App\Filament\Resources\LargeAnimals\Microorganisms\Pages;

use App\Filament\Resources\LargeAnimals\Microorganisms\MicroorganismResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMicroorganism extends EditRecord
{
    protected static string $resource = MicroorganismResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
