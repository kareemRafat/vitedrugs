<?php

namespace App\Filament\Resources\Precautions\Pages;

use App\Filament\Resources\Precautions\PrecautionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrecaution extends EditRecord
{
    protected static string $resource = PrecautionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
