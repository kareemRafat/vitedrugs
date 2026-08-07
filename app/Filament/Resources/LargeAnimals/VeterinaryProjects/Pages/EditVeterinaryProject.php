<?php

namespace App\Filament\Resources\LargeAnimals\VeterinaryProjects\Pages;

use App\Filament\Resources\LargeAnimals\VeterinaryProjects\VeterinaryProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVeterinaryProject extends EditRecord
{
    protected static string $resource = VeterinaryProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
